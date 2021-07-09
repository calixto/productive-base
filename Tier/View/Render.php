<?php

namespace Productive\Tier\View;

interface Render {
    public function header();
    public function setVars($vars);
    public function getVars();
    public function setVar($name,$value);
    public function getVar($name);
    public function fetch();
    public function show();
}
