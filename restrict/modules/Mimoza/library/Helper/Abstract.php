<?php

Mimoza_Loader::loadClass('Mimoza_Builder_Abstract');

/**
 * Mimoza Helper
 * Auxiliar para Métodos Adicionais aos Construtores
 * 
 * @author Wanderson Henrique Camargo Rosa
 * @see    http://code.google.com/p/webmind/Mimoza
 * 
 * @uses Mimoza_Builder_Abstract
 * 
 * @package Mimoza
 * @subpackage Helper
 *
 */
abstract class Mimoza_Helper_Abstract
{
    /**
     * Construtor para Auxílio
     * @var Mimoza_Builder_Abstract
     */
    protected $_builder;

    /**
     * Construtor da Classe
     * Referencia um Construtor para Auxílio
     * @param Mimoza_Builder_Abstract $builder Construtor Alvo
     */
    public function __construct(Mimoza_Builder_Abstract $builder)
    {
        $this->_setBuilder($builder);
    }

    /**
     * Configuração do Construtor para Auxílio
     * @param Mimoza_Builder_Abstract $builder Construtor Alvo
     * @return Mimoza_Helper_Abstract Próprio Objeto para Encadeamento
     */
    protected function _setBuilder(Mimoza_Builder_Abstract $builder)
    {
        $this->_builder = $builder;
        return $this;
    }

    /**
     * Informa o Construtor Atual do Auxiliar
     * @return Mimoza_Builder_Abstract Construtor Alvo
     */
    public function getBuilder()
    {
        return $this->_builder;
    }

    /**
     * Método Abstrato
     * Execução Principal do Auxiliar
     * @param mixed $params Parâmetros para Execução
     * @return mixed Valores Gerados pelo Auxiliar Especializado
     */
    public abstract function execute($params);
}