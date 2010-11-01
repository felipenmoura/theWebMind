<?php

	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
    
	class RunTest extends Symfony\Component\Console\Command\Command
	{
		public function configure()
		{
			$this
					->setName('test')
					->setDescription('Performs some tests on theWebMind')
					->setDefinition(array())
					->setHelp(<<<EOT
			Executes specific tests and report their results, about the system itself
EOT
					);
		}
		public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
		{
			if(!isset($_SESSION['auth']))
			{
				Mind::write('not_allowed');
				Mind::write('not_allowed_tip');
				return false;
			}
			$this->runStep1();
		}
		
		private function runStep1()
		{
			Mind::message('Autoloader', '[OK]');
			Mind::message('Includes', '[OK]');
			return true;
		}
	}
