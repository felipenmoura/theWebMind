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

	/**
	* function taken from: http://www.dasprids.de/blog/2008/08/22/getting-a-password-hidden-from-stdin-with-php-cli
	* this method should read the passwords from console, not showing any character
	* or replacing them by stars(asterisks)
	* @method readPassword
	* @param Boolan $stars if true, show an * for each typed char
	* @return String password
	*/
	public static function readPassword($stars)
	{
		// Get current style
		$oldStyle = shell_exec('stty -g');

		if ($stars === false) {
			shell_exec('stty -echo');
			$password = rtrim(fgets(STDIN), "\n");
		} else {
			shell_exec('stty -icanon -echo min 1 time 0');

			$password = '';
			while (true) {
				$char = fgetc(STDIN);

				if ($char === "\n") {
					break;
				} else if (ord($char) === 127) {
					if (strlen($password) > 0) {
						fwrite(STDOUT, "\x08 \x08");
						$password = substr($password, 0, -1);
					}
				} else {
					fwrite(STDOUT, "*");
					$password .= $char;
				}
			}
		}

		// Reset old style
		shell_exec('stty ' . $oldStyle);

		// Return the password
		return $password;
	}
}