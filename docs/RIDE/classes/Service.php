<?php
/**
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */

session_start();

class Service {
    
    public static function isAutorized()
    {
        if(!isset($_SESSION['auth']))
            return false;
        return true;
    }
    
    public static function login()
    {
        print_r($_POST);
        return false;
    }
}