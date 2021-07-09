<?php

namespace Productive\Tier;

use \Productive\Data\Operator;

class Model {

    private static $debug = false;
    protected static $viaBinds = false;
    protected $conn;

    public function __construct() {
        $this->conn = \Productive\Database\Connection::create();
    }

    public static function startDebug() {
        $args = func_get_args();
        $ar = debug_backtrace();
        echo '<link rel="stylesheet" href="' . \Productive\Http\Url::root() . '/debug.css" />';
        echo "<div class='debug'>Iniciando debug da model em:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
        self::$debug = true;
    }

    public static function stopDebug() {
        $args = func_get_args();
        $ar = debug_backtrace();
        echo '<link rel="stylesheet" href="' . \Productive\Http\Url::root() . '/debug.css" />';
        echo "<div class='debug'>Parando debug da model em:{$ar[0]['file']} na linha:{$ar[0]['line']}</div>";
        self::$debug = false;
    }

    protected function securityData($data, $field = null) {
        $data = str_replace("'", "''", $data);
        switch ($field['type']) {
            case 'date':
                if(strlen($data) > 10){
                    $data = (string) "str_to_date('{$data}', '%d/%m/%Y %H:%i:%s')";
                }else{
                    $data = (string) "str_to_date('{$data}', '%d/%m/%Y')";
                }
                break;
            case 'integer':
                $data = (integer) $data;
                break;
            case 'float':
                $data = (float) $data;
                break;
            case 'boolean':
                $data = (boolean) $data;
                break;
            case 'string':
            default:
                $data = (string) "'{$data}'";
        }
        return $data;
    }

    protected function query($sql) {
        if (self::$debug) {
            echo "<pre><font class='tipoPrimario'>";
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            echo "</font>";
            echo "<font class='string'>";
            echo $sql;
            echo "</font>";
            echo "</pre>";
        }
        return $this->conn->query($sql);
    }

    protected function queryBinds($sql, $binds = [], $fn = null) {
        $fn = $fn ? $fn : function($line) {
            return $line;
        };
        $statement = $this->conn->prepare($sql);
        $statement->execute($binds);
        while ($row = $statement->fetch(\PDO::FETCH_NUM, \PDO::FETCH_ORI_NEXT)) {
            $fn($row);
        }
    }

    /**
     * Executa a seleção de similares
     * @param \Productive\Data\Data $object
     * @param \Productive\Data\Paginator $page
     */
    public function select(\Productive\Data\Data $object, \Productive\Data\Paginator $page = null) {
        if (!$page) {
            $page = new \Productive\Data\Paginator(0);
        }
        $map = $object->describe();
        $fields = [];
        $order = [];
        foreach ($map['fields'] as $field) {
            switch ($field['type']) {
                case 'date':
                    $fields[] = sprintf("\n\tdate_format(%s,'%%d/%%m/%%Y') as \"%s\"", $field['dbName'], $field['property']);
                    break;
                default:
                    $fields[] = sprintf("\n\t%s as \"%s\"", $field['dbName'], $field['property']);
            }
            if(isset($field['order'])){
                $order[$field['order']['priority']] = sprintf('%s.%s %s',$map['table'],$field['dbName'],$field['order']['direction']);
            }
        }
        if (!count($order)) {
            $order[] = 1;
        }
        ksort($order);
        
        $sql = sprintf("select %s \nfrom \n\t%s %s\norder by \n\t%s", implode(', ', $fields), $map['table'], $this->where($object), implode(", \n\t", $order));
        if ($page->getPageSize() !== 0) {
            $sql = sprintf("select * from (%s) selecao limit %s offset %s", $sql, $page->getPageSize(), $page->getFirstLine() - 1);
        }
        $reflection = new \ReflectionClass($object);
        if ($reflection->hasMethod('defaultCollection')) {
            $class = (new \ReflectionClass($object->defaultCollection()))->getName();
        } else {
            $class = \Productive\Data\Collection::class;
        }
        return new $class($this->query($sql)->fetchAll(\PDO::FETCH_CLASS, $reflection->getName()));
    }

    protected function where(\Productive\Data\Data $object, $key = false) {
        $map = $object->describe();
        $filter = function($fieldOpt, $value)use($map) {
            if ($value instanceof Operator) {
                switch ($value->getOperator()) {
                    case Operator::isNull:
                        return sprintf("\n\t%s is null %s", $fieldOpt['dbName'], $this->securityData($value->getValue(), $fieldOpt), $value->getRestriction());
                    case Operator::isNotNull:
                        return sprintf("\n\t%s is not null %s", $fieldOpt['dbName'], $this->securityData($value->getValue(), $fieldOpt), $value->getRestriction());
                    case Operator::equal:
                        return sprintf("\n\t%s=%s %s", $fieldOpt['dbName'], $this->securityData($value->getValue(), $fieldOpt), $value->getRestriction());
                    case Operator::different:
                        return sprintf("\n\t%s<>%s %s", $fieldOpt['dbName'], $this->securityData($value->getValue(), $fieldOpt), $value->getRestriction());
                    case Operator::initLike:
                        return sprintf("\n\tupper(%s) like upper(%s) %s", $fieldOpt['dbName'], $this->securityData($value->getValue() . '%', $fieldOpt), $value->getRestriction());
                    case Operator::endLike:
                        return sprintf("\n\tupper(%s) like upper(%s) %s", $fieldOpt['dbName'], $this->securityData('%' . $value->getValue(), $fieldOpt), $value->getRestriction());
                    case Operator::like:
                        return sprintf("\n\tupper(%s) like upper(%s) %s", $fieldOpt['dbName'], $this->securityData('%' . $value->getValue() . '%', $fieldOpt), $value->getRestriction());
                    case Operator::biggerThen:
                        return sprintf("\n\t%s > %s %s", $fieldOpt['dbName'], $this->securityData($value->getValue(), $fieldOpt), $value->getRestriction());
                    case Operator::lessThen:
                        return sprintf("\n\t%s < %s %s", $fieldOpt['dbName'], $this->securityData($value->getValue(), $fieldOpt), $value->getRestriction());
                    case Operator::biggerOrEqual:
                        return sprintf("\n\t%s >= %s %s", $fieldOpt['dbName'], $this->securityData($value->getValue(), $fieldOpt), $value->getRestriction());
                    case Operator::lessOrEqual:
                        return sprintf("\n\t%s <= %s %s", $fieldOpt['dbName'], $this->securityData($value->getValue(), $fieldOpt), $value->getRestriction());
                    case Operator::between:
                        return sprintf("\n\t(%s between %s and %s ) %s", $fieldOpt['dbName'], $this->securityData($value->getValue()['value1'], $fieldOpt), $this->securityData($value->getValue()['value2'], $fieldOpt), $value->getRestriction());
                    case Operator::domain:
                        return sprintf("\n\t%s in (%s) %s", $fieldOpt['dbName'], implode(', ', array_map([Model::class, 'securityData'], $value->getValue())), $value->getRestriction());
                }
            } else {
                return sprintf("\n\t%s=%s and", $fieldOpt['dbName'], $this->securityData($value, $fieldOpt));
            }
        };
        if ($key) {
            $primaryKeys = [];
            foreach ($object->toArray() as $field => $value) {
                if (isset($map['fields'][$field]['primaryKey']) && $map['fields'][$field]['primaryKey'] == 'true') {
                    $primaryKeys[] = $field;
                }
            }
            if (!count($primaryKeys)) {
                throw new Exception('Nenhum campo chave possui valor para pesquisar.');
            }
            $where = [];
            foreach ($primaryKeys as $fieldKey) {
                $where[] = $filter($map['fields'][$fieldKey], $object->{$fieldKey});
            }
            return sprintf("\nwhere %s", implode(" and\n\t", $where));
        } else {
            $where = [];
            foreach ($object->toArray() as $field => $value) {
                if ($value) {
                    $where[] = $filter($map['fields'][$field], $object->{$field});
                }
            }
            if (count($where)) {
                $keys = array_keys($where);
                $lastKey = end($keys);
                $where[$lastKey] = substr($where[$lastKey], 0, -4);
                return sprintf("\nwhere %s", implode("", $where));
            } else {
                return '';
            }
        }
    }

