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
	class Remove extends MindCommand implements program
	{
        /*
         * The properties you will use as argument MUST be declared, and public
         */
        public $user= '';
        public $from= '';
        public $project= '';
        
        public function executableFunction()
        {
            if(!\MindUser::isAdmin()){
                \MindSpeaker::write('mustBeAdmin');
                return false;
            }
            
            if(!\API\User::userExists($this->user))
            {
                \MindSpeaker::write('auth_fail');
                return false;
            }
            
            if(!\API\Project::projectExists($this->project) || !$projectData= \Mind::hasProject($this->project))
            {
                \MindSpeaker::write('noProject', true, $this->project);
                return false;
            }
            
            $pF= new DAO\ProjectFactory($projectData);
            if($pF->removeUser(\MindUser::getUserByLogin($this->user))){
                \MindSpeaker::write('done');
                return true;
            }else{
                return false;
            }
        }
        
        public function __construct()
        {
            /**
             * You can use the following structure to set the program behavior
             */
            $this->setCommandName('remove')
                 ->setDescription("Removes a user from a project")
                 ->setRestrict(true)
                 ->setHelp("Use this command to remove a user's access to a project.")
                 ->setAction('executableFunction');
           
            $this->addRequiredArgument('user',
                                       'The user to be removed');
            $this->addRequiredArgument('from',
                                       'string "from"');
            $this->addRequiredArgument('project',
                                       'The project from which the user will be removed');
            
            $this->init();
        }
	}
