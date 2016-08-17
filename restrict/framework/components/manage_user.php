<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	$ar= Array();
	$_POST['action']= explode(',', str_replace(' ', '', $_POST['action']));
	if(in_array('list', $_POST['action']))
	{
		$ar['list']= User::getUsers();
	}
	if(in_array('get', $_POST['action']))
	{
		$u= new User($_MIND['fw']->filter($_POST['code']));
		$ar['get']= Array();
		$ar['get']['info']= Array();
		$ar['get']['conf']= Array();
		$ar['get']['info']['name']= $u->name();
		$ar['get']['info']['age']= $u->age();
		$ar['get']['info']['description']= $u->description();
		$ar['get']['info']['position']= $u->position();
		$ar['get']['conf']['login']= $u->login();
		$ar['get']['conf']['status']= $u->status();
		$ar['get']['conf']['email']= $u->email();
	}
	echo json_encode($ar);
?>
