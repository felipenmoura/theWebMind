<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MindUser
 *
 * @author felipe
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