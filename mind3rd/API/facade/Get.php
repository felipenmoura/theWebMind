<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
namespace API;
/**
 * This is a facade to help you getting generic data.
 * This class offers you a bunch of "shortcuts" to many different data or results.
 *
 * @author felipe
 */
class Get{
    
    /**
     * Gets a list of installed plugins.
     * 
     * @param boolean $echoes If the plugins list should be sent to the output
     * @return Array
     */
    public static function plugins($echoes=false)
    {
        return \MindPlugin::listPlugins($echoes);
    }
    
    /**
     * Gets an array with all the installed lobes.
     * @return Array
     */
    public static function lobes()
    {
        return \Lobe\Neuron::listLobes();
    }
    
    /**
     * Gets all the information about the current project.
     * @return string 
     */
    public static function projectData()
    {
        $dt= \Mind::$currentProject;
        return $dt;
    }
    
    /**
     * Returns a list of identified tables for the current project.
     * @return Array
     */
    public static function tables()
    {
        return \Analyst::getUniverse();
    }
    
    /**
     * Returns an array with all the DDL codes for the current project.
     * @return Array
     */
    public static function DDL()
    {
        $dbDriver= \Mind::$currentProject['database_drive'];
        \DQB\QueryFactory::setUp($dbDriver);
        return \DQB\QueryFactory::getCompleteQuery(false, true, 'array');
    }
    
    /**
     *
     * Returns an array with all the DDL codes for the current project.
     * This method returns each DDL comand as a decorated command, containing HTML tags.
     * @return Array
     */
    public static function DecoratedDDL()
    {
        $dbDriver= \Mind::$currentProject['database_drive'];
        \DQB\QueryFactory::setUp($dbDriver);
        return \DQB\QueryFactory::getCompleteQuery(true, false, 'array');
    }
 
    /**
     * Gets the currently opened project or false if none.
     * 
     * @return \MindProject|false
     */
    public static function currentProject()
    {
        return \Mind::$project? \Mind::$project:
                                      false;
    }
    
    /**
     * Gets an array with all the source code, for the current project.
     * @return Array
     */
    public static function source()
    {
        return \API\Project::source();
    }
    
    /**
     * Gets the list of currently instaled idioms.
     * @return Array
     */
    public static function idioms()
    {
        return \Mind::getIdiomsList();
    }
}