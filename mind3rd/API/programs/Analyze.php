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

		public function configure()
		{
			$this->setName('analyze')
				 ->setDescription('Analyze the the code for your application')
				 ->setRestrict(true)
				 ->setDefinition(Array(
					new InputArgument('namespace', InputArgument::OPTIONAL, 'Analyze an specific namespace'),
					new InputOption('commit', false, InputOption::PARAMETER_NONE, "Commit the result after analisys")
				 ))
				 ->setHelp(<<<EOT
	This program will analyze your code, typed on your application directory, on all the files .mnd starting for the main.mnd
	You may have as many files as you want, each of then will be treated as namespaces and will be analyzed too,
	unless you have sent an specific namespace to parse, as argument
EOT
					);
		}
		public function execute(Console\Input\InputInterface $input,
								Console\Output\OutputInterface $output)
		{
			if(!parent::execute($input, $output))
				return false;
			$this->nameSpace= $input->getArgument('namespace');
			$this->autoCommit= $input->getOption('commit');
			Mind::write('thinking');
			$this->runAction();
		}

		public function HTTPExecute()
		{
			GLOBAL $_REQ;
			if(!parent::HTTPExecute())
				return false;
			if(isset($_REQ['data']['namespace']))
			$this->nameSpace= $_REQ['data']['namespace'];
			if(isset($_REQ['data']['commit']))
				$this->autoCommit= $_REQ['data']['commit'];
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

			MindProject::analyze();
			return $this;
		}

		public function runAction()
		{
			$ret= $this->action();
			parent::runAction();
			return $ret;
		}
	}
