<?php
	include('restrict/config/mind.php');
	include('restrict/'.$_MIND['framework']);
	if($_SESSION)
	{
		session_destroy();
	}
	$u= new User();
	$_MIND['rootDir']= 'restrict/';
	if($u->loadUser($_POST['login'], utf8_encode($_POST['pass'])))
	{
		if($_MIND['sessionDir'] != 'default')
			session_save_path('restrict/'.$_MIND['sessionDir']);
		session_start();
		if($_MIND['sessionLife']!== '0')
			session_cache_expire($_MIND['sessionLife']);
		$_SESSION['user']				= Array();
		$_SESSION['user']['name']		= $u->name();
		$_SESSION['user']['login']		= $u->login();
		$_SESSION['user']['age']		= $u->age();
		$_SESSION['user']['description']= $u->description();
		$_SESSION['user']['position']	= $u->position();
		$_SESSION['user']['status']		= $u->status();
		$_SESSION['user']['email']		= $u->email();
		$_SESSION['mind']				= Array();
		echo "<script> top.location.href= 'restrict/index.php';</script>";
	}else{
			?>
				<script>
					top.$("#mind_login_message").html("Error: Invalid access data. Your login or password doesn't match.");
					top.$("#mind_login_message").fadeIn("slow");
				</script>
			<?php
		 }
?>