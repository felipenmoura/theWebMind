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
		// inspired on Doctrine bat for windows
		self::$content= <<<BAT
   @echo off

if "%PHPBIN%" == "" set PHPBIN=@php_bin@
if not exist "%PHPBIN%" if "%PHP_PEAR_PHP_BIN%" neq "" goto USE_PEAR_PATH
GOTO RUN
:USE_PEAR_PATH
set PHPBIN=%PHP_PEAR_PHP_BIN%
:RUN
"%PHPBIN%" "@bin_dir@\mind" %*
BAT;
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
