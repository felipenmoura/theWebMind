<?php
	/**
	* This file corresponds to the bootstrap.
	* Every requisition should pass here
	* This file decides which environment will be used
	* and also some permissions, starting some environmental
	* variables, such as language for localization
	*/
	require(_MINDSRC_.'/mind3rd/API/utils/header.php');
	include(_MINDSRC_.'/mind3rd/API/utils/constants.php');
	
	// Instantiating the main Mind class
	/**
	 * @global Mind $_MIND  This variable contains many information about the proect, the system and also have some methods an attributes to deal with such data
	 */
	$_MIND= new Mind();

	if($_MIND->defaults['plugins']==1)
	{
		require(_MINDSRC_.'/mind3rd/API/interfaces/plugin.php');
		$d = dir(_MINDSRC_.'/mind3rd/API/plugins');
		while(false !== ($entry = $d->read()))
		{
			if(substr($entry, 0, 1) !=  '.')
			{
				include(_MINDSRC_.'/mind3rd/API/plugins/'.$entry.'/'.$entry.'.php');
				Mind::addPlugin(new $entry());
			}
		}
		$d->close();
	}
	
	
	define('SYSTEM_NAME', 'mind');
	
	
	$app= new Symfony\Component\Console\Application(SYSTEM_NAME);
	$app->addCommands(Array(
		new RunTest(),
		new Quit(),
		new Auth(),
		new Clear(),
		new Commit(),
		new Info(),
		new Create(),
		new Show(),
		new Analyze(),
		new SetUse(),
		new dqb(),
		new Generate()
	));
	
	if($_REQ['env']=='shell')
            include('shell.php');
	else
            include('http.php');