<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
	class ConsoleCommand extends MindCommand implements program
	{
		public function __construct()
		{
			$this->setCommandName('eval')
				 ->setDescription('Clears the console')
                 ->setRestrict(false)
                 ->setFileName('ConsoleCommand')
                 ->setAction('action')
				 ->setHelp(<<<EOT
			Clears the console
EOT
					);
            
            //$this->addArgument('command', null, 'The command to be executed');
            $this->addRequiredArgument('command', 'The command to be executed');
            
            $this->init();
		}
		public function action()
		{
            return \API\Program::execute($this->command);
		}
	}
