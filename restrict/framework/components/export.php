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
		if(isset($_GET['onlyDDL']))
			$p->export($_GET['onlyDDL']);
		else
			$p->export();
	}else{
			echo "top.Mind.Dialog.ShowError(".JSON_encode($_MIND['fw']->errorOutput(2)).")";
		 }
?>