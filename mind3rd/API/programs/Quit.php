<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
	class Quit extends MindCommand implements program
	{
		public function __construct()
		{
			$this->setCommandName('exit')
				 ->setDescription('Finishes the application')
				 ->setAction('action')
                 ->setRestrict(false)
				 ->setFileName('Quit')
				 ->setHelp(<<<EOT
			Finishes the application, leaving the console;
EOT
					);
            $this->init();
		}

		public function action()
		{
			@session_destroy();
			Mind::write('bye');
            exit;
		}
	}
