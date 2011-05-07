<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */

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
	
	try {
        $sh= new Symfony\Component\Console\Shell($app);
        $sh->run();
    }catch(Exception $exc) {

        echo "   <[ERROR] It looks like you are not using the readline extension enabled!\n";
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
        {
            echo "   Sorry! The readline extension is not available for windows(!)\n";
            echo "   So, you will not be able to use the application through command line\n\n";
        }
        else
            echo "   Please, follow these instructions to install it: http://goo.gl/UDrEY\n\n";
        echo "   NOTE: Even without the readline extension, you already can use the\n";
        echo "         system via HTTP once it does not need the shell to work\n\n";
    }