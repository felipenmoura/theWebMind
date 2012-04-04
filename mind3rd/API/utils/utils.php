<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     *
     * This file corresponds to the bootstrap.
     * Every requisition should pass here
     * This file decides which environment will be used
     * and also some permissions, starting some environmental
     * variables, such as language for localization
     *
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */

	require(_MINDSRC_.'/mind3rd/API/utils/header.php');

	if(!Mind::isInstalled()){
		if($_REQ['env']=='shell'){
			echo "You have to install the application!\nTo install it, please run: sudo php mind install";
		}else{
			header('location:setup.php');
		}
		exit;
	}

	include(_MINDSRC_.'/mind3rd/API/utils/constants.php');
	require(_MINDSRC_.'/mind3rd/env/setup/Setup.php');

	/**
	 * @global Mind $_MIND  This variable contains many information about the proect, the system and also have some methods an attributes to deal with such data
	 */
	$_MIND= new Mind();

    // we will load the plugins if they are allowed
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

	// building the application
	define('SYSTEM_NAME', 'mind');
	$app= new Symfony\Component\Console\Application(SYSTEM_NAME);

    // defining the programs/commands to be used
    $programs= Array();
    $d = dir(_MINDSRC_.'/mind3rd/API/programs');
    while(false !== ($entry = $d->read()))
    {
        if(substr($entry, 0, 1) !=  '.')
        {
            $entry= str_replace('.php', '', $entry);
            $prog= new $entry();
            $programs[strtolower($prog->getName())]= $prog;
        }
    }
    $d->close();
    $app->addCommands($programs);
    \MIND::$programs= $programs;
    // starting the application
	if($_REQ['env']=='shell'){
		include(_MINDSRC_.'/mind3rd/API/shell.php');
	}else{
			include(_MINDSRC_.'/mind3rd/API/http.php');
	}