<?php

/**
 * Mimoza Filter
 * Filtro para Saída de Arquivos dos Construtores
 * 
 * @author Wanderson Henrique Camargo Rosa
 * @see    http://code.google.com/p/webmind/Mimoza
 * 
 * @package Mimoza
 * @subpackage Filter
 *
 */
interface Mimoza_Filter_Interface
{
    /**
     * Método da Interface para Filtragem Especializada
     * @param mixed $value Valor para Filtragem
     * @return mixed Valor Processado pelo Filtro
     */
    public function filter($value);
}