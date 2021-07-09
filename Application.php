<?php

namespace Productive;

class Application {

    private static $version;
    private static $environment;
    /**
     * Controle em uso
     * @var Tier\Controller
     */
    private static $controller;
    private static $method;
    private static $folderController = 'Controller';
    private static $folderView = 'View';
    private static $mainTemplate = 'App/View/main.phtml';
    public static $ssl = false;

    public static function run() {
        try {
            if(Http\Request::isAjax()){
                set_exception_handler("\Productive\Application::showAjaxException");
            }else{
                set_exception_handler("\Productive\Application::showException");
            }
            set_error_handler("\Productive\Application::showError");
            if (self::$ssl && (!Http\Url::connectionSsl())) {
                throw new Exception\RequestException('Conexão insegura.');
            }
            self::makeDefinitions();
            self::createController();
            self::executeMethod();
        } catch (Exception $ex) {
            echo $ex;
        }
    }
    
    public static function setFolderController($folderName){
        self::$folderController = $folderName;
        return (new Application);
    }
    
    public static function getFolderController(){
        return self::$folderController;
    }
    
    public static function setFolderView($folderName){
        self::$folderView = $folderName;
        return (new Application());
    }
    
    public static function getFolderView(){
        return self::$folderView;
    }
    
    public static function loadDebug(){
        include_once 'debug.php';
        \errorsOn();
        return (new Application());
    }
    
    public static function setMainTemplate($mainTemplate){
        self::$mainTemplate = $mainTemplate;
        return (new Application());
    }
    
    public static function getMainTemplate(){
        return self::$mainTemplate;
    }
    
    protected static function getRouteToPath($tier = null, $separator = '\\'){
        $route = explode('/', Data\TypeString::dashesToUpperCamelCase(Http\Url::route()));
        self::$method = array_pop($route);
        $class = array_pop($route);
        if($tier){
            array_push($route, $tier);
        }
        array_push($route, $class);
        return implode($separator, array_map('ucFirst', $route));
    }

    public static function getRouteToClassPath($tier = null){
        return self::getRouteToPath($tier, '\\');
    }
    
    public static function getRouteToDirectoryPath($tier = null){
        return self::getRouteToPath($tier, DIRECTORY_SEPARATOR);
    }
    
    public static function getMethodName(){
        return self::$method;
    }

    protected static function createController() {
        $class = self::getRouteToClassPath(self::$folderController);
            (new Http\Session())->start();
            try{
                self::$controller = new $class();
            } catch (\Exception $ex) {
                throw new Exception\RequestException('Route error.');
            }
            if (!self::$controller instanceof Tier\Controller) {
                throw new Exception\RequestException('It is not a Controller.');
            }
    }

    protected static function executeMethod() {
        self::$controller->csrfVerify(self::$method);
        $reflection = new \ReflectionClass(self::$controller);
        $res = null;
        if(false !== self::$controller->init(self::$method)){
            switch (true) {
                case $reflection->hasMethod(self::$method . Http\Request::type()):
                    $res = self::$controller->{self::$method . Http\Request::type()}();
                    break;
                case $reflection->hasMethod(self::$method):
                    $res = self::$controller->{self::$method}();
                    break;
                default:
                    throw new Exception\RequestException('This method not exist.');
            }
        }
        self::$controller->finish($res);
        self::returnExecution($res);
    }
    
    protected static function returnExecution($res){
        switch (true) {
            case !$res:
                break;
            case $res instanceof Tier\View\Render\Html:
                self::$controller->csrfRegister();
            case $res instanceof Tier\View\Render:
                $res->header();
                $res->show();
                break;
            case $res instanceof Http\JsonTransport:
                $res->send();
            default:
                echo json_encode(['data' => $res]);
        }
    }

    public static function production() {
        return self::$environment === 'production';
    }

    public static function homologation() {
        return self::$environment === 'homologation';
    }

    public static function development() {
        return self::$environment === 'development';
    }

    public static function version() {
        return self::$version;
    }

    private static function makeDefinitions() {
        self::$environment = $_SERVER['ENVIRONMENT'];
        self::$version = $_SERVER['VERSION'];
        switch (true) {
            case self::development():
                ini_set('display_errors', 'On');
                break;
            case self::production():
            case self::homologation():
                ini_set('display_errors', 'Off');
                break;
        }
    }

    public static function showErrorAjax($errno, $errstr, $errfile, $errline){
        (new Http\JsonTransport())
                ->setError($errstr)
                ->setType($errno)
                ->setExtraData('file', $errfile)
                ->setExtraData('line', $errline)
                ->setExtraData('trace', debug_print_backtrace())
                ;
    }
    
    public static function showAjaxException(\Exception $ex){
        (new Http\JsonTransport())->setException($ex)->send();
    }
    
    public static function showError($errno, $errstr, $errfile, $errline){
        ob_start();
        debug_print_backtrace();
        self::printError(is_numeric($errno) ? 'Error' : $errno, $errstr, $errfile, $errline, ob_get_clean());
    }
    public static function showException(\Exception $ex){
        $errorType = (new \ReflectionClass($ex))->getName();
        $message = $ex->getMessage();
        $file = isset($ex->realFile) ? $ex->realFile : $ex->getFile();
        $line = isset($ex->realLine) ? $ex->realLine : $ex->getLine();
        $trace = $ex->getTraceAsString();
        self::printError($errorType, $message, $file, $line, $trace);
    }
    
    protected static function printError($errorType, $message, $file, $line, $trace){
        debugCss();
        echo <<<dbg
        <div style="font:#000">
        <h1 class="keyword">{$errorType}</h1>
        <div>Message: <strong>{$message}</strong></div>
        <div>In file: <span class="string"><b>{$file}</b></span></div>
        <div>on line: <span class="numero"><b>{$line}</b></span></div>
        <pre>{$trace}</pre>
        </div>
        
dbg;
    }

}
