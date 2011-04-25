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
	
/**
 * @author felipe
 */
class Info extends MindCommand implements program
{
    public function __construct()
	{
		$this
				->setCommandName('info')
				->setDescription('Performs some tests on theWebMind')
				->setRestrict(true)
                ->setAction('action')
				->setHelp(<<<EOT
		Executes specific tests and report their results, about the system itself
EOT
				);
        $this->init();
	}

	public function action()
	{
		GLOBAL $_MIND;
		print_r($_MIND->about);
		return $this;
	}
}