<?php
	require(_MINDSRC_.'/mind3rd/API/external/Symfony/Component/Console/Shell.php');
	require(_MINDSRC_.'/mind3rd/API/external/Symfony/Component/Console/Application.php');
	require(_MINDSRC_.'/mind3rd/API/external/Symfony/Component/Console/Command/Command.php');
	
	use Symfony\Component\Console\Application;
	use Symfony\Component\Console\Helper\HelperSet;

	$app= new Symfony\Component\Console\Application('mind');
	$app->addCommands(Array(
		new RunTest(),
		new Quit(),
		new Auth(),
		new Clear()
	));
	
	$helperSet= false;
	$helperSet = ($helperSet) ?: new Symfony\Component\Console\Helper\HelperSet();
    $app->setHelperSet($helperSet);
	
	include_once('external/Symfony/Component/Console/Shell.php');
	$sh= new Symfony\Component\Console\Shell($app);
	$sh->run();
	
	
	
	
	
	
	
	
	
	
	
		/**
		* This file receives the shell call for a program,
		* and parses its parameters, calling the program from the API
		*
		*/

		/*

        function shellExecute($command)
        {
            GLOBAL $_MIND;
			readline_completion_function('mmindAutoComplete');
            if(!is_array($command))
            	$command= explode(' ', $command);
            $program= array_shift($command); // the first parameter is the program itself
            try
            {
            	$program= new $program($command);
            	//echo "\n";
            }catch(Exception $e){
            	print_r($e->getMessage());
            }
        }

        $fp = fopen("php://stdin", "r");
        $in = '';
        if(isset($params[0]))
		{
		    if($params[0]== 'help' || $params[0]== '-h' || $params[0]== '--help')
		    {
		    	new help();
		    	exit;
		    }elseif($params[0]== '-u' && isset($params[1]))
		    	 {
		    		new autenticate(Array($params[1]));
		    	 }
		}else
			new clear();
        echo "Welcome to mind3rd:\nType help to see the help content\n";
        while($in != "exit")
        {
        	if(!isset($_SESSION['login']))
        		echo "mind > ";
        	else
            	echo $_SESSION['login'].'@mind > ';
            $in=trim(fgets($fp));
            if($in!='exit' && trim($in)!='')
            {
                shellExecute($in);
            }
        }
        new clear();
        exit;
        */
