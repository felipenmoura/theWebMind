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
		self::$content= '<?php
	$_REQ= Array();
	$_REQ["env"]= "shell";
	define("_MINDSRC_", "'.getcwd().'");
    
    
    if(sizeOf($_SERVER["argv"])>0 && isset($_SERVER["argv"][1])){
        require("'.getcwd().'/mind");
    }else{
        require("'.getcwd().'/mind3rd/API/utils/utils.php");
    }
';

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
		echo shell_exec("sudo chmod +x /bin/mind");
        return true;
	}

    /**
     * Remove the executable file in Unix Bases OSs.
     * 
     * This method will *NOT* drop database or remove project's files.
     * The user must use it with sudo privilegies.
     */
    public static function uninstall($rem){
        if(self::isInstalled()){
            if($ret = shell_exec("sudo rm /bin/mind") == ''){
                if(!$rem){
                    echo "Mind was successfully uninstalled.\n";
                    echo "Please note that the database of project's folders weren't removed!\nIf you want to remove them, execute\n   sudo ./mind remove\n";
                }
            }else{
                echo $ret;
                return false;
            }
        }else{
            echo "No previous installed version detected!\n";
        }
        return true;
    }
    
    public static function remove(){
        
        $fp = fopen('php://stdin', 'r');
        echo "Are you sure you want to uninstall mind and ALSO REMOVE ITS DATA?\n";
        echo "    *** By duing so, you will loose all projects, users and history ***\n        [yes/no]: ";
        $answer = trim(fgets($fp, 1024));
        
        if($answer != 'yes'){
            echo "Not removed\n";
            return false;
        }
        
        if(self::isInstalled()){
            if(self::uninstall(true)){
                self::removeDataBase();
                self::clearProjects();
            }else{
                echo "Failed trying to uninstall!";;
                return false;
            }
        }else{
            echo "No previous installed version detected!\n";
        }
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