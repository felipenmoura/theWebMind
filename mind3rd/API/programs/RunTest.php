<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
    
	class RunTest extends MindCommand implements program
	{
        public $unit=false;
        
		public function __construct()
		{
			$this->setCommandName('test')
                 ->setDescription('Performs some tests on theWebMind')
                 ->setRestrict(false)
                 ->setAction('action')
                 ->setFileName('RunTest')
                 ->setHelp(<<<EOT
            Executes specific tests and report their results, about the system itself
EOT
					);
            
            $this->addFlag('unit', '-u', 'Also execute unit tests');
            
            $this->init();
		}

		public function action()
		{
			GLOBAL $_MIND;
            
            ob_start();
			$this->runStep1();
            ob_flush();
			$this->runStep2();
            ob_flush();
			$this->runStep3();
            ob_flush();
            
			if($this->unit)
			{
                if(!isset($_MIND->conf['phpunit-src']))
                {
                    \Mind::write('phpunitNotFound');
                    return false;
                }
                \Mind::write('runnintPHPUnit');
                ob_flush();
                echo shell_exec($_MIND->conf['phpunit-src']." "._MINDSRC_."/Tests/");
			}
            ob_end_flush();
            return true;
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
			if(!$db = new SQLite3(_MINDSRC_.SQLITE))
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
	}
