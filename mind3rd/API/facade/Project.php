<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
    namespace API;
    /**
     * This class is a facade for methods to deal with Project's data.
     *
     * @author felipe
     */
    class Project{

        /**
         * Opens and load a specific project.
         * Retrurns the MindProject object of the opened project, of false in case of unsuccess.
         * 
         * @param string $projectName
         * @return MindProject|false
         */
        public static function openProject($projectName)
        {
            if(!$projectData= \Mind::hasProject($projectName))
                return false;
            \Mind::openProject($projectData);
            return self::current();
        }

        /**
         * Retrieves the list of existing projects.
         * 
         * @return Array
         */
        public static function projectList($detail=false)
        {
            return \MindProject::listProjects($detail);
        }
        
        /**
         * Returns the current project.
         * @return MindProject|false
         */
        public static function current()
        {
            return \Mind::$project? \Mind::$project:
                                    false;
        }

        /**
         * Returns an array with information about the current project.
         * @return Array
         */
        public static function data()
        {
            $dt= \Mind::$currentProject;
            return $dt;
        }
        
        /**
         *
         * Returns an array with all the DDL codes for the current project.
         * This method returns each DDL comand as a decorated command, containing HTML tags.
         * 
         * @param boolean $decorated
         * @return Array
         */
        public static function getDDLCommand($decorated=true)
        {
            if($decorated)
                return \API\Get::DecoratedDDL();
            return \API\Get::DDL();
        }
        
        /**
         * Verifies whether the project exists or not.
         * 
         * @param string $projectName
         * @return boolean
         */
        public static function projectExists($projectName)
        {
            return \MindProject::projectExists($projectName);
        }
        
        /**
         * Askes the core to analyze the current project.
         * 
         * @return boolean
         */
        public static function analyze()
        {
            return MindProject::analyze(false);
        }
        
        public static function set($attr, $val, $proj)
        {
            return \MindProject::set($attr, $val, $proj);
        }
        
        /**
         * Gets an array with all the source code, for the current project.
         * @return Array
         */
        public static function source()
        {
            if(sizeof(\MindProject::$sourceContent) == 0)
                \MindProject::loadSources();
            return \MindProject::$sourceContent;
        }
    }