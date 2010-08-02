<?php

/**
 * Mimoza Exception
 * Manipulador de Exceções para o Pacote
 * 
 * @author Wanderson Henrique Camargo Rosa
 * @see http://code.google.com/p/webmind/Mimoza
 * 
 * @uses Exception
 * 
 * @package Mimoza
 * @subpackage Exception
 *
 */
class Mimoza_Exception extends Exception
{
    /**
     * Construtor
     * @param string $message[optional] Exception Message
     * @param int $code[optional] Exception Code
     * @return void
     */
    public function __construct($message = '', $code = 0)
    {
        parent::__construct($message, (int) $code);
    }
}