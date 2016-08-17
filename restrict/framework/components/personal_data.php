<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	if($_POST)
	{
		if(trim($_POST['pwd'])=='')
		{
			echo "Mind.Dialog.ShowModalMessage('Invalid Current Password', 'error')";
			/*echo "parent.document.getElementById('options_errorMessageLabel').style.display='';
					parent.document.getElementById('options_errorMessageLabel').innerHTML= 'Invalid Current Password';";*/
			exit;
		}
		$u= new User();
		if($u->loadUser($_SESSION['user']['login'], $_POST['pwd']))
		{
			$u->populate($_POST);
			$u->login($_SESSION['user']['login']);
			if(trim($_POST['new_pwd'])!='')
				$u->pwd($_POST['new_pwd']);
			$ret= $_MIND['fw']->output();
			if(trim($_MIND['fw']->output())!='')
			{
				echo "Mind.Dialog.ShowModalMessage('".$_MIND['fw']->output()."', 'error')";
				exit;
			}
			$u->save(false);
			if(trim($_MIND['fw']->output())=='')
			{
				$_SESSION['user']['name']		= $u->name();
				$_SESSION['user']['login']		= $u->login();
				$_SESSION['user']['age']		= $u->age();
				$_SESSION['user']['description']= $u->description();
				$_SESSION['user']['position']	= $u->position();
				$_SESSION['user']['status']		= $u->status();
				$_SESSION['user']['email']		= $u->email();
				echo "parent.Mind.Dialog.CloseModal();";
				exit;
			}else{
					echo "Mind.Dialog.ShowModalMessage('".$_MIND['fw']->output()."', 'error')";
				 }
		}else{
				echo "Mind.Dialog.ShowModalMessage('".$_MIND['fw']->output()."', 'error')";
				exit;
			 }
		exit;
	}else{
			$u= new User();
			$u->populate($_SESSION['user']);
		 }
?>
<form action='<?php echo $_SERVER['PHP_SELF']; ?>'>
	<div class='config'>
		<div class='errorMessageLabel'
			 id='options_errorMessageLabel'
			 style='display:none;'>
			<br>
		</div>
		<br>
		<table align='center'>
			<tr>
				<td>
					Name
				</td>
				<td>
					<input type='text'
						   name="name"
						   class='iptText'
						   value='<?php echo $u->name(); ?>'
						   required='true'
						   label='Name'>
					<input type='hidden'
						   name="status"
						   value='1'>
				</td>
			</tr>
			<tr>
				<td>
					Age
				</td>
				<td>
					<input type='text'
						   name="age"
						   class='iptText'
						   style='width:40px;'
						   value='<?php echo $u->age(); ?>'>
				</td>
			</tr>
			<tr>
				<td>
					Description
				</td>
				<td>
					<input type='text'
						   name="description"
						   class='iptText'
						   value='<?php echo $u->description(); ?>'>
				</td>
			</tr>
			<tr>
				<td>
					Position
				</td>
				<td>
					<input type='text'
						   name="position"
						   class='iptText'
						   value='<?php echo $u->position(); ?>'>
				</td>
			</tr>
			<tr>
				<td>
					E-mail
				</td>
				<td>
					<input type='text'
						   name="email"
						   class='iptText'
						   required='true'
						   label='E-mail'
						   value='<?php echo $u->email(); ?>'>
				</td>
			</tr>
			<tr>
				<td colspan='2'
					class='iptText'>
					<table>
						<tr>
							<td>
								Login
							</td>
							<td>
								<input type='text'
									   name="login"
									   class='iptText'
									   disabled='true'
									   required='true'
									   label='Login'
									   value='<?php echo $u->login(); ?>'>
							</td>
						</tr>
						<tr>
							<td>
								Current password
							</td>
							<td>
								<input type='password'
									   name="pwd"
									   required='true'
									   label='Current Password'
									   class='iptText'>
							</td>
						</tr>
						<tr>
							<td>
								New password
							</td>
							<td>
								<input type='password'
									   name="new_pwd"
									   class='iptText'>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</form>