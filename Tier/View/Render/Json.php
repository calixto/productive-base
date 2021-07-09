<?php

namespace Productive\Tier\View\Render;

class Json implements \Productive\Tier\View\Render{

    private $__vars = [];

    public function getVar($name) {
        return issset($this->__vars[$name]) ? $this->__vars[$name] : '';
    }

    public function getVars() {
        return $this->__vars;
    }

    public function setVar($name, $value) {
        $this->__vars[$name = $value];
    }

    public function setVars($vars) {
        $this->__vars = $vars;
    }
    
    public function fetch(){
        return json_encode($this->__vars);
    }

    public function show() {
        echo json_encode($this->__vars);
    }

    public function header() {
        header("Content-type:text/json; charset=utf-8");
    }

}

