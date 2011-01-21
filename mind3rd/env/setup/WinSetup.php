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

	public function createExecFile()
	{
		self::$content= '';
		// TODO: any good soul to help with it?
	}

    public function install(){
		self::createExecFile();
		self::createDatabase();
	}
}
?>
