<?php

Mimoza_Loader::loadClass('Mimoza_Filter_Interface');

/**
 * Filtro de Troca de Informações
 * 
 * @author Wanderson Henrique Camargo Rosa
 * @see http://code.google.com/p/webmind/Mimoza
 * 
 * @uses Mimoza_Filter_Interface
 * 
 * @package Mimoza
 * @subpackage Filter
 *
 */
class Mimoza_Filter_Replace implements Mimoza_Filter_Interface
{
    /**
     * Elementos para Troca
     * Conjunto de Informações com Chave para Valor Atual
     * Elemento da Chave Aponta para Novo Valor para Processamento
     * @var array
     */
    protected $_elements = array();

    /**
     * Configuração de Elementos para Troca
     * @param string $source Elemento Inicial
     * @param string $target Elemento para Troca
     * @return Mimoza_Filter_Replace Próprio Objeto para Encadeamento
     */
    public function replace($source, $target)
    {
        $source = (string) $source;
        $target = (string) $target;
        $this->_elements[$source] = $target;
        return $this;
    }

    public function filter($value)
    {
        $elements = $this->_elements;
        foreach ($elements as $source => $target) {
            $value = str_ireplace($source, $target, $value);
        }
        return $value;
    }
}