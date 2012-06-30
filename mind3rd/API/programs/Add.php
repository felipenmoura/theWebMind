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
	 * This class represents a model for programs.
	 *
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class Add extends MindCommand implements program
	{
        /*
         * The properties you will use as argument MUST be declared, and public
         */
        public $user= '';
        public $to= '';
        public $project= '';
        
        public function executableFunction()
        {
            if(!\API\User::userExists($this->user))
            {
                echo "user does not exist";
                return false; // TODO: put it into L10N
            }
            if(!\API\Project::projectExists($this->project))
            {
                echo "project does not exist";
                return false; // TODO: put it into L10N
            }
            
            if(!$projectData= \Mind::hasProject($this->project))
                return false;
            
            $pF= new DAO\ProjectFactory($projectData);
            if($pF->addUser(\MindUser::getUserByLogin($this->user))){
                \MindSpeaker::write('done');
                return true;
            }else{
                return false;
            }
            
            //var_dump($pF);
            //print_r($pF);
            //echo $this->project."\n\n";
            //echo "BBBBBBBBB";
            //$pF= new DAO\ProjectFactory(Mind::$currentProject);
            
            //$user= \API\User::loadUserInfo($this->user);
            //print_r(\API\Project::data());
        }
        
        public function __construct()
        {
            /**
             * You can use the following structure to set the program behavior
             */
            $this->setCommandName('add')
                 ->setDescription("Add a user to an specified project")
                 ->setRestrict(true)
                 ->setHelp("Use this command to add a user to a project.\nYou MUST be the project's owner")
                 ->setAction('executableFunction');
           
            /**
             * The next commands shows you how to set the signature of you program, such as
             * parameters, options or flags.
             * Your class will receive a property for each parameter, which can be accessed
             * by its argument name(in this example, 'firstArgument'.
             */
            $this->addRequiredArgument('user',
                                       'The user to be added');
            $this->addRequiredArgument('to',
                                       'string "to"');
            $this->addRequiredArgument('project',
                                       'The project in which the user will be added as developer');
            
            
            $this->init();
        }
	}
