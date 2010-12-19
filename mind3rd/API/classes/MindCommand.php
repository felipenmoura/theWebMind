<?php
	use Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputOption,
		Symfony\Component\Console;

/**
 * This class extends the Command class from Symfony
 * All the program should extend it
 *
 * @author felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
class MindCommand extends Symfony\Component\Console\Command\Command
{
	private $restrict= true;
	private $fileName= null;

	/**
	 * Specifies the name of the file, included with the program
	 * @param String $fName
	 * @return MindCommand
	 */
	public function setFileName($fName)
	{
		$this->fileName= $fName;
		return $this;
	}

	/**
	 * Gets the name of the file which the program is refered to
	 * @method getFileName
	 * @return String
	 */
	public function getFileName()
	{
		return $this->fileName!=null? $this->fileName: $this->getName();
	}

	/**
	 * Sets the restrict property
	 * 
	 * @param Boolean $b
	 * @return MindCommand Itself
	 */
	public function setRestrict($b)
	{
		$this->restrict= $b;
		return $this;
	}

	/**
	 * Construct
	 * @param String $name
	 */
	public function __construct($name = null)
	{
		parent::__construct($name);
	}

	/**
	 * Verifies if the user has already registered or not
	 * according to the specifications of each program
	 *
	 * @method verifyCredentials
	 * @return Boolean
	 */
	public function verifyCredentials()
	{
		if($this->restrict)
			if(!isset($_SESSION['auth']))
			{
				Mind::write('not_allowed');
				Mind::write('not_allowed_tip');
				return false;
			}
		return true;
	}

	/**
	 * Calls the pluggins that should run on
	 * specific already registered events
	 *
	 * @method runPlugins
	 * @param String $evt
	 * @return void
	 */
	public function runPlugins($evt)
	{
		if(isset(Mind::$pluginList[$this->name]))
		{
			foreach(Mind::$pluginList[$this->name][$evt] as $plugin)
			{
				$plugin->run($this);
			}
		}
	}

	/**
	 * Calls the program using the cosole interface
	 *
	 * @method execute
	 * @param Console\Input\InputInterface $input
	 * @param Console\Output\OutputInterface $output
	 * @return Boolean
	 */
	public function execute(Console\Input\InputInterface $input,
							Console\Output\OutputInterface $output)
	{
		return $this->verifyCredentials();
	}

	/**
	 * Calls the program by the HTTP interface
	 * @method HTTPExecute
	 * @global Array $_REQ
	 * @return Boolean
	 */
	public function HTTPExecute()
	{
		GLOBAL $_REQ;
		if($_REQ['env'] =='http')
		{
			return $this->verifyCredentials();
		}
	}
}
?>
