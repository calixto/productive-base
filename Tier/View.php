<?php

namespace Productive\Tier;

abstract class View {

    /**
     *
     * @var View\Render
     */
    protected $render;

    public function __construct() {
        $this->render = new View\Render\Html();
    }

    public function setRender(View\Render $render) {
        $this->render = $render;
    }

    public function show() {
        $this->render->show();
    }

    public function __set($name, $value) {
        $this->render->setVar($name, $value);
    }

    public function __get($name) {
        return $this->render->getVar($name);
    }

}
