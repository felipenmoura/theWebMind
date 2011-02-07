<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
	class Clear extends MindCommand implements program
	{
		public function configure()
		{
			$this->setName('clear')
				 ->setDescription('Clears the console')
				 ->setDefinition(array())
				 ->setHelp(<<<EOT
			Clears the console
EOT
					);
		}
		public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
		{
			system('clear');
		}

		public function HTTPExecute()
		{
		}

		private function action()
		{
			return $this;
		}

		public function runAction()
		{
			$ret= $this->action();
			parent::runAction();
			return $ret;
		}
	}
