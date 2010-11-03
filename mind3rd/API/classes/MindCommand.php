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

	public function setFileName($fName)
	{
		$this->fileName= $fName;
		return $this;
	}
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
	public function __construct($name = null)
	{
		parent::__construct($name);
	}
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
	public function execute(Console\Input\InputInterface $input,
							Console\Output\OutputInterface $output)
	{
		return $this->verifyCredentials();
	}
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
