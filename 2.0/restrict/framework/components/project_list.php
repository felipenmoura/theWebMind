<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	$projectList= Project::getProjects($_SESSION['user']['login']);
	echo ($projectList)? jSon_encode($projectList): 'false';
?>