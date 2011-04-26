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
	 * This class represents the program auth, receiving the user and
	 * may also receive the password. It will start your session
	 * allowing you to run the restricted programs
	 *
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class Commit extends MindCommand implements program
	{
		private $nameSpace= false;
		public  $autoCommit= false;

		public function __construct()
		{
			$this->setCommandName('commit')
				 ->setDescription('Commits the analyzed content to a new version')
				 ->setRestrict(true)
                 ->setAction('action')
				 ->setHelp(<<<EOT
	This command will increase the current version and also will persist the currently analyzed structure into the system's knowledge base.
EOT
					);
            $this->init();
		}

		public function action()
		{
			if(!isset($_SESSION['currentProject']))
			{
				Mind::write('currentProjectRequired');
				Mind::write('currentProjectRequiredTip');
				return false;
			}
			MindProject::analyze(true, false);
			return $this;
		}
	}
