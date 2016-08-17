<?php
	include('config/mind.php');
	include($_MIND['framework']);
	include($_MIND['header']);
	
	$_MIND['fw']->mountIde();
	$_MIND['fw']->output();
	exit;
?>