<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Productive\Data;

/**
 * Description of Collection
 *
 * @author calixto
 */
class Collection implements \ArrayAccess, \Iterator, \JsonSerializable {

    private $items = [];
    private $results = [];
    protected $type = Data::class;

    public final function __construct(array $array = null) {
        if ($array) {
            foreach ($array as $key => $value) {
                $this->offsetSet($key, $value);
                unset($array[$key]);
            }
        }
    }

    public function offsetUnset($index) {
        unset($this->items[index]);
    }

    /**
     * Add item on Collection
     * @param type $index
     * @param \Productive\Data\Data $value
     * @return $this
     * @throws \Exception
     */
    public function offsetSet($index, $value) {
        try {
            if (!is_object($value)) {
                throw new \Exception();
            }
            $refl = new \ReflectionClass($this->type);
            if ($refl->isInstance($value)) {
                if($index){
                    $this->items[$index] = $value;
                }else{
                    $this->items[] = $value;
                }
                return $this;
            }
        } catch (\Exception $exc) {
            $error = debug_backtrace();
            if ($error[1]['function'] == '__construct' && $error[1]['class'] == 'Productive\Data\Collection') {
                $error = $error[1];
            } else {
                $error = $error[0];
            }
            $name = (new \ReflectionClass($this))->getName();
            $throw = new \Productive\Exception\DataException("A coleção [{$name}] somente aceita o tipo de dado [{$this->type}]");
            $throw->realLine = $error['line'];
            $throw->realFile = $error['file'];
            throw $throw;
        }
    }

    /**
     * Get an item on Collection
     * @param string $index
     * @return Data
     */
    public function offsetGet($index) {
        return $this->items[$index];
    }

    public function offsetExists($index) {
        return isset($this->items[$index]);
    }

    public function rewind() {
        reset($this->items);
    }

    public function current() {
        return current($this->items);
    }

    public function key() {
        return key($this->items);
    }

    public function next() {
        return next($this->items);
    }

    public function valid() {
        $key = key($this->items);
        return ($key !== NULL && $key !== FALSE);
    }

    public function subCollection(array $filters) {
        $class = (new \ReflectionClass($this))->getName();
        $collection = new $class();
        foreach ($this as $idx => $object) {
            $match = true;
            foreach ($filters as $field => $filter) {
                if ($object->{$field} != $filter) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $collection[$idx] = $object;
            }
        }
        return $collection;
    }
    
    public function clear() {
        $this->items = [];
    }

    public function count() {
        return count($this->items);
    }

    public function first() {
        $this->rewind();
        return $this->current();
    }

    public function last() {
        end($this->items);
        return $this->current();
    }
    
    public function getResults(){
        return $this->results;
    }

    public function __call($name, $arguments) {
        $this->results = [];
        if ($this->count()) {
            $refl = new \ReflectionClass($this->first());
            if($refl->hasMethod($name)){
                $method = $refl->getMethod($name);
                foreach($this as $idx => $object){
                    $this->results[$idx] = $method->invokeArgs($object, $arguments);
                }
            }else{
                $error = debug_backtrace()[1];
                $ex = new \Productive\Exception\DataException('Método inexistente '.$name);
                $ex->realFile = $error['file'];
                $ex->realLine = $error['line'];
                throw $ex;
            }
        }
        return $this;
    }
    
    public static function transpose(array $array){
        $res = [];
        if(count($array)){
            $titles = array_keys($array);
            foreach($array[$titles[0]] as $idx => $i){
                foreach($titles as $title){
                    if(is_object($array[$title])){
                        $res[$idx][$title] = $array[$title]->{$idx};
                    }else{
                        $res[$idx][$title] = $array[$title][$idx];
                    }
                }
            }
        }
        return $res;
    } 
    
    public function getCloneAndIndexBy($propertyName){
        $class = (new \ReflectionClass($this))->getName();
        $clone = new $class();
        if ($this->count()) {
            $refl = new \ReflectionClass($this->first());
            if($refl->hasProperty($propertyName)){
                foreach($this as $idx => $object){
                    $clone[$object->{$propertyName}] = $object;
                }
            }else{
                $error = debug_backtrace()[1];
                $ex = new \Productive\Exception\DataException('Propriedade inexistente '.$propertyName);
                $ex->realFile = $error['file'];
                $ex->realLine = $error['line'];
                throw $ex;
            }
        }
        return $clone;
    }
    
    public function indexBy($propertyName){
        if ($this->count()) {
            $refl = new \ReflectionClass($this->first());
            if($refl->hasProperty($propertyName)){
                $itensClone = $this->items;
                $this->clear();
                foreach($itensClone as $idx => $object){
                    $this[$object->{$propertyName}] = $object;
                }
            }else{
                $error = debug_backtrace()[1];
                $ex = new \Productive\Exception\DataException('Propriedade inexistente '.$propertyName);
                $ex->realFile = $error['file'];
                $ex->realLine = $error['line'];
                throw $ex;
            }
        }
        return $this;
    }
    
    public function getLinearArrayOfProperties(array $properties){
        $res = [];
        if ($this->count()) {
            $refl = new \ReflectionClass($this->first());
            foreach($properties as $propertyName){
                if(!$refl->hasProperty($propertyName)){
                    $error = debug_backtrace()[1];
                    $ex = new \Productive\Exception\DataException('Propriedade inexistente '.$propertyName);
                    $ex->realFile = $error['file'];
                    $ex->realLine = $error['line'];
                    throw $ex;
                }
            }
            foreach($this as $idx => $object){
                foreach($properties as $propertyName){
                    $res[$propertyName][$idx] = $object->{$propertyName};
                }
            }
        }
        return $res;
    }
    
    public function toArray(){
        return $this->items;
    }

    public function jsonSerialize() {
        return $this->items;
    }

}