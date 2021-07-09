<?php

namespace Productive\Tier;

abstract class Controller {

    /**
     * Render default html
     * @var View\Render\Html
     */
    protected $view;

    /**
     * Session manager
     * @var \Productive\Http\Session
     */
    private $sessionSystem;

    /**
     * Session manager
     * @var \Productive\Http\Session
     */
    private $sessionProgram;

    /**
     * Construct
     */
    public final function __construct() {
        $this->sessionSystem = new \Productive\Http\Session('___system');
        $this->sessionProgram = new \Productive\Http\Session('___program', (new \ReflectionClass($this))->getName());
        $this->view = new View\Render\Html();
        $this->view->setMainTemplate(\Productive\Application::getMainTemplate());
        $this->view->setConfigDefaultJsVars([
            'URL_ROOT' => \Productive\Http\Url::root(),
            'URL_ROUTE' => \Productive\Http\Url::route(),
            'X_CSRF_TOKEN' => (new \Productive\Http\Csrf($this->sessionSystem()))->get(),
            'EXTRA' => $this->extraConfigsJs(),
        ]);
    }
    
    protected function extraConfigsJs(){
        return [];
    }
    
    /**
     * Verify an CSRF attack
     * @param type $method
     */
    public function csrfVerify($method = null){
        (new \Productive\Http\Csrf($this->sessionSystem()))->verify();
    }
    
    /**
     * Register a Token CSRF 
     */
    public function csrfRegister(){
        (new \Productive\Http\Csrf($this->sessionSystem()))->register();
    }

    /**
     * Method called before requested method
     * @param string $method
     */
    public function init($method) {
        
    }

    /**
     * Method called afeter requested method
     * @param mixed $result
     */
    public function finish($result) {
        
    }

    /**
     * Return a $_GET value
     * @param string $var
     * @return mixed
     */
    protected function get($var = null) {
        if ($var) {
            return isset($_GET[$var]) ? $_GET[$var] : false;
        }
        return $_GET;
    }

    /**
     * Return a $_POST value
     * @param string $var
     * @return mixed
     */
    protected function post($var = null) {
        if ($var) {
            return isset($_POST[$var]) ? $_POST[$var] : false;
        }
        return $_POST;
    }

    /**
     * Return a system session
     * @return \Productive\Http\Session
     */
    protected final function sessionSystem() {
        return $this->sessionSystem;
    }

    /**
     * Return a program session
     * @return \Productive\Http\Session
     */
    protected final function sessionProgram() {
        return $this->sessionProgram;
    }

}
