<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;

	/**
	 * This class represents the program auth, receiving the user and
	 * may also receive the password. It will start your session
	 * allowing you to run the restricted programs
	 *
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class Generate extends MindCommand implements program
	{
		public $lobe;
		public $param;
		public $detail;
		public $optional;
		public $extra;

		public function __construct()
		{
            $dir= \theos\ProjectFileManager::getLobesDir();
            $d = dir($dir);
            $options= Array();
            while (false !== ($entry = $d->read())) {
                if(is_dir($dir.$entry) && substr($entry, 0, 1) != '.')
                    $options[]= "     >".$entry."\n";
            }
            $d->close();
            
            $help= <<<EOT
    will generate an output.
    This program uses one(or more) of the Lobe classes to generate different
    data, structure or output.
EOT;
            $help.= "\n    Currently installed Lobes:\n".implode("", $options);
            
			$this->setCommandName('generate')
				 ->setDescription('Generates different outputs')
				 ->setRestrict(true)
                 ->setAction('action')
				 ->setHelp($help);
            
            $this->addRequiredArgument('lobe', 'Lobe to be used');
            $this->addOptionalArgument('param', 'A param for that command');
            $this->addOptionalArgument('detail', 'A detail for that command');
            $this->addOptionalArgument('optional', 'An optional argument');
            $this->addOptionalArgument('extra', 'Extra data to be passed');
            
            $this->init();
		}

		public function action()
		{
			if(!isset($_SESSION['currentProject']))
			{
				Mind::write('currentProjectRequired');
				Mind::write('currentProjectRequiredTip');
				return false;
			}
            Mind::write('thinking');
			if($exec= Mind::$gosh->generate(Array(
									$this->lobe,
									$this->param,
									$this->detail,
									$this->optional,
									$this->extra
								  )))
                return false;
			return $this;
		}
	}
