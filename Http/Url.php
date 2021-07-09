<?php

namespace Productive\Http;

class Url {

    public static function connectionSsl() {
        switch (true) {
            case isset($_SERVER['HTTPS']) && in_array($_SERVER['HTTPS'], array('on', '1')):
            case isset($_SERVER['SERVER_PORT']) && in_array($_SERVER['SERVER_PORT'], [443]):
                return true;
            default:
                return false;
        }
    }

    public static function protocol() {
        return self::connectionSsl() ? 'https://' : 'http://';
    }

    public static function root() {
        //$host = str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']);
        return self::protocol() . $_SERVER['HTTP_HOST'] . '/';
    }

    protected static function request() {
        return str_replace(self::root(), '', self::protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    public static function route() {
        $rota = self::request();
        if (preg_match('/(.*)(\/\?.*$|\?.*$)/', self::request(), $match)) {
            $rota = $match[1];
            // print_r($match);
        }
        if (preg_match('/(.*)\/$/', $rota, $match)) {
            $rota = $match[1];
        }
        return $rota;
    }

}
