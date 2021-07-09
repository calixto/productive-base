<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Productive\Data;

/**
 * Description of Operator
 *
 * @author calixto
 */
class Operator {

    /**
     * Operador de 'and'
     */
    const restrictionAnd = ' and ';

    /**
     * Operador de 'or'
     */
    const restrictionOr = '  or ';

    /**
     * Operador de '>'
     */
    const biggerThen = '>';

    /**
     * Operador de '>='
     */
    const biggerOrEqual = '>=';

    /**
     * Operador de '<'
     */
    const lessThen = '<';

    /**
     * Operador de '<='
     */
    const lessOrEqual = '<=';

    /**
     * Operador de 'not null'
     */
    const isNotNull = 'not null';

    /**
     * Operador de 'null'
     */
    const isNull = '';

    /**
     * Operador de 'and'
     */
    const equal = '=';

    /**
     * Operador de '<>'
     */
    const different = '<>';

    /**
     * Operador de 'like %text%'
     */
    const like = '%text%';

    /**
     * Operador de 'like text%'
     */
    const initLike = 'text%';

    /**
     * Operador de 'like %text'
     */
    const endLike = '%text';

    /**
     * Operador de 'in (1,2,3...)'
     */
    const domain = '1 ou 2 ou 3...';

    /**
     * Operador de 'between'
     */
    const between = 'values between {value1} e {value2}';

    /**
     * Operador de 'busca gulosa'
     */
    const generic = '%aáãàä%';

    /**
     * Operador a ser passado
     * @var string
     */
    protected $operator;

    /**
     * Valor a ser comparado
     * @var mixed
     */
    protected $value;

    /**
     * Restrição utilizada após o operator
     * @var string
     */
    protected $restriction;

    /**
     * Retorna o operador utilizado
     * @return string
     */
    public function getOperator() {
        return $this->operator;
    }

    /**
     * Retorna o valor do operador
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Retorna a restrição do operador
     * @return string
     */
    public function getRestriction() {
        return $this->restriction;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function generic($value, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::generic;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function between($value1, $value2, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::between;
        $operator->restriction = $restriction;
        $operator->value = array('value1' => $value1, 'value2' => $value2);
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function domain($value, $restriction = operator::restrictionAnd) {
        if (!is_array($value)) {
            throw new Exception('Não foi passado um array para o operator de domínio');
        }
        $operator = new operator();
        $operator->operator = operator::domain;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function endLike($value, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::endLike;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function initLike($value, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::initLike;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function like($value, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::like;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function different($value, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::different;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function equal($value, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::equal;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param string $restriction
     * @return operator
     */
    public static function isNotNull($restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::isNotNull;
        $operator->restriction = $restriction;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param string $restriction
     * @return operator
     */
    public static function isNull($restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::isNull;
        $operator->restriction = $restriction;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function lessOrEqual($value, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::lessOrEqual;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function lessThen($value, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::lessThen;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function biggerOrEqual($value, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::biggerOrEqual;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de configuração de um operator
     * @param mixed $value
     * @param string $restriction
     * @return operator
     */
    public static function biggerThen($value, $restriction = operator::restrictionAnd) {
        $operator = new operator();
        $operator->operator = operator::biggerThen;
        $operator->restriction = $restriction;
        $operator->value = $value;
        return $operator;
    }

    /**
     * Método de visualização like string
     * @return string
     */
    public function __toString() {
        return "{$this->value}";
    }

}

?>