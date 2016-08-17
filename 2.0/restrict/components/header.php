<?php
	header ('Content-type: text/html; charset=utf-8');
	if($_MIND['sessionDir'] != 'default')
		session_save_path($_MIND['sessionDir']);
	session_start($_MIND['sessionLife']);
	if(!$_SESSION['user']['login'])
	{
		echo "<b><span style='color:red;'>Permission Denied</span></b>";
		exit;
	}
	reset($_POST);
	foreach($_POST as $p)
	{
		$_POST[key($_POST)]= @$_MIND['fw']->treatClientInfo($_POST[key($_POST)]);
		next($_POST);
	}
	//ini_set('display_errors', 0);
?>