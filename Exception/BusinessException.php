<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Productive\Exception;

/**
 * Description of BusinessException
 *
 * @author calixto
 */
class BusinessException extends \Exception{
    public function __construct($message = "", $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    //put your code here
}
