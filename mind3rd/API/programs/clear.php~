<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
	class Clear extends Symfony\Component\Console\Command\Command
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
	}
