<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace API;
/**
 * Description of Get
 *
 * @author felipe
 */
class Get{
    
    public static function plugins($echoes=false)
    {
        return \MindPlugin::listPlugins($echoes);
    }
    public static function lobes()
    {
        return \Lobe\Neuron::listLobes();
    }
    public static function projectData()
    {
        $dt= \Mind::$currentProject;
        if(isset($dt['data']))
            $dt['data']= '';
        return $dt;
    }
    public static function tables()
    {
        return \Analyst::getUniverse();
    }
    public static function DDL()
    {
        $dbDriver= \Mind::$currentProject['database_drive'];
        \DQB\QueryFactory::setUp($dbDriver);
        return \DQB\QueryFactory::getCompleteQuery(false, true, 'array');
    }
    public static function DecoratedDDL()
    {
        $dbDriver= \Mind::$currentProject['database_drive'];
        \DQB\QueryFactory::setUp($dbDriver);
        return \DQB\QueryFactory::getCompleteQuery(true, false, 'array');
    }
 
    public static function currentProject()
    {
        return \Mind::$project? \Mind::$project:
                                      false;
    }
    public static function source()
    {
        return \API\Project::source();
    }
    public static function idioms()
    {
        return \Mind::getIdiomsList();
    }
}