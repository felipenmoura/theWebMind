<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
if(!class_exists('Setup'))
    require('Setup.php');
/**
 * This class is responsable for the setup/installation
 * of the system on Unix based OSs
 *
 * @author felipe
 */
class UnixSetup extends Setup{
	public static $header = '#!/usr/bin/env php';
	public static $content= '';

	/**
	 * Creates the mind file, at bin directory
	 */
	public static function createExecFile()
	{
        /*
        $uriToAdd= getcwd()."/";
        //echo $uriToAdd."<br/>";
        ///usr/local/bin:/usr/bin:/bin
        //echo shell_exec('echo $PATH');
        echo "<br/>";
        echo getenv('PATH').PATH_SEPARATOR.$uriToAdd;
        echo "<br/>";
        echo shell_exec('expert PATH=$PATH'.PATH_SEPARATOR.$uriToAdd);
        echo shell_exec('echo $PATH');
        echo "<br/>";
        */
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
        return true;
	}

	/**
	 * Installs the program to be used in command line
	 * or http.
	 * It uses an inherited method, createDatabase
	 */
    public static function install(){
        parent::init();
		if(self::createExecFile())
            return self::createDatabase();
        return false;
	}
}