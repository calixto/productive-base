<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Productive\Data;

/**
 * Description of DataTrait
 *
 * @author calixto
 */
trait DatabaseRowTrait {
    
    /**
     * Retorna os campos chave do objeto
     * @return array
     */
    public function keys(){
        $keys = [];
        foreach($this->describe()['fields'] as $field){
            if($this->{$field['property']} && isset($field['primaryKey']) && $field['primaryKey'] == 'true'){
                $keys[$field['property']] = $this->{$field['property']};
            }
        }
        return $keys;
    }

    /**
     * Retorna a coleção padrão de retorno 
     * @return \Productive\Data\Collection
     */
    public function defaultCollection() {
        return new \Productive\Data\Collection();
    }

    /**
     * Retorna a model padrão de uso
     * @return \Productive\Tier\Model
     */
    public function defaultModel() {
        return new \Productive\Tier\Model();
    }
    
    /**
     * Retorna o primeiro objeto similar encontrado
     * @param type $values
     * @return Data
     */
    public function first($values = null){
        if($values){
            parent::loadFromView($values);
        }
        return $this->similars()->first();
    }

    /**
     * Seleciona os objetos similares a este
     * @param \Productive\Data\Paginator $page
     * @return Collection
     */
    public function similars(\Productive\Data\Paginator $page = null) {
        return $this->defaultModel()->select($this, $page);
    }

    /**
     * Insere este objeto no banco
     * @return Data
     */
    public function insert() {
        return $this->validate('insert')->defaultModel()->insert($this);
    }

    /**
     * Atualiza este objeto no banco
     * @return Data
     */
    public function update() {
        return $this->validate('update')->defaultModel()->update($this);
    }

    /**
     * Remove este objeto do banco
     * @return Data
     */
    public function delete() {
        return $this->validateKeys('delete')->defaultModel()->delete($this);
    }
    
    /**
     * Retorna se este objeto é identificado
     * @return boolean
     */
    protected function isIdentified(){
        return count($this->keys());
    }
    
    /**
     * Salva este objeto no banco 
     * @return Data
     */
    public function save() {
        if($this->isIdentified()){
            return $this->update();
        }else{
            return $this->insert();
        }
    }

}
