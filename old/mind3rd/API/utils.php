<?php
	/**
	* This file corresponds to the bootstrap.
	* Every requisition should pass here
	* This file decides which environment will be used
	* and also some permissions, starting some environmental
	* variables, such as language for localization
	*/
	session_start();
	function __autoload($class)
	{
		GLOBAL $_MIND;
		$class= preg_replace("/[ '\"\\\.\/]/", '', $class);
		if(!isset($_SESSION['auth']) && $class != 'autenticate' && $class != 'help' && $class != 'clear')
        {
            $_MIND->write('not_allowed');
            throw new Exception('');
            return false;
        }
		$f= _MINDSRC_.'/mind3rd/API/programs/'.$class.'.php';
		if(file_exists($f) && !class_exists($class))
			require $f;
		else{
				$f= _MINDSRC_.'/'.$class.'.php';
				if(file_exists($f) && !class_exists($class))
					require $f;
				else{
						throw new Exception($_MIND->write('shell_no_such_file', false));
						return false;
					}
			}
		return true;
	}
	require('interfaces/program.php');
	require('classes/Mind.php');
	if(isset($_SERVER['argv']))
	{
		$params= $_SERVER['argv'];
		array_shift($params);
	}
	$_MIND= new Mind();
	if($_REQ['env']=='shell')
            include('shell.php');
	else
            include('http.php');
