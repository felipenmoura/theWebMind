<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
	class Clear extends MindCommand implements program
	{
		public function __construct()
		{
			$this->setCommandName('clear')
				 ->setDescription('Clears the console')
                 ->setRestrict(false)
                 ->setAction('action')
				 ->setHelp(<<<EOT
			Clears the console
EOT
					);
            $this->init();
		}
		public function action()
		{
			system('clear');
		}
	}
