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
    class Program{
 
        public static function all($full=false)
        {
            return ($full)? \MIND::$programs : array_keys(\MIND::$programs);
        }

        public static function getDetails($programName)
        {
            return \MIND::$programs[strtolower($programName)];
        }

        public static function getHelp($programName)
        {
            $help= self::signature($programName)."\n";
            $help.= \MIND::$programs[strtolower($programName)]->getHelp();
            return $help;
        }

        public static function getArgs($programName)
        {
            return \MIND::$programs[strtolower($programName)]->getDefinition();
        }
        
        public static function argumentsDescription($programName)
        {
            $args= \MIND::$programs[strtolower($programName)]->getDefinition();
            foreach($args->getArguments() as $arg)
            {
                
            }
        }
        
        public static function signature($programName, $string=true)
        {
            $signature= Array($programName);
            $def= \MIND::$programs[strtolower($programName)]->getDefinition();
            foreach($def->getArguments() as $arg)
            {
                $signature[]= $arg->getName();
            }
            $options= Array();
            foreach($def->getOptions() as $opt)
            {
                $options[]= $opt->getName();
            }
            if(sizeof($options) > 0)
            {
                if($string)
                    $signature[]= '[';
                $signature= array_merge($signature, $options);
                if($string)
                    $signature[]= ']';
            }
            return $string? implode(' ', $signature): $signature;
        }
        
        public static function execute($programName)
        {
            if(strpos($programName, ' ') !== false)
            {
                $args= explode(' ', $programName);
                $programName= array_shift($args);
            }else{
                    $args= \func_get_args();
                    array_shift($args);
                 }
            
            $realArgs= self::signature($programName, false);
            array_shift($realArgs);
            
            $programName= self::getDetails($programName)->getFileName();
            
            $program= new $programName;
            foreach($args as $k=>$arg)
            {
                if($arg == 'help' || $arg=='-h' || $arg=='--help')
                {
                    echo self::getHelp($programName);
                    return;
                }
                $program->$realArgs[$k]= $arg;
            }
            $program->action();
        }
    }