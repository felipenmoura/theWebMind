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
	class Set extends MindCommand implements program
	{
        public $whose    = '';
        public $attribute= '';
        public $value    = '';
        
        private function setUserData()
        {
            if($this->attribute=='pwd')
                $this->value= $this->prompt('pwd', 'The new password, please', true);
            \API\User::set($this->attribute, $this->value);
        }
        
        private function setProjectData()
        {
            echo "Project's data\n";
        }
        
        public function executableFunction()
        {
            if($this->whose == 'user')
            {
                $this->setUserData();
            }else{
                $this->setProjectData();
            }
        }
        
        public function __construct()
        {
            
            $this->setCommandName('set')
                 ->setDescription("Sets user's or project's data ")
                 ->setRestrict(false)
                 ->setHelp("You can set information about the current user or a project's data.")
                 ->setAction('executableFunction');
            
            $this->addRequiredArgument('whose',
                                       'Who will suffer the update',
                                       Array('user', 'project'));
            $this->addRequiredArgument('attribute',
                                       'The attribute you will change');
            $this->addOptionalArgument('value',
                                       'The value for that attribute(optional only for pwd');

            $this->init();
        }
	}
