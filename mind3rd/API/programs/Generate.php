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
		public $what;
		public $param;
		public $detail;
		public $optional;
		public $extra;

		public function configure()
		{
			$this->setName('generate')
				 ->setDescription('Commits the analyzed content to a new version')
				 ->setRestrict(true)
				 ->setHelp(<<<EOT
	This command will increase the current version and also will persist the currently analyzed structure into the system's knowledge base.
EOT
					)
				 ->setDefinition(Array(
					 new InputArgument('what', InputArgument::REQUIRED, 'What to create'),
					 new InputArgument('param', InputArgument::OPTIONAL, 'A param for that command'),
					 new InputArgument('detail', InputArgument::OPTIONAL, 'A detail for that command'),
					 new InputArgument('optional', InputArgument::OPTIONAL, 'An optional argument'),
					 new InputArgument('extra', InputArgument::OPTIONAL, 'Extra data to pass'),
				  ));
		}
		
		public function execute(Console\Input\InputInterface $input,
								Console\Output\OutputInterface $output)
		{
			if(!parent::execute($input, $output))
				return false;
			$this->what= $input->getArgument('what');
			$this->param= $input->getArgument('param');
			$this->detail= $input->getArgument('detail');
			$this->optional= $input->getArgument('optional');
			$this->extra= $input->getArgument('extra');
			Mind::write('thinking');
			$this->runAction();
		}

		public function HTTPExecute()
		{
			GLOBAL $_REQ;
			if(!parent::HTTPExecute())
				return false;
			$this->what    = $_REQ['data']['what'];
			$this->param  = (isset($_REQ['data']['param']))? $_REQ['data']['param']: false;
			$this->detail  = (isset($_REQ['data']['detail']))? $_REQ['data']['detail']: false;
			$this->optional= (isset($_REQ['data']['optional']))? $_REQ['data']['optional']: false;
			$this->extra   = (isset($_REQ['data']['extra']))? $_REQ['data']['extra']: false;
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
			Mind::$gosh->generate(Array(
									$this->what,
									$this->param,
									$this->detail,
									$this->optional,
									$this->extra
								  ));
			return $this;
		}

		public function runAction()
		{
			$ret= $this->action();
			parent::runAction();
			return $ret;
		}
	}
