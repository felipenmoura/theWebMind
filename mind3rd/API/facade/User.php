<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace API;
/**
 * Description of User
 *
 * @author felipe
 */
class User{
    public static function usersList()
    {
        return \MindUser::listUsers();
    }
    public static function projectsList()
    {
        return \MindProject::projectsList();
    }
    /*
    public static function ()
    {
    }
    public static function ()
    {
    }*/
}