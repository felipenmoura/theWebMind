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
            /**
             * This could be beautiful, but unfortunately, PHP has not support
             * for readline on windows! Then, on windows, the system will run
             * ONLY with HTTP requisitions...sorry guys!
             */
            // <editor-fold defaultstate="collapsed" desc="Code responsible for the instalation in windows, but is not in use by now">
            /*
            $runDir= str_replace('cmd.exe', '', getenv('COMSPEC'));
            $phpBin= '';

            if(!isset($_SERVER))
            {
                while(!file_exists($phpBin) || basename($phpBin)!='php.exe')
                {
                    $command= "   [PROMPT] Please type the PHP bin file";
                    if(file_exists('c:/wamp'))
                        $command.="
    eg: \"c:/wamp/bin/php/php5.3.4/php.exe\":\n        ";
                    else
                        echo $command.= "
    eg: \"c:/php/php.exe\":\n        ";
                    echo $command;
                    $fp = fopen('php://stdin', 'r');
                    $phpBin = trim(fgets($fp, 1024));
                }
            }elseif(!isset($_GET['phpBin']))
            {
                echo "To be used in windows, you must provide the php.exe path\n";
                echo "This file will probably be at\n";
                echo "c:/wamp/bin/php/php5.3.4/php.exe\n";
                echo "or\n";
                echo "c:/php/php.exe";
                return false;
            }
            @shell_exec("copy /y NUL ".$runDir."mind.bat >NUL");
            @shell_exec("copy /y NUL ".$runDir."mind3rd.php >NUL");


            $content= $phpBin.' '.$runDir.'mind3rd.php';
            @shell_exec('echo '.$content.' > '.$runDir.'mind.bat');

            $cwd= str_replace('\\', '/', getcwd());

            $phpContent= '$_REQ= Array(); $_REQ["env"]= "shell"; define("_MINDSRC_", "'.$cwd.'"); require("'.$cwd.'/mind3rd/API/utils/utils.php"); ';
            @shell_exec('echo ^<?php '.$phpContent.' > '.$runDir.'mind3rd.php');
            */
            // </editor-fold>
            return true;
	}

	/**
	 * Installs the program to be used in command line
	 * or http.
	 * It uses an inherited method, createDatabase
	 */
    public static function install(){
	if(self::createExecFile())
            return self::createDatabase();
        return false;
	}
}