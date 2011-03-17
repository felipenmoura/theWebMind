<?php

	// setting the general helperSet
	$helperSet= false;
	$helperSet= ($helperSet) ?: new Symfony\Component\Console\Helper\HelperSet();
    $app->setHelperSet($helperSet);
	
	if(isset($_SERVER['argv']))
	{
		$params= $_SERVER['argv'];
		array_shift($params);
	}

	
	/* let's load the plugins, if they are allowed */
	Mind::$triggers= array_keys($app->getCommands());
	
	$sh= new Symfony\Component\Console\Shell($app);
	$sh->run();