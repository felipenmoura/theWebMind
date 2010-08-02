<?php
	include('restrict/config/mind.php');
	include('restrict/'.$_MIND['framework']);
	if(isset($_SESSION))
	{
		session_destroy();
	}
	$u= new User();
	$_MIND['rootDir']= 'restrict/';
	
	if($_MIND['sessionDir'] != 'default')
		session_save_path('restrict/'.$_MIND['sessionDir']);
	session_start();
	?>
		<script>
			Mind= {
					Dialog:{
							ShowMessage: function (msg)
							{
								top.$("#mind_login_message").html("Error: Your login or password doesn't match.");
								top.$("#mind_login_message").fadeIn("slow");
							}
						  }
				  };
	<?php
	if(@$u->loadUser($_POST['login'], utf8_encode($_POST['pass'])))
	{
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
		echo "  top.location.href= 'restrict/index.php'; ";
	}else{
			?>
			<?php
		 }
?>
</script>