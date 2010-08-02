<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	if($_POST)
	{
		$data= json_decode(($_POST['json']));
		if(!$data)
		{
			$data= json_decode((stripslashes($_POST['json'])));
			if(!$data)
				die('invalid Object');
		}
		
		$data= $_MIND['fw']->objectToArray($data);
		
		$data['subversion']= $data['version'][1];
		$data['update']= $data['version'][2];
		$data['version']= $data['version'][0];
		
		$p= new Project($data['name'], $_SESSION['user']['login']);
		
		$p->populate($data);
		
		if($p->run())
		{
			echo JSON_encode($p->knowledge);
		}else{
				echo JSON_encode($_MIND['fw']->errorOutput(3));
			 }
	}else{
			echo 'Not allowed';
		 }
?>