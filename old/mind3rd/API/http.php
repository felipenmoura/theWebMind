<?php
    print_r($_REQ['data']);
	if(isset($_REQ['data']) && sizeof($_REQ['data'])>0)
	{
		$program= preg_replace("/['\"\\\.\/]/", '', $_REQ['data'][0]);
		//$params= $_REQ['data'];
		//echo '<hr/>'; print_r($_REQ['data']); echo '<hr/>';
		for($i=1; $i<sizeof($_REQ['data']); $i++)
		{
			$params[]= $_REQ['data'][$i];
		}
	}
        if(!isset($_SESSION['auth']) && $program != 'autenticate')
	{
		$_MIND->write('not_allowed');
		exit;
	}

	if(file_exists('mind3rd/API/programs/'.$program.".php"))
	{
		include('mind3rd/API/programs/'.$program.".php");
		$program= new $program($params);
		echo "\n";
		exit;
	}else{
		//echo "No such function!\nType mind -h  or mind command -h for help\n";
		$_MIND->write('http_no_such_file');
             }