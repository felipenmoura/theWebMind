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
	class Analyze extends MindCommand implements program
	{
		private $nameSpace= false;
		public  $autoCommit= false;

		public function __construct()
		{
			$this->setCommandName('analyze')
				 ->setDescription('Analyze the the code for your application')
				 ->setRestrict(true)
                 ->setAction('action')
				 ->setHelp(<<<EOT
	This program will analyze your code, typed on your application directory, on all the files .mnd starting for the main.mnd
	You may have as many files as you want, each of then will be treated as namespaces and will be analyzed too,
	unless you have sent an specific namespace to parse, as argument
EOT
				 );
            $this->addOptionalArgument('namespace', 'Analyze an specific namespace');
            $this->addFlag('commit', false, "Commit the result after analisys");
            
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
			Mind::write('thinking');
			MindProject::analyze($this->autoCommit? true: false);
            //Mind::write('done');
			return $this;
		}

	}
