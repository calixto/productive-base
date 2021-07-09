<?php

namespace Productive\Http;

class Csrf {
    
    protected $session;
    
    public function __construct(Session $session) {
        $this->session = $session;
    }
    
    public function verify() {
        switch(true){
            case Request::isGet() && (!Request::isAjax()):
            case Request::header('X-CSRF-TOKEN') == $this->session->get('X_CSRF_OLD_TOKEN'):
            case Request::isPost() && (Request::post('X-CSRF-TOKEN') == $this->session->get('X_CSRF_OLD_TOKEN')):
                unset($_POST['X-CSRF-TOKEN']);
                break;
            default :
                throw new \Productive\Exception\CsrfException('Falsificação de solicitação entre sites');
        }
    }
    
    public function register() {
        $this->session->set('X_CSRF_OLD_TOKEN', $this->get());
        $this->session->set('X_CSRF_TOKEN', substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 64));
    }
    
    public function get() {
        try{
            return $this->session->get('X_CSRF_TOKEN');
        } catch (\Exception $ex) {
            return false;
        }
    }

}
