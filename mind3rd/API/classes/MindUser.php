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
    static protected $dbConn          = false;
    static protected $validAttrs      = Array('name', 'email', 'pwd');
    static protected $adminValidAttrs = Array('status', 'type');
    
    protected static function hash($pwd)
    {
        return sha1($pwd);
    }
    
    public static function set($attr, $value, $user=false)
    {
        if(\in_array($attr, self::$adminValidAttrs) || $user)
        {
            if($_SESSION['pk_user'] != 1)
            {
                \Mind::write('mustBeAdmin');
                return false;
            }
        }elseif(!\in_array($attr, self::$validAttrs))
                return false;
        
        if($attr == 'pwd')
            $value= self::hash($value);
        
        $value= (is_string($value))? "'".$value."'": $value;
        
        $db= self::getDBConn();
        $user= $user? $user: $_SESSION['pk_user'];
        $qr= "UPDATE user set ".$attr."= ".$value.
             "WHERE pk_user=".$user;
        $db->execute($qr);
        if($attr == 'pwd')
            echo "\n";
    }
    
    protected static function getDBConn()
    {
        if(!self::$dbConn)
            self::$dbConn= new \MindDB();
        return self::$dbConn;
    }
    
    public static function listUsers($detailed=false)
    {
        $db= self::getDBConn();
        if($detailed)
            $usrs= $db->query('SELECT * from user');
        else
            $usrs= $db->query('SELECT login from user');
        return $usrs;
    }
}