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
					->setRestrict(false)
					 ->setDefinition(Array(
						 new InputOption('unit', '-u', InputOption::PARAMETER_NONE, 'Execute unit tests, also')
					 ))
					->setFileName('RunTest')
					->setHelp(<<<EOT
			Executes specific tests and report their results, about the system itself
EOT
					);
		}

		private function action()
		{
			GLOBAL $_MIND;
			$this->runStep1();
			$this->runStep2();
			$this->runStep3();
			if($this->unitTestsAlso)
			{
				if(file_exists(_MINDSRC_."/Tests/bundle.list"))
				{
					if(!isset($_MIND->conf['phpunit-src']))
					{
						// TODO: put it into speaker classes, to use l10n messages
						echo "    You must specify where to find phpUnit classes\n";
						echo "    You can configure it on mind3rd/env/mind.ini ini file\n";
						echo "    changing the phpunit-src ini property\n";
						return false;
					}
					// TODO: it doesn't work yet
					/*
					$unitTestsFolder= _MINDSRC_."/Tests/";
					$unitTestsList= file_get_contents($unitTestsFolder."bundle.list");
					$unitTestsList= explode("\n", $unitTestsList);

					foreach($unitTestsList as $unitTests)
					{
						$unitTests= explode(' ', $unitTests);
						echo "Applying Unit tests to ".$unitTests[1]."\n";
						//include_once $unitTestsFolder.$unitTests[0];
						echo shell_exec("phpunit ".$unitTestsFolder.$unitTests[0]);
						break;
					}
					*/
				}else
				{
					// TODO: send it to the speaker
					echo "[Error] Unit Tests not fount in [root]/Tests\n";
					return false;
				}
			}
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

			$this->unitTestsAlso= $input->getOption('unit')? true: false;

			return $this->runAction();
		}
		
		public function HTTPExecute()
		{
			GLOBAL $_REQ;
			if(!parent::HTTPExecute())
				return false;

			$this->unitTestsAlso= (isset($_REQ['data'])
									&&
								   isset($_REQ['data']['unit']))?
									   true: false;
			
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
