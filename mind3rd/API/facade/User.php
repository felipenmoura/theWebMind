<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
namespace API;
/**
 * A facade class to deal with user's data.
 *
 * @author felipe
 */
class User{
    
    static protected $validAttrs= Array('name', 'email', 'password');
    
    /**
     * Returns an array of all registered users.
     * @return Array
     */
    public static function usersList()
    {
        return \MindUser::listUsers();
    }
    
    /**
     * Gets the list of projects in which the current user is registered to work in.
     * @return Array
     */
    public static function projectsList()
    {
        return \MindProject::projectsList();
    }
    
    public static function set($attr, $value)
    {
        print_r(self::$validAttrs);
    }
    /*
    public static function ()
    {
    }*/
}