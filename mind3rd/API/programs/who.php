<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;

	/**
	 * This class represents a model for programs
	 *
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class Who extends MindCommand implements program
	{
        
        public function executableFunction()
        {
            $name= JSON_decode($_SESSION['auth']);
            $name= $name->name;
            echo 'You are '.$_SESSION['login'].", also known as ".$name."\n";
        }
        
        public function __construct()
        {
            $this->setCommandName('who')
                 ->setDescription("Show information about the currently logged used")
                 ->setRestrict(true)
                 ->setHelp("Show information about the currently logged used")
                 ->setAction(function($class){
                     $class->executableFunction();
                   });
            
            /*$this->addOption('am', 'am');
            $this->addOption('i', 'i');*/
            $this->addRequiredArgument('am',
                                       'am',
                                       Array('am'));
            $this->addRequiredArgument('i',
                                       'I',
                                       Array('i', 'I', 'i?', 'I?'));
            $this->init();
        }
    }