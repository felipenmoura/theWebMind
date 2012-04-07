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
	class helpContent extends MindCommand implements program
	{
        
        public function executableFunction()
        {
            echo "theWebMind(or just mind):\n    website: http://thewebmind.org\n    Twitter: @thewebmind\n    Docs: http://docs.thewebmind.org\n\n";
                 "The list of  startup commands:\n".
                 "    install  : installs the application itself\n".
                 "    uninstall: uninstalls the application, but do NOT remove projects,\n".
                 "               users or history\n".
                 "    remove   : installs the application AND REMOVE every data it may\n".
                 "               have created, including ALL projects, history and users.\n".
                 "    --help   :\n";
                 "    -h       :\n";
                 "    help     :\n";
                 "    ?        :\n";
                 "              Shows this help content.\n\n";
            echo "After installing, you can open the application by calling the 'mind' command anywhere.\n".
                 "You will also be able to send POST requisition to mind's server address sending the command you want to execute and its parameters.\n";
        }
        
        public function __construct()
        {
            $this->setCommandName('helps')
                 ->setFileName('helpContent')
                 ->setDescription("Shows the help content")
                 ->setRestrict(false)
                 ->setHelp("Shows the help content")
                 ->setAction(function($class){
                     $class->executableFunction();
                   });
                   
            /*$this->addRequiredArgument('firstArgument',
                                       'first, and required argument',
                                       Array('X', 'Y', 'Z'));*/
            $this->init();
        }
    }