<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Productive\Data;

/**
 * Classe que representa uma página abstrata
 * @author calixto
 */
class Paginator {

    /**
     * Número total de linhas do conteúdo
     * @var integer
     */
    public $totalLines = 0;

    /**
     * Número de linhas da página
     * @var integer
     */
    public $linesPerPage;

    /**
     * Número da página atual
     * @var integer
     */
    public $currentPage = 1;

    /**
     * Método construtor
     * @param integer tamanho da página
     */
    public function __construct($linesPerPage = 20) {
        $this->linesPerPage = $linesPerPage;
    }

    /**
     * Retorna o número da linha inicial da pagina atual
     * @return integer número da linha inicial da pagina atual
     */
    function getFirstLine() {
        return ($this->linesPerPage * $this->currentPage) - ($this->linesPerPage - 1);
    }

    /**
     * Retorna o número de linhas de uma pagina
     * @return integer número de linhas de uma pagina
     */
    function getPageSize() {
        return $this->linesPerPage;
    }

    /**
     * Retorna o número da linha final da pagina atual
     * @return integer número da linha final da pagina atual
     */
    function getLastLine() {
        return ($this->linesPerPage * $this->currentPage);
    }

    /**
     * Define a pagina atual
     * @param integer número da pagina atual
     */
    function setPage($pagina = 1) {
        $this->currentPage = ($pagina < 1) ? 1 : $pagina;
        return $this;
    }

    /**
     * Define o escopo total de linhas existentes
     * @param integer número de linhas existentes
     */
    function setTotalLines($totalLines) {
        $this->totalLines = $totalLines;
        if (($this->totalLines > 0) && ($this->totalLines < ($this->linesPerPage * $this->currentPage))) {
            $this->currentPage = $this->getLastPage();
        }
        return $this;
    }

    /**
     * Retorna o tamanho geral do conteudo
     * @return integer número total de linhas do conteudo geral
     */
    function getTotalLines() {
        return $this->totalLines;
    }

    /**
     * Retorna o número da ultima página
     * @return integer
     */
    function getLastPage() {
        if ($this->totalLines % $this->linesPerPage) {
            return (int) ($this->totalLines / $this->linesPerPage) + 1;
        }
        return (int) ($this->totalLines / $this->linesPerPage);
    }
    
    function getCurrentPage() {
        return $this->currentPage;
    }

    /**
     * Incrementa a página atual
     * @return boolean
     */
    function next() {
        if ($this->pegarPagina() < $this->getLastPage()) {
            $this->currentPage++;
            return true;
        } else {
            return false;
        }
    }

}

?>