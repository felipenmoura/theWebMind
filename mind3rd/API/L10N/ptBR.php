<?php
/**
 * This class represents the structure required to use L10N
 *
 * @author felipe
 */
class ptBR {
	private $messages= Array();

	public $name= 'pt-BR';

	/**
	 * This method returns the translated message
	 * @method getMessage
	 * @param String $msg
	 * @return String Returns the string translated, or fals if the required message does not exist
	 */
    public function getMessage($msg)
	{
		if(isset($this->messages[$msg]))
			return $this->messages[$msg];
		else
			return false;
	}

	/**
	 * Construct
	 * This method should start all the messages, setting them
	 * to the class::$messages
	 */
	public function __construct()
	{
		//header('Content-type: text/html; charset=iso-8859-1');
		$this->messages['programRequired']			= Mind::message("API: You must send the program name, to execute", '[Fail]', false);
		$this->messages['loginRequired']			= Mind::message("Auth: Both login and password are required", '[Fail]', false);
		$this->messages['passwordRequired']			= "I need a password for this user, please: ";
		$this->messages['autenticated']				= Mind::message("\nMain: %s autenticated", "[OK]", false);//"\n[OK] %s autenticated\n";
		$this->messages['not_allowed']				= Mind::message("\nMain: You have not autenticated your credentials yet", '[Fail]', false);
		$this->messages['not_allowed_tip']			= "Try calling the command\n    auth < login >\nA password will be required.\n";
		$this->messages['no_such_file']				= Mind::message("\nMain: No such command '%s'", "[Fail]", false);
		$this->messages['auth_fail']				= Mind::message("\nAuth: Wrong user or password", "[Fail]", false);
		$this->messages['bye']						= "Logging out...\n";
		$this->messages['invalidCreateParams']		= Mind::message("Main: Invalid parameters", "[Fail]", false);
		$this->messages['invalidOption']			= Mind::message("Invalid option '%s'", '[Fail]', false);
		$this->messages['projectAlreadyExists']		= Mind::message("There is, already, another project with the same name", '[Fail]', false);
		$this->messages['projectCreated']			= Mind::message("Created project '%s'", '[Ok]', false);
		$this->messages['userCreated']				= Mind::message("Created user '%s'", '[Ok]', false);
		$this->messages['noProject']				= Mind::message("Project '%s' doesn't exist or you have no access", '[Fail]', false);
		$this->messages['projectOpened']			= Mind::message("Project '%s' opened", '[Ok]', false);
		$this->messages['currentProjectRequired']	= Mind::message("You must open a project first", '[Fail]', false);
		$this->messages['currentProjectRequiredTip']= "You can use the command\n  use project <projectName>\n";

		$this->messages['http_invalid_requisition']	= <<<MESSAGE
   Invalid HTTP requisition.
   You *must* send some POST data acoording your request, and also a variable "program" by post, with the name of the program you want to run.
MESSAGE;
	}
}
?>