    /**
     * Insere o objeto no banco de dados
     * @param \Productive\Data\Data $object
     * @return \Productive\Data\Data
     */
    public function insert(\Productive\Data\Data $object) {
        $map = $object->describe();
        $fields = [];
        $values = [];
        foreach ($object->toArray() as $field => $value) {
            if ($value !== null) {
                $fields[':' . $field] = $map['fields'][$field]['dbName'];
                $values[':' . $field] = self::$viaBinds ? $value : $this->securityData($value, $map['fields'][$field]);
            }
        }
        if (self::$viaBinds) {
            $sql = sprintf("insert into %s (\n\t%s) values (\n\t%s)", $map['table'], implode(",\n\t", $fields), implode(",\n\t", array_keys($values)));
            $stm = $this->conn->prepare($sql);
            foreach ($values as $key => $val) {
                switch ($map['fields'][$field]['type']) {
                    case 'integer':
                        $stm->bindParam($key, (integer) $val, \PDO::PARAM_INT);
                    case 'boolean':
                        $stm->bindParam($key, (boolean) $val, \PDO::PARAM_BOOL);
                    case 'date':
                    case 'float':
                    case 'string':
                        $stm->bindParam($key, (string) $val, \PDO::PARAM_STR);
                }
            }
            $stm->execute();
        } else {
            $sql = sprintf("insert into %s (\n\t%s) values (\n\t%s)", $map['table'], implode(",\n\t", $fields), implode(",\n\t", $values));
            $this->query($sql);
            $this->setLastIsertedId($object, $map);
        }
        return $object;
    }

    public function setLastIsertedId(\Productive\Data\Data $object, $map = null) {
        $id = $this->conn->lastInsertId();
        if (!$map) {
            $map = $object->describe();
        }
        foreach ($object->toArray() as $field => $value) {
            if (isset($map['primaryKeys'][$field])) {
                $object->{$field} = $id;
                break;
            }
        }
    }

    /**
     * Atualiza o objeto no banco de dados
     * @param \Productive\Data\Data $object
     * @return \Productive\Data\Data
     */
    public function update(\Productive\Data\Data $object) {
        $map = $object->describe();
        $fields = [];
        $values = [];
        foreach ($object->toArray() as $field => $value) {
            if ($value !== null) {
                $fields[':' . $field] = $map['fields'][$field]['dbName'];
                $values[':' . $field] = $value;
            }
        }
        if (self::$viaBinds) {
            
        } else {
            $sets = [];
            $where = [];
            foreach ($values as $field => $value) {
                $field = substr($field, 1);
                $sets[] = sprintf("%s=%s", $map['fields'][$field]['dbName'], $this->securityData($value, $map['fields'][$field]));
            }
            foreach ($map['primaryKeys'] as $fieldKey => $value) {
                $where[] = sprintf("%s=%s", $map['fields'][$fieldKey]['dbName'], $this->securityData($object->{$fieldKey}, $map['fields'][$fieldKey]));
            }
            $sql = sprintf("update %s set \n\t%s where %s", $map['table'], implode(",\n\t", $sets), implode(",\n\t", $where));
            $this->query($sql);
        }
        return $object;
    }

    /**
     * Remove o objeto do banco de dados
     * @param \Productive\Data\Data $object
     * @return \Productive\Data\Data
     */
    public function delete(\Productive\Data\Data $object) {
        $map = $object->describe();
        $fields = [];
        $values = [];
        foreach ($object->toArray() as $field => $value) {
            if ($value !== null) {
                $fields[':' . $field] = $map['fields'][$field]['dbName'];
                $values[':' . $field] = $value;
            }
        }
        if (self::$viaBinds) {
            
        } else {
            $sets = [];
            $where = [];
            foreach ($values as $field => $value) {
                $field = substr($field, 1);
                $sets[] = sprintf("%s=%s", $map['fields'][$field]['dbName'], $this->securityData($value, $map['fields'][$field]));
            }
            foreach ($map['primaryKeys'] as $fieldKey => $value) {
                $where[] = sprintf("%s=%s", $map['fields'][$fieldKey]['dbName'], $this->securityData($object->{$fieldKey}, $map['fields'][$fieldKey]));
            }
            $sql = sprintf("delete from %s where %s", $map['table'], implode(",\n\t", $where));
            $this->query($sql);
        }
        return $object;
    }

}
