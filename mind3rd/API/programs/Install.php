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
	 * This program will be able to install components to the application.
     * These components may be a new L10N idiom, a new language to be interpreted, a new program, a new DBMS, a new Plugin or a new Lobe.
	 *
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class Install extends MindCommand implements program
	{
        /*
         * The properties you will use as argument MUST be declared, and public
         */
        public $firstArgument= '';
        
        protected function installIt()
        {
            $list= explode("\n",
                           file_get_contents(_MINDSRC_.
                                             '/mind3rd/env/trusted-sources.list'));
            foreach($list as $source)
            {
                $src= $source.$this->what."/".$this->which_one.".xml";
                echo $src."\n";
                if(file_exists($src))
                {
                    echo file_get_contents($src);
                    return true;
                }
            }
            echo "Thing not found!";
            return false;
        }

        public function executableFunction()
        {
            if(Mind::getInstance()->conf['allow_installation'])
            {
                return $this->installIt();
            }
            return Mind::write('cannotInstall');
        }
        
        public function __construct()
        {
            $this->setCommandName('install')
                 ->setDescription("Adds components to the application")
                 //->setFileName('modeloTeste') // use this if your class has NOT the same name as its file
                 ->setRestrict(true)
                 ->setAdminAccess()
                 ->setHelp("This program will be able to install components to the application.\nThese components may be a new L10N idiom, a new language to be interpreted, a new program, a new DBMS, a new Plugin or a new Lobe.")
                 ->setAction('executableFunction');
            
            $this->addRequiredArgument('what',
                                       'what will be installed',
                                       Array('l10n', 'language', 'dbms', 'plugin', 'lobe'));
            
            $this->addRequiredArgument('which_one',
                                       'What is te one of it you choosed, you will install?');
            
            //$this->addOptionalArgument('secondArgument', 'This is the second and optional argument');
            //$this->addRequiredOption('user', '-u', 'The user who will be passed for any reason', 'root');
            //$this->addOptionalOption('detailed', '-d', 'Should perform its action detailed?', null);
            //$this->addFlag('silent', '-s', 'Executes the command quietly');

            $this->init();
        }
    }