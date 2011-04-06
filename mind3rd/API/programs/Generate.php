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
	class Generate extends MindCommand implements program
	{
		private $nameSpace= false;
		public  $autoCommit= false;

		public function configure()
		{
			$this->setName('generate')
				 ->setDescription('Commits the analyzed content to a new version')
				 ->setRestrict(true)
				 ->setDefinition(Array(
					new InputArgument('what', InputArgument::REQUIRED, 'What to create')))
				 ->setHelp(<<<EOT
	This command will increase the current version and also will persist the currently analyzed structure into the system's knowledge base.
EOT
					);
		}
		
		public function execute(Console\Input\InputInterface $input,
								Console\Output\OutputInterface $output)
		{
			if(!parent::execute($input, $output))
				return false;
			Mind::write('thinking');
			$this->runAction();
		}

		public function HTTPExecute()
		{
			GLOBAL $_REQ;
			if(!parent::HTTPExecute())
				return false;
			$this->runAction();
		}

		private function action()
		{
			if(!isset($_SESSION['currentProject']))
			{
				Mind::write('currentProjectRequired');
				Mind::write('currentProjectRequiredTip');
				return false;
			}
			Mind::$gosh->generate();
			return $this;
		}

		public function runAction()
		{
			$ret= $this->action();
			parent::runAction();
			return $ret;
		}
	}
