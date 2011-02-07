<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
/**
 * Description of Info
 *
 * @author felipe
 */
class Info extends MindCommand implements program
{
    public function configure()
	{
		$this
				->setName('info')
				->setDescription('Performs some tests on theWebMind')
				->setRestrict(true)
				->setDefinition(array())
				->setHelp(<<<EOT
		Executes specific tests and report their results, about the system itself
EOT
				);
	}
	public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		if(!parent::execute($input, $output))
			return false;
		$this->runAction();
	}

	public function HTTPExecute()
	{
		$this->runAction();
	}

	private function action()
	{
		GLOBAL $_MIND;
		if(!parent::verifyCredentials())
			return false;
		print_r($_MIND->about);
		return $this;
	}

	public function runAction()
	{
		$ret= $this->action();
		parent::runAction();
		return $ret;
	}
}