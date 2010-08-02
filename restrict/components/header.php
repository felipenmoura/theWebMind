<?php
	header ('Content-type: text/html; charset=utf-8');
	if($_MIND['sessionDir'] != 'default')
		session_save_path($_MIND['sessionDir']);
	session_start($_MIND['sessionLife']);
	if(!isset($_SESSION['user']['login']))
	{
		//echo "<b><span style='color:red;'>Permission Denied</span></b><br/><input type='button' value='Ok' onclick='top.location.href=\"../index.php\";'>";
		//echo "".$_MIND['rootDir']."index.php";
		header("Location:".$_MIND['rootDir']."../index.php");
		exit;
	}
	reset($_POST);
	foreach($_POST as $p)
	{
		if(isset($_POST[key($_POST)])){
			$_POST[key($_POST)]= $_MIND['fw']->treatClientInfo($_POST[key($_POST)]);
			next($_POST);
		}
	}
	date_default_timezone_set($_MIND["default_timezone"]);
	//ini_set('display_errors', 0);
?>