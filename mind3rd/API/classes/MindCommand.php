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
	private $restrict                = true;
	private $fileName                = null;
	private $requiredArguments       = Array();
	private $optionalArguments       = Array();
	private $requiredOptions         = Array();
	private $optionalOptions         = Array();
	private $commandFlags            = Array();
	public  $answers                 = Array();
	public  $commandAvailableOptions = Array();
    
    public function init()
    {
        parent::__construct();
    }
    
    public function configure()
	{
        $this->setDefinition(Array());
        $this->setName($this->commandName);
        
        $definition= Array();
        $definition= array_merge($this->requiredArguments,
                                 $this->requiredOptions,
                                 $this->optionalArguments,
                                 $this->optionalOptions,
                                 $this->commandFlags);
        
        $helpDetails= "\n";
        $avOptsStr= Array();
        foreach($this->commandAvailableOptions as $k=>$avOpts)
        {
            if($avOpts)
            {
                $avOptsStr[]= "    ->".$k."\n        ".implode(', ', $avOpts);
            }
        }
        if(sizeof($avOptsStr)>0)
            $helpDetails.= "\nAvailables options:\n".implode("\n", $avOptsStr);
        $this->setHelp($this->getHelp().$helpDetails);
        
        $this->setDefinition($definition);
    }
    
    public function prompt($name, $question, $mode=false)
    {
		GLOBAL $_REQ;
        $secret= false;
        $options= false;
        
        if($mode)
        {
            if(is_array($mode))
            {
                $options= $mode;
            }else
                $secret= true;
        }
        
        $answer= null;
		if($_REQ['env'] !='http')
        {
            do
            {
                echo $question."\n";
                if($options)
                {
                    echo "(";
                    $optionLegend= Array();
                    foreach($options as $optVal=>$optLabel)
                    {
                        $optionLegend[]= $optVal."=".$optLabel;
                    }
                    echo trim(implode(" |", $optionLegend));
                    echo ")\n";
                }
                if(!$secret)
                {
                    $fp = fopen('php://stdin', 'r');
                    $answer = trim(fgets($fp, 1024));
                        
                    if($options &&
                       !in_array(strtolower($answer),
                                 array_map('strtolower', array_keys($options))))
                    {
                        Mind::write('invalidOptionValue', true, $answer, $name);
                        $answer= false;
                    }
                    
                }else{
                        $answer= $this->readPassword('*');
                     }
            }while(!$answer);
        }else{
                if(isset($_POST[$name]))
                {
                    $answer= $_POST[$name];
                    
                    if($options &&
                       !in_array(strtolower($answer),
                                  array_map('strtolower', $options)))
                    {
                        Mind::write('invalidOptionValue', true, $answer, $name);
                       $answer= false;
                    }
                }
                if(!$answer)
                {
                    Mind::write('missingParameter', true, $name);
                    exit;
                }
             }
        $this->answers[$name]= trim($answer);
        return $this->answers[$name];
    }
    
    public function addRequiredArgument($argName,
                                        $description='',
                                        $availableOptions=null)
    {
        if($availableOptions)
            $description.= "(".implode(', ', $availableOptions).")";
        $this->requiredArguments[$argName]= new InputArgument($argName,
                                                      InputArgument::REQUIRED,
                                                      $description);
        $this->commandAvailableOptions[$argName]= $availableOptions;
        return $this;
    }
    public function addOptionalArgument($argName,
                                        $description='',
                                        $availableOptions=null)
    {
        if($availableOptions)
            $description.= "(".implode(', ', $availableOptions).")";
        $this->optionalArguments[$argName]= new InputArgument($argName,
                                                      InputArgument::OPTIONAL,
                                                      $description);
        $this->commandAvailableOptions[$argName]= $availableOptions;
        return $this;
    }
    public function addRequiredOption($argName,
                                      $shortCut=null,
                                      $description='',
                                      $default=null,
                                      $availableOptions=null)
    {
        if($availableOptions)
            $description.= "(".implode(', ', $availableOptions).")";
        $this->requiredOptions[$argName]= new InputOption($argName,
                                                  $shortCut,
                                                  InputOption::PARAMETER_REQUIRED,
                                                  $description,
                                                  $default);
        $this->commandAvailableOptions[$argName]= $availableOptions;
        return $this;
    }
    public function addOptionalOption($argName,
                                      $shortCut=null,
                                      $description='',
                                      $default=null,
                                      $availableOptions=null)
    {
        if($availableOptions)
            $description.= "(".implode(', ', $availableOptions).")";
        $this->optionalOptions[$argName]= new InputOption($argName,
                                                  $shortCut,
                                                  InputOption::PARAMETER_OPTIONAL,
                                                  $description,
                                                  $default);
        $this->commandAvailableOptions[$argName]= $availableOptions;
        return $this;
    }
    
    public function addFlag($argName,
                            $shortCut=null,
                            $description='',
                            $availableOptions=null)
    {
        if($availableOptions)
            $description.= "(".implode(', ', $availableOptions).")";
        $this->commandFlags[$argName]= new InputOption($argName,
                                               $shortCut,
                                               InputOption::PARAMETER_NONE,
                                               $description);
        $this->commandAvailableOptions[$argName]= $availableOptions;
        return $this;
    }
    
    public function setCommandName($commandName)
    {
        $this->commandName= $commandName;
        return $this;
    }
    
    public function description($description)
    {
        $this->description= $description;
        return $this;
    }
    
    public function help($helpContent)
    {
        $this->helpContent= $helpContent;
        return $this;
    }
    
    public function setAction($action)
    {
        $this->commandAction= $action;
        return $this;
    }
    
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
				if($plugin->active !== false)
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
		if(!$this->verifyCredentials())
           return false;
        
        foreach($input->getArguments() as $k=>$arg)
        {
            $this->$k= $arg;
        }
        foreach($input->getOptions() as $k=>$opt)
        {
            $this->$k= $opt;
        }
        
        $this->runAction();
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
			if(!$this->verifyCredentials())
               return false;
            
            foreach($_REQ['data'] as $k=>$arg)
            {
                $this->$k= $arg;
            }
            
            $this->runAction();
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

	/**
	 * This method will execute the plugins that should run AFTER
	 * the execution of the program, so, call parent::runAction AFTER
	 * each program::runAction command blocks
	 */
	public function runAction(){
        
        $this->runPlugins('before');
        
        /*echo "\n\n";
        print_r($this->commandAvailableOptions);
        echo "\n\n";*/
        
        foreach($this->commandAvailableOptions as $k=>$avOpts)
        {
            if($avOpts && !in_array(strtolower($this->$k),
                                    array_map('strtolower', $avOpts)))
            {
                Mind::write('invalidOptionValue', true, $this->$k, $k);
                return false;
            }
        }
        
        // yea, I know it looks a bit crazy!
        if(is_string($this->commandAction))
            $this->{$this->commandAction}();
        else
            call_user_func($this->commandAction, $this);
        
        
        $this->runPlugins('after');
	}
    
    public function __set($what, $value)
    {
        $this->$what= $value;
    }
}