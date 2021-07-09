<?php

namespace Productive\Http;

class Session {

    const name = '___infra';
    protected $systemScope = '___system';
    protected $programScope = '___program';
    protected $program;
    
    public function __construct($scope = '___system', $program = 'NULL') {
        $this->scope = $scope;
        $this->program = $program;
    }

    public function start() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function stop() {
        unset($_SESSION[self::name]);
    }

    public function has($var) {
        return isset($_SESSION[self::name][$this->scope][$this->program][$var]);
    }

    public function get($var) {
        if ($this->has($var)) {
            return $_SESSION[self::name][$this->scope][$this->program][$var];
        }else{
            throw new \Productive\Exception\SessionException("Variável {$var} inexistente na sessão.");
        }
    }

    public function set($var, $value = null) {
        return $_SESSION[self::name][$this->scope][$this->program][$var] = $value;
    }
}
