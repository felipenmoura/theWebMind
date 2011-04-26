<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
/**
 * Class to deal with User's structure.
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
class MindUser
{
    public static function listUsers($detailed=false)
    {
        $db= new \MindDB();
        if($detailed)
            $projs= $db->query('SELECT * from user');
        else
            $projs= $db->query('SELECT login from user');
        return $projs;
    }
}