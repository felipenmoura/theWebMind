<?php
	//use Mind\Command;
	
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
    
	class RunTest extends MindCommand implements program
	{
		public function configure()
		{
			$this
					->setName('test')
					->setDescription('Performs some tests on theWebMind')
					->setDefinition(array())
					->setRestrict(false)
					->setFileName('RunTest')
					->setHelp(<<<EOT
			Executes specific tests and report their results, about the system itself
EOT
					);
		}

		private function action()
		{
			$this->runStep1();
			$this->runStep2();
			$this->runStep3();
		}
		
		public function runAction()
		{
			$ret= $this->action();
			parent::runAction();
			return $ret;
		}

		public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
		{
			if(!parent::execute($input, $output))
				return false;
			return $this->runAction();
		}
		
		public function HTTPExecute()
		{
			if(!parent::HTTPExecute())
				return false;
			return $this->runAction();
		}

		private function runStep1()
		{
			// by this point, if it reached here, we know these steps are ok
			Mind::message('Autoloader', '[OK]');
			Mind::message('Includes', '[OK]');
			Mind::message('Namespaces', '[OK]');
		}
		private function runStep2()
		{
			if(!$db = new SQLiteDatabase(_MINDSRC_.'/mind3rd/SQLite/mind'))
			{
				Mind::message('Database', '[Fail]');
				return false;
			}
			Mind::message('Database', '[OK]');
			return true;
		}
		private function runStep3()
		{
			if(!is_readable(Mind::$projectsDir) || !is_writable(Mind::$projectsDir))
				$stat= '[Fail]';
			else
				$stat= '[OK]';
			Mind::message('Read & Write permissions', $stat);
		}

		public function  __construct($name = null) {
			parent::__construct($name);
		}
	}
