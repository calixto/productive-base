<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Productive\Data;

/**
 * Description of Data
 *
 * @author calixto
 */
abstract class Data implements \JsonSerializable {

    /**
     * Retorna um array do objeto
     * @return array
     */
    public function toArray() {
        return get_object_vars($this);
    }

    /**
     * Prepara e retorna o objeto para envio à camada de visão
     * @return $this
     */
    public function sendToView() {
        return $this;
    }

    /**
     * Preenche o objeto com um array indexado com os nomes dos atributos
     * @param array $array
     */
    public function loadFromView(array $array) {
        $ref = new \ReflectionClass($this);
        foreach ($array as $propertyName => $value) {
            if ($ref->hasProperty($propertyName)) {
                $this->{$propertyName} = $value;
            }
        }
        return $this;
    }

    /**
     * Preenche o objeto com um array indexado com os nomes dos campos da tabela
     * @param type $array
     */
    public function loadFromModel($array) {
        $ref = new \ReflectionClass($this);
        foreach ($ref->getProperties() as $property) {
            foreach (explode("\n", $property->getDocComment()) as $strConfig) {
                if (preg_match('/\@name\s+(.*)/', trim($strConfig), $match)) {
                    if (isset($array[$match[1]])) {
                        $this->{$property->getName()} = $array[$match[1]];
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Prepara e retorna o objeto para envio à camada de modelo
     * @return $this
     */
    public function sendToModel() {
        $map = $this->describe();
        foreach ($map['fields'] as $field => $props) {
            switch ($props['type']) {
                case 'date':
                    $this->{$field} = (string) $this->{$field};
                    break;
                case 'float':
                    $this->{$field} = empty($this->{$field}) ? null : (float) $this->{$field};
                    break;
                case 'integer':
                    $this->{$field} = empty($this->{$field}) ? null : (integer) $this->{$field};
                    break;
                case 'string':
                    $this->{$field} = (string) $this->{$field};
                    break;
            }
        }
        return $this;
    }

    /**
     * Retorna o mapeamento do objeto de dados
     * @return array
     */
    public function describe() {
        $res = [
            'table' => '',
            'fields' => [],
            'primaryKeys' => []
        ];
        $ref = new \ReflectionClass($this);
        $comment = $ref->getDocComment();
        preg_match('/\@table\s+(\w+)/', trim($comment), $match);
        $res['table'] = $match[1];
        $needType = false;
        foreach ($ref->getProperties() as $property) {
            if ($property->isPublic()) {
                $res['fields'][$property->getName()]['property'] = $property->getName();
                foreach (explode("\n", $property->getDocComment()) as $strConfig) {
                    if (preg_match('/\@(dbName|size|type|primaryKey|required|order)\s+(.*)/', trim($strConfig), $match)) {
                        $res['fields'][$property->getName()][$match[1]] = isset($match[2]) ? $match[2] : null;
                        if ($match[1] == 'primaryKey') {
                            $res['primaryKeys'][$property->getName()] = isset($match[2]) ? $match[2] : null;
                        }
                        if ($match[1] == 'order') {
                            if (isset($match[2]) && preg_match('/(\d*)(\s+desc|\s+asc|)/i', $match[2], $order)) {
                                $res['fields'][$property->getName()][$match[1]] = [
                                    'direction' => $order[2] ? $order[2] : ' asc',
                                    'priority' => $order[1]
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $res;
    }

    /**
     * Método de validação para string
     * @param \ReflectionProperty $property
     * @throws \Exception
     */
    protected function validateTypeString(\ReflectionProperty $property, $title) {
        $title = $title ? $title : $property->getName();
        if ($property->getValue($this) && !is_string($property->getValue($this))) {
            throw new \Exception("O campo {$title} não é uma string.");
        }
    }

    /**
     * Método de validação para um domínio de dados
     * @param \ReflectionProperty $property
     * @param type $options
     * @throws \Exception
     */
    protected function validateDomain(\ReflectionProperty $property, $options, $title) {
        $title = $title ? $title : $property->getName();
        if ($property->getValue($this)) {
            $arOptions = explode(',', str_replace('[', '', str_replace(']', '', $options)));
            $arOptions = array_map(function($item) {
                if (preg_match("/^'(.*)'$/", $item, $match)) {
                    return $match[1];
                }
                return $item;
            }, $arOptions);
            if (!in_array($property->getValue($this), $arOptions)) {
                throw new \Exception("O campo {$title} recebeu o valor '{$property->getValue($this)}' para um domínio {$options}.");
            }
        }
    }

    /**
     * Método de validação para um dipo data
     * @param \ReflectionProperty $property
     * @throws \Exception
     */
    protected function validateTypeDate(\ReflectionProperty $property, $title) {
        $title = $title ? $title : $property->getName();
        if ($property->getValue($this)) {
            if (!is_string($property->getValue($this))) {
                throw new \Exception("O campo {$title} não tem um formato de string para data.");
            }
            if (!in_array(strlen($property->getValue($this)), [10, 16, 19])) {
                throw new \Exception("O campo {$title} não tem um formato de data válido aaaa-mm-dd [[hh:ii]:ss].");
            }
            $day = substr($property->getValue($this), 0, 2);
            $month = substr($property->getValue($this), 3, 2);
            $year = substr($property->getValue($this), 6, 4);
            if (!checkdate($month, $day, $year)) {
                throw new \Exception("O campo {$title} não é uma data válida.");
            }
        }
    }

    /**
     * Método de validação para um integer
     * @param \ReflectionProperty $property
     * @throws \Exception
     */
    protected function validateTypeInteger(\ReflectionProperty $property, $title) {
        $title = $title ? $title : $property->getName();
        if ($property->getValue($this) && !is_integer($property->getValue($this))) {
            throw new \Exception("O campo {$title} não é um número inteiro.");
        }
    }

    /**
     * Método de validação para um float
     * @param \ReflectionProperty $property
     * @throws \Exception
     */
    protected function validateTypeFloat(\ReflectionProperty $property, $title) {
        $title = $title ? $title : $property->getName();
        if ($property->getValue($this) && !is_float($property->getValue($this))) {
            throw new \Exception("O campo {$title} não é um número decimal.");
        }
    }

    /**
     * Método de validação para um boolean
     * @param \ReflectionProperty $property
     * @throws \Exception
     */
    protected function validateTypeBoolean(\ReflectionProperty $property, $title) {
        $title = $title ? $title : $property->getName();
        if (!is_bool($property->getValue($this))) {
            throw new \Exception("O campo {$title} não é um booleano.");
        }
    }

    /**
     * Método de validação para o tamanho do campo
     * @param \ReflectionProperty $property
     * @param type $size
     * @throws \Exception
     */
    protected function validateSize(\ReflectionProperty $property, $size, $title) {
        $title = $title ? $title : $property->getName();
        if (strlen($property->getValue($this)) > $size) {
            throw new \Exception("O campo {$title} excede o tamanho máximo de {$size} caracteres.");
        }
    }

    /**
     * Método de validação de obrigatoriedade de preenchimento do campo
     * @param \ReflectionProperty $property
     * @param boolean $flag
     * @param string $title
     * @throws \Exception
     */
    protected function validateRequired(\ReflectionProperty $property, $flag, $title) {
        $title = $title ? $title : $property->getName();
        $val = trim($property->getValue($this));
        if ($flag == 'true' && ($val === null || $val === '')) {
            throw new \Exception("O campo {$title} é obrigatório.");
        }
    }

    /**
     * Método de mapeamento e validação de uma propriedade da classe
     * @param \ReflectionProperty $property
     * @throws \Reflection\Exception
     */
    protected function validateProperty(\ReflectionProperty $property) {
        if ($property->getValue($this) instanceof Data) {
            $property->getValue($this)->validate();
        } else {
            $reflection = new \ReflectionClass($this);
            $comment = $property->getDocComment();
            $title = '';
            if (preg_match('/\@(title)\s+(.*)/', $comment, $match)) {
                $title = $match[2];
            }
            foreach (explode("\n", $comment) as $strConfig) {
                if (preg_match('/\@(\w+)\s+(.*)/', trim($strConfig), $match)) {
                    try {
                        switch ($match[1]) {
                            case 'var':
                            case 'title':
                            case 'description':
                            case 'dbName':
                            case 'primaryKey':
                            case 'order':
                                break;
                            case 'type':
                                $method = $reflection->getMethod("validate{$match[1]}{$match[2]}");
                                $method->setAccessible(true);
                                $method->invoke($this, $property, $title);
                                break;
                            default:
                                $method = $reflection->getMethod("validate{$match[1]}");
                                $method->setAccessible(true);
                                $method->invoke($this, $property, $match[2], $title);
                        }
                    } catch (\Reflection\Exception $ex) {
                        $reflection = new \ReflectionClass($this);
                        echo $reflection->getName();
                        echo '::$';
                        echo $property->getName();
                        throw $ex;
                    }
                }
            }
        }
    }

    /**
     * Método de validação da classe
     * @param string $operation
     * @return $this
     */
    public function validate($operation = null) {
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties() as $property) {
            $this->validateProperty($property);
        }
        return $this;
    }

    /**
     * 
     * @param string $operation
     */
    public function validateKeys($operation = null) {
        $map = $this->describe();
        $reflection = new \ReflectionClass($this);
        foreach ($map['primaryKeys'] as $propertyName => $value) {
            $property = $reflection->getProperty($propertyName);
            $this->validateProperty($property);
        }
        return $this;
    }

    /**
     * Mostra o objeto
     */
    public function show() {
        x($this);
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
