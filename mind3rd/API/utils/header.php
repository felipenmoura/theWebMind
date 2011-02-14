<?php
	session_start();
	define('_CONSOLE_LINE_LENGTH_', 80);
	require(_MINDSRC_.'/mind3rd/API/classes/Mind.php');

	if(!Mind::isInstalled())
	{
		//include(_MINDSRC_.'/wizard/installation-1.php');
		exit;
	}

	Mind::autoloadRegisterPath(Array(
			_MINDSRC_.'/mind3rd/API/external/',
			_MINDSRC_.'/mind3rd/API/cortex/Lexer/',
			_MINDSRC_.'/mind3rd/API/interfaces/',
			_MINDSRC_.'/mind3rd/API/programs/',
			_MINDSRC_.'/mind3rd/API/L10N/',
			_MINDSRC_.'/mind3rd/API/classes/',
			_MINDSRC_.'/mind3rd/API/cortex/tokenizer/',
			_MINDSRC_.'/mind3rd/API/cortex/canonic/',
			_MINDSRC_.'/mind3rd/API/cortex/syntaxer/',
			_MINDSRC_.'/mind3rd/API/cortex/analyst/',
			_MINDSRC_.'/mind3rd/API/languages/'
		));

	function __autoload($what)
	{
		GLOBAL $_MIND;
		$what= preg_replace("/[ '\"\.\/]/", '', $what);
		$what= str_replace('\\', '/', $what);

		if(file_exists(_MINDSRC_.'/mind3rd/API/programs/'.$what.'.php'))
		{
			include(_MINDSRC_.'/mind3rd/API/programs/'.$what.'.php');
			return true;
		}
		if(strpos($what, '\\')>=0)
		{
			$what= explode('\\', $what);
			$what= array_pop($what);
		}

		$dirs= Mind::$autoloadPaths;

		for($i=0; $i<sizeof($dirs); $i++)
		{
			if(file_exists($dirs[$i].$what.'.php'))
			{
				require_once($dirs[$i].$what.'.php');
				return true;
			}
		}

		// let's check if it is a language
		$langPath= _MINDSRC_.'/mind3rd/API/languages/'.$_MIND->defaults['default_human_languageName'].'/'.$what.'.php';
		if(file_exists($langPath))
		{
			require_once($langPath);
			return true;
		}

		echo " [ERROR] Class not found: ".$what."\n";
		return false;
	}