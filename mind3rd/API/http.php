<?php
	echo "<b>Requisition data:</b><br/>";
    print_r($_REQ);
	echo "<br/><b>Return:</b><br/>";
	if(isset($_REQ['data']) && sizeof($_REQ['data'])>0)
	{
		$program= preg_replace("/['\"\\\.\/]/", '', $_REQ['data'][0]);
		for($i=1; $i<sizeof($_REQ['data']); $i++)
		{
			$params[]= $_REQ['data'][$i];
		}
	}

	if(file_exists('mind3rd/API/programs/'.ucfirst($program).".php"))
	{
		include('mind3rd/API/programs/'.ucfirst($program).".php");
		//$program= new $program($params);
		$program= new $program($program);
		echo "\n";
		exit;
	}else{
		//echo "No such function!\nType mind -h  or mind command -h for help\n";
			$_MIND->write('no_such_file', true, $program);
         }