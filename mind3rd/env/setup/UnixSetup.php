<?php
require('Setup.php');
/**
 * This class is responsable for the setup/installation
 * of the system on Unix based OSs
 *
 * @author felipe
 */
abstract class UnixSetup extends Setup{
	public static $header = '#!/usr/bin/env php';
	public static $content= '';

	/**
	 * Creates the mind file, at bin directory
	 */
	public function createExecFile()
	{
		self::$content= '<?php
	$_REQ= Array();
	$_REQ["env"]= "shell";
	define("_MINDSRC_", "'.getcwd().'");
	require("'.getcwd().'/mind3rd/API/utils.php");';

		echo "  starting the installation...\n";
		echo "  creating the file...\n";
		shell_exec("sudo touch /bin/mind");
		echo "  writing the header...\n";
		shell_exec("sudo echo '".self::$header."' > ".
				   "/bin/mind");
		echo "  writing the main commands...\n";
		echo shell_exec("sudo echo '".self::$content."' >>".
						"/bin/mind;");
		echo "  setting permissions...\n";
		echo shell_exec("sudo chmod 777 /bin/mind");
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
