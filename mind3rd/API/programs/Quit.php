<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
	class Quit extends MindCommand implements program
	{
		public function configure()
		{
			$this->setName('exit')
				 ->setDescription('Finishes the application')
				 ->setDefinition(Array())
				 ->setFileName('Quit')
				 ->setHelp(<<<EOT
			Finishes the application, leaving the console;
EOT
					);
		}
		public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
		{
			$this->runAction();
			exit;
		}

		public function HTTPExecute()
		{
			$this->runAction();
		}

		private function action()
		{
			session_destroy();
			Mind::write('bye');
			return $this;
		}

		public function runAction()
		{
			$ret= $this->action();
			return $ret;
		}
	}
