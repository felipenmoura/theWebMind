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
    class Project{

        public static function openProject($projectName)
        {
            if(!$projectData= \Mind::hasProject($projectName))
                return false;
            \Mind::openProject($projectData);
            return self::current();
        }

        public static function projectList()
        {
            return \MindProject::listProjects();
        }
        
        public static function current()
        {
            return \Mind::$project? \Mind::$project:
                                          false;
        }

        public static function data()
        {
            $dt= \Mind::$currentProject;
            return $dt;
        }
        public static function getDDLCommand($decorated=true)
        {
            if($decorated)
                return \API\Get::DecoratedDDL();
            return \API\Get::DDL();
        }
        public static function projectExists($projectName)
        {
            return \MindProject::projectExists($projectName);
        }
        public static function analyze()
        {
            return MindProject::analyze(false);
        }
        public static function source()
        {
            if(sizeof(\MindProject::$sourceContent) == 0)
                \MindProject::loadSources();
            return \MindProject::$sourceContent;
        }
    }