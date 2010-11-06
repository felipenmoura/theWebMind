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
		private $argName= false;
		private $what= false;

		public function configure()
		{
			$this->setName('use')
				 ->setDescription('Opens the project, or specifies any personal option')
				 ->setRestrict(true)
				 ->setFileName('SetUse')
				 ->setDefinition(Array(
					new InputArgument('what', InputArgument::REQUIRED, 'What to use, from now on'),
					new InputArgument('name', InputArgument::REQUIRED, 'specify what you want to use/open')
				 ))
				 ->setHelp(<<<EOT
	You can use this command to start using a different language or project, for example
EOT
					);
		}
		public function execute(Console\Input\InputInterface $input,
								Console\Output\OutputInterface $output)
		{
			if(!parent::execute($input, $output))
				return false;
			$this->what= $input->getArgument('what');
			$this->argName= $input->getArgument('name');
			$this->runAction();
		}

		public function HTTPExecute()
		{
			GLOBAL $_REQ;
			if(!parent::HTTPExecute())
				return false;
			if(isset($_REQ['data']['what']) && isset($_REQ['data']['name']))
			{
				$this->argName= $_REQ['data']['name'];
				$this->what= $_REQ['data']['what'];
			}
			$this->runAction();
		}

		private function action()
		{
			switch($this->what)
			{
				case 'project':
						if(!$projectData= Mind::hasProject($this->argName))
							return false;
						Mind::openProject($projectData);
					break;
			}
			return $this;
		}

		public function runAction()
		{
			return $this->action();
		}
	}
