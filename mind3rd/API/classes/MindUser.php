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
            if(!\MindUser::isAdmin())
            {
                \Mind::write('mustBeAdmin');
                return false;
            }
        }
        if(!\in_array($attr, self::$validAttrs))
            return false;
        
        if($attr == 'pwd')
            $value= self::hash($value);
        
        $value= (is_string($value))? "'".$value."'": $value;
        
        $db= self::getDBConn();
        if($user && !is_numeric($user)){
            $user= \MindUser::getUserByLogin($user);
            if(!$user){
                \MindSpeaker::write('auth_fail');
                return false;
            }
            $user= $user['pk_user'];
        }
        $user= $user? $user: $_SESSION['pk_user'];
        $qr= "UPDATE user set ".$attr."= ".$value.
             "WHERE pk_user=".$user;
        
        $db->execute($qr);
        if($attr == 'pwd')
            echo "\n";
    }
    
    /**
     * Verifies if there is a user autenticated.
     * @return boolean
     */
    public static function isIn()
    {
        return isset($_SESSION['pk_user']);
    }
    
    /**
     * Retrieves if the currently logged user is an administrator.
     * @return boolean
     */
    public static function isAdmin()
    {
        return isset($_SESSION['pk_user']) && $_SESSION['type'] == 'A';
    }
    
    protected static function getDBConn()
    {
        if(!self::$dbConn)
            self::$dbConn= new \MindDB();
        return self::$dbConn;
    }
    
    /**
     * Retrieves the user details based on the login.
     * 
     * This method DEMANDS the current user to be admin.
     * 
     * @param String $login
     * @return UserObject Or false if not admin
     */
    public static function getUserByLogin($login){
        if(!\MindUser::isAdmin()){
            \Mind::write('mustBeAdmin');
            return false;
        }
            
        $db= self::getDBConn();
        $user= false;
        $usrs= $db->query("SELECT * from user where login = '".addslashes($login)."'");
        foreach($usrs as $k=>$usr)
        {
            $user= $usr;
            break;
        }
        return $user;
    }
    
    /**
     * Retrieves the list of users.
     * @param boolean $detailed
     * @return mixed
     */
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