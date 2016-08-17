<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	if(Project::hasProject($_GET['pName']))
	{
		$pName= $_GET['pName'];
		$p= new Project($pName, $_SESSION['user']['login']);
		//$p->process();
		$p->export();
	}else{
			echo "Mind.Dialog.ShowError(".JSON_encode($_MIND['fw']->errorOutput(2)).")";
		 }
?>