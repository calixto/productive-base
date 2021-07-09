<?php

namespace Productive\Http;

class Request {
    const typePost = 'Post';
    const typeGet = 'Get';

    public static function type() {
        return ucfirst(strtolower($_SERVER['REQUEST_METHOD']));
    }

    /**
     * Return true if a XHR request
     * @return boolean
     */
    public static function isAjax() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
    
    /**
     * Return true if a POST request
     * @return boolean
     */
    public static function isPost() {
        return self::type() == self::typePost;
    }
    
    /**
     * Return true if a GET request
     * @return boolean
     */
    public static function isGet() {
        return self::type() == self::typeGet;
    }
    
    /**
     * Return a $_GET value
     * @param string $var
     * @return mixed
     */
    public static function get($var = null) {
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
    public static function post($var = null) {
        if ($var) {
            return isset($_POST[$var]) ? $_POST[$var] : false;
        }
        return $_POST;
    }

    /**
     * Return a HEADER of request
     * @param string $var
     * @return string
     */
    public static function header($var = null) {
        if ($var) {
            foreach (getallheaders() as $name => $value) {
                if ($var == $name) {
                    return $value;
                }
            }
            return null;
        } else {
            $res = [];
            foreach (getallheaders() as $name => $value) {
                $res[$name] = $value;
            }
            return $res;
        }
    }

}
