<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;

	/**
	 * This class represents the program auth, receiving the user and
	 * may also receive the password. It will start your session
	 * allowing you to run the restricted programs
	 *
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class SetUse extends MindCommand implements program
	{
		public $projectName= false;

		public function __construct()
		{
			$this->setCommandName('use')
				 ->setDescription('Opens the project, or specifies any personal option')
				 ->setRestrict(true)
                 ->setaction('action')
				 ->setFileName('SetUse')
				 ->setHelp(<<<EOT
	You can use this command to start using a different language or project, for example
EOT
					);
            
            $projectsList= \API\Project::projectList();
            foreach($projectsList as $k=>$p)
            {
                $projectsList[$k]= $p['name'];
            }
            
            $this->addRequiredArgument('projectName',
                                       'Specify the project name, you want to use/open',
                                       $projectsList);
            $this->init();
		}
        
		public function action()
		{
            if(!$projectData= Mind::hasProject($this->projectName))
                return false;
            Mind::openProject($projectData);
			return $this;
		}
	}
