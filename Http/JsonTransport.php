<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Productive\Http;

/**
 * Description of JsonTransport
 *
 * @author calixto
 */
class JsonTransport implements \JsonSerializable{
    protected $error = false;
    protected $nrError;
    protected $type;
    protected $message;
    protected $data;
    protected $extraData=[];
    
    public function setError($message = null){
        $this->error = true;
        if($message){
            $this->message = $message;
        }
        return $this;
    }
    
    public function setNrError($nrError){
        $this->nrError = $nrError;
        return $this;
    }
    
    public function setSuccess($message = null){
        $this->error = false;
        if($message){
            $this->message = $message;
        }
        return $this;
    }
    
    public function setType($type){
        $this->type = $type;
        return $this;
    }
    
    public function setMessage($message){
        $this->message = $message;
        return $this;
    }
    
    public function setData($data){
        $this->data = $data;
        return $this;
    }
    
    public function setExtraData($name, $extra){
        $this->extraData[$name] = $extra;
        return $this;
    }
    
    public function setException(\Exception $ex, $showExtraData = true){
        $this->setError($ex->getMessage());
        $this->setType(get_class($ex));
        $extraData = [
            'type'=> get_class($ex),
            'file'=> $ex->getFile(),
            'line' => $ex->getLine(),
            'message' => $ex->getMessage(),
            'trace' => $ex->getTrace()
        ];
        if($showExtraData){
            $this->setExtraData('exception', $extraData);
        }
        return $this;
    }
    
    public function send(){
        header("Content-type: application/json; charset=utf-8");
        echo json_encode($this);
        die;
    }
    
    //put your code here
    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
