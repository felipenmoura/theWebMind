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
	
	$app= new Symfony\Component\Console\Application('mind');
	$app->addCommands(Array(
		new RunTest(),
		new Quit(),
		new Auth(),
		new Clear(),
		new Info(),
		new Create(),
		new Show(),
		new Analyze(),
		new SetUse()
	));

	// setting the general helperSet
	$helperSet= false;
	$helperSet= ($helperSet) ?: new Symfony\Component\Console\Helper\HelperSet();
    $app->setHelperSet($helperSet);
	
	if(isset($_SERVER['argv']))
	{
		$params= $_SERVER['argv'];
		array_shift($params);
	}

	// Instantiating the main Mind class
	$_MIND= new Mind();

	/* let's load the plugins, if they are allowed */
	Mind::$triggers= array_keys($app->getCommands());
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
	
	if($_REQ['env']=='shell')
            include('shell.php');
	else
            include('http.php');