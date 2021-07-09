<?php

namespace Productive\Tier\View\Render;

class Html implements \Productive\Tier\View\Render {

    protected $__css = [];
    protected $__scripts = [];
    protected $__vars = [];
    protected $__jsVars = [];
    protected $__defaultVars = [];
    protected $__defaultJsVars = [];
    protected $__mainTemplate = '';
    protected $__template = '';

    public function getVar($name) {
        return issset($this->__vars[$name]) ? $this->__vars[$name] : '';
    }

    public function getVars() {
        return $this->__vars;
    }
    
    public function addCss($path) {
        $this->__css[] = $path;
        return $this;
    }
    
    public function addScriptJs($path) {
        $this->__scripts[] = $path;
        return $this;
    }

    public function setJsVar($name, $value) {
        $this->__jsVars[$name] = $value;
        return $this;
    }

    public function setJsVars($vars) {
        $this->__jsVars = $vars;
        return $this;
    }
    
    public function setVar($name, $value) {
        $this->__vars[$name] = $value;
        return $this;
    }

    public function setVars($vars) {
        $this->__vars = $vars;
        return $this;
    }

    public function setConfigDefaultJsVars($vars) {
        $this->__defaultJsVars = $vars;
        return $this;
    }
    
    public function setConfigDefaultVars($vars) {
        $this->__defaultVars = $vars;
        return $this;
    }
    
    public function setMainTemplate($template){
        $this->__mainTemplate = $template;
        return $this;
    }
    
    public function getMainTemplate(){
        return $this->__mainTemplate;
    }
    
    public function setTemplate($template){
        $this->__template = $template;
        return $this;
    }
    
    public function getTemplate(){
        return $this->__template;
    }
    
    public function fetch(){
        ob_start();
        $this->show();
        return ob_get_clean();
    }
    
    protected function templateDefault() {
        return
                \Productive\Application::getRouteToDirectoryPath(\Productive\Application::getFolderView()) .
                DIRECTORY_SEPARATOR .
                \Productive\Application::getMethodName() . '.phtml';
    }
    
    protected function scriptDefault() {
        $path = 'public/js/'.
                \Productive\Application::getRouteToDirectoryPath().'/'.
                \Productive\Application::getMethodName().'.js';
        return is_file(ROOT_PATH.$path) ? $path : '';
    }
    
    protected function cssDefault() {
        $path = 'public/css/'.
                \Productive\Application::getRouteToDirectoryPath().'/'.
                \Productive\Application::getMethodName().'.css';
        return is_file(ROOT_PATH.$path) ? $path : '';
    }

    public function show() {
        extract($this->__vars);
        extract($this->__defaultVars);
        ${' JS'} = json_encode($this->__jsVars + $this->__defaultJsVars);
        if(!$this->__template){
            $this->addScriptJs($this->scriptDefault());
            $this->addCss($this->cssDefault());
            $this->__template = $this->templateDefault();
            if(!is_file(APP_PATH.$this->__template)){
                throw new \Exception("Template [{$this->__template}] not found.");
            }
        }
        if($this->__mainTemplate && is_file(ROOT_PATH.$this->__mainTemplate)){
            ob_start();
            include_once APP_PATH.$this->__template;
            $contentPage = ob_get_clean();
            include_once ROOT_PATH.$this->__mainTemplate;
        }else{
            include_once APP_PATH.$this->__template;
        }
    }

    
    public function url($path = null){
        if(preg_match('/^http(s|)\:\/\//', $path)){
            return $path;
        }else{
            return \Productive\Http\Url::root().$path;
        }
    }
    
    public function header() {
        header("Content-type:text/html; charset=utf-8");
    }

}
