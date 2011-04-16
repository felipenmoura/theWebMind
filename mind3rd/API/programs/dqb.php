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
    public $query=null;
    public $table=null;
    
    public function __construct()
	{
		$this
				->setCommandName('dqb')
				->setDescription('Performs some tests on theWebMind')
				->setRestrict(true)
                ->setAction('action')
				->setHelp(<<<EOT
		This program will create the needed query to the selected database.
		Notice that it will NOT execute then, only return them as a string.
EOT
				);
        
        //$this->addRequiredArgument('query', 'Options: create, drop, alter, insert, delete, select and update');
        $this->addRequiredArgument('table', 'Which table will have its query built. Use * to see them all.');
        
        $this->init();
	}

	public function action()
	{
		GLOBAL $_MIND, $_REQ;
		if(!parent::verifyCredentials())
			return false;
		
        // for now, only the create has been developed
        // even if the following options are already described there are not
        // plans to build them so soon.
        $this->query= 'c';
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
		
		$qrs= \MindProject::showSQL(($this->table=='*'), $this->table, $this->query);
		echo $qrs;
		return $this;
	}
}