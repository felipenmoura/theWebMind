<?php
require('Setup.php');
/**
 * This class is responsable for the setup/installation
 * of the system, on Windows
 *
 * @author felipe
 */
abstract class WinSetup extends Setup{
	public static $header = '';
	public static $content= '';

	/**
	 * *SHOULD* create the file to use the program throught
	 * command line, on windows
	 */
	public function createExecFile()
	{
		self::$content= '';
		// TODO: any good soul to help with it?
	}

	/**
	 * Installs the program to be used in command line
	 * or http.
	 * It uses an inherited method, createDatabase
	 */
    public function install(){
		self::createExecFile();
		self::createDatabase();
	}
}
?>
