<?php

/**
 * Mimoza
 * Mind Module for Zend Framework Applications
 * 
 * @author Wanderson Henrique Camargo Rosa
 * @see http://code.google.com/p/webmind/wiki/Mimoza
 * 
 * @uses Module
 * @uses Module_Interface
 */
class Mimoza extends Module implements Module_Interface
{
    /**
     * Diretório da Estrutura Inicial do Aplicativo
     * 
     * @var string Nome do Diretório da Estrutura
     */
    protected $_structure;

    /**
     * Configura a Localização da Estrutura Inicial
     * 
     * @param $structure Nome do Diretório da Estrutura
     * @return Mimoza Próprio Objeto
     */
    public function setStructure($structure)
    {
        $this->_structure = (string) $structure;
        return $this;
    }

    /**
     * Informa o Nome do Diretório da Estrutura Inicial
     * 
     * @return string Nome do Diretório
     */
    public function getStructure()
    {
        return $this->_structure;
    }

    /**
     * Execuções em Tempo de Inicialização
     * Métodos Necessários para Execução Inicial do Módulo
     * Criação da Estrutura Inicial
     * 
     * @return Mimoza Próprio Objeto
     */
    public function onStart()
    {
        return $this;
    }

    /**
     * Execuções em Tempo de Finalização
     * Métodos Necessários para Execução Final do Módulo
     * 
     * @return Mimoza Próprio Objeto
     */
    public function onFinish()
    {
        return $this;
    }

    /**
     * Aplica as Estruturas de Programação Referentes à Entidade
     * 
     * @param Table $entity
     * @return Mimoza Próprio Objeto
     */
    public function applyCRUD($entity)
    {
        return $this;
    }

    /**
     * Chamadas para Métodos Extras Conforme Necessidade
     * 
     * @return Mimoza Próprio Objeto
     */
    public function callExtra()
    {
        return $this;
    }
}