<?php
/**
 * This class represents the structure required to use L10N
 *
 * @author felipe
 */
class En {
	private $messages= Array();
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
	public function __construct()
	{
		$this->messages['passwordRequired']	= "I need a password for this user, please: ";
		$this->messages['autenticated']		= Mind::message("\nMain: %s autenticated", "[OK]", false);//"\n[OK] %s autenticated\n";
		$this->messages['not_allowed']		= Mind::message("\nMain: You have not autenticated your credentials yet", '[Fail]', false);
		$this->messages['not_allowed_tip']	= "Try calling the command\n    auth < login >\nA password will be required.\n";
		$this->messages['no_such_file']		= Mind::message("\nMain: No such command '%s'", "[Fail]", false);
	}
}
?>
