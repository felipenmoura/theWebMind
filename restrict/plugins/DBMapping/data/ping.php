<?php
	require('adodb5/adodb.inc.php');
	
	$dbdriver= $_POST['dataBase'];
	$db= ADONewConnection($dbdriver);
	$db->debug = false;
	$server= $_POST['dbAddress1'];
	$user= $_POST['userRoot1'];
	$password= $_POST['userRootPwd1'];
	$database= $_POST['dbName1'];
	$f= false;
	if($dbdriver == 'mssql'){
		$db = &ADONewConnection("ado_mssql");
		$myDSN="PROVIDER=MSDASQL;DRIVER={SQL Server};". "SERVER=$server;DATABASE=$database;UID=$user;PWD=$password;";
		if($db->Connect($myDSN)){
			$f= true;
		}
	}else{
		if(@$db->Connect($server, $user, $password, $database))
		{
			$f= true;
		}
	}
	
	if(!$f)
	{
		echo 'Connection Failed
Error Message: '.$db->_errorMsg;
	 }else{
			echo 'Connection OK';
		 }
?>