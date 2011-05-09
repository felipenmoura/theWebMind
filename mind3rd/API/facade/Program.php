<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
    namespace API;
    /**
     * 
     * @author felipe
     * @package API
     */
    class Program{
 
        /**
         * Gets the complete list of all registered programs.
         * It returns the complete list of all registered programs and, if true is
         * passed as parameter, return the details of each program.
         * 
         * @param boolean $full Complete description of each project, or simply its name
         * @return Array
         */
        public static function all($full=false)
        {
            return ($full)? \MIND::$programs : array_keys(\MIND::$programs);
        }

        /**
         * Loads the details of an specified program.
         * 
         * @param string $programName
         * @return MindCommand|false Returns false in case the required program does not exist.
         */
        public static function getDetails($programName)
        {
            if(self::programExists($programName))
                return \MIND::$programs[strtolower($programName)];
            return false;
        }

        /**
         * Gets the help content for the given program.
         * 
         * @param string $programName
         * @return string|false
         */
        public static function getHelp($programName)
        {
            if(!self::programExists($programName))
                return false;
            $help = "  Signature:\n\t".self::signature($programName)."\n\n";
            $argsHelp= self::argumentsDescription($programName);
            if(sizeof($argsHelp) > 0)
            {
                $help.= "  Arguments:\n";
                foreach($argsHelp as $k=>$arg)
                {
                    $help.= "\t".$k.": ".$arg."\n";
                }
            }
            $help.= "  Definition:\n".\MIND::$programs[strtolower($programName)]->getHelp()."\n";
            return $help;
        }

        /**
         * Gets the arguments list for the given program.
         * 
         * @param string $programName
         * @return InputDefinitin|false
         */
        public static function getArgs($programName)
        {
            if(!self::programExists($programName))
                return false;
            return \MIND::$programs[strtolower($programName)]->getDefinition();
        }
        
        /**
         * Loads the descriptin of each argument.
         * 
         * @param string $programName 
         */
        public static function argumentsDescription($programName)
        {
            if(!self::programExists($programName))
                return false;
            $args= \MIND::$programs[strtolower($programName)]->getDefinition();
            $ar= Array();
            foreach($args->getArguments() as $arg)
            {
                $ar[$arg->getName()]= $arg->getDescription();
            }
            return $ar;
        }
        
        public static function invalidProgam($programName)
        {
            echo $programName." is not a valid/installed progam...\n";
            return false;
        }
        
        public static function programExists($programName)
        {
            return isset(\MIND::$programs[strtolower($programName)]);
        }
        
        public static function signature($programName, $string=true)
        {
            $signature= Array($programName);
            
            if(!self::programExists($programName))
                return self::invalidProgam($programName);
            
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
            
            if(!self::programExists($programName))
                return self::invalidProgam($programName);
            
            $realArgs= self::signature($programName, false);
            $programName= self::getDetails($programName)->getFileName();
            
            array_shift($realArgs);
            
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
            return true;
        }
    }