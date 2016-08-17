<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?>
<?php
	$ar= Array();
	$_POST['action']= explode(',', str_replace(' ', '', $_POST['action']));
	if(in_array('list', $_POST['action']))
	{
		$pl= Plugin::getPlugins();
		$ar['list']= $pl;
	}
	if(in_array('get', $_POST['action']))
	{
		$pl= new Plugin($_MIND['fw']->filter($_POST['code']));
		$ar['get']= $pl;
	}
	if(in_array('save', $_POST['action']))
	{
		//print_r($_POST['JSON']);
	}
	echo json_encode($ar);
?>