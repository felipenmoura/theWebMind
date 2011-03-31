<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;
	
/**
 * Description of Info
 *
 * @author felipe
 */
class DQB extends MindCommand implements program
{
    public function configure()
	{
		$this
				->setName('dqb')
				->setDescription('Performs some tests on theWebMind')
				->setRestrict(true)
				->setDefinition(array(
					new InputArgument('query',
									  InputArgument::REQUIRED,
									  'Options: create, drop, alter, insert, delete, select and update'),
					new InputArgument('table',
									  InputArgument::REQUIRED,
									  "Which table will have its query built. Use * to see them all."),
				))
				->setHelp(<<<EOT
		This program will create the needed query to the selected database.
		Notice that it will NOT execute then, only return them as a string.
EOT
				);
	}
	public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		if(!parent::execute($input, $output))
			return false;
		$this->query= $input->getArgument('query');
		$this->table= $input->getArgument('table');
		$this->runAction();
	}

	public function HTTPExecute()
	{
		GLOBAL $_REQ;
		if(!parent::HTTPExecute())
			return false;
		if(isset($_REQ['data']['query']) && isset($_REQ['data']['table']))
		{
			$this->query= $_REQ['data']['query'];
			$this->table= $_REQ['data']['table'];
		}
		$this->runAction();
	}

	private function action()
	{
		GLOBAL $_MIND;
		if(!parent::verifyCredentials())
			return false;
		
		switch($this->query)
		{
			case 'create':
			case 'c':
				$this->query= 'createTable';
				break;
			case 'select':
			case 's':
			case 'query':
				$this->query= 'select';
				break;
			case 'delete':
			case 'del':
			case 'd':
				$this->query= 'delete';
				break;
			case 'insert':
			case 'ins':
			case 'i':
				$this->query= 'insert';
				break;
			case 'update':
			case 'upd':
			case 'u':
				$this->query= 'update';
				break;
		}
		
		\DQB\QueryFactory::setUp(Mind::$currentProject['database_drive']);
		\DQB\QueryFactory::buildQuery($this->table, $this->query);
		
		\DQB\QueryFactory::$showHeader= true;
		\DQB\QueryFactory::showQueries();
		return $this;
	}

	public function runAction()
	{
		$ret= $this->action();
		parent::runAction();
		return $ret;
	}
}