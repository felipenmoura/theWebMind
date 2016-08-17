<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	if($_POST)
	{
		$u= new User();
		$u->populate($_POST);
		if(file_exists($_MIND['rootDir'].$_MIND['userDir'].'/'.$u->login().'/'.$_MIND['userConfFile'].'.xml'))
		{
			echo "Mind.Dialog.ShowModalMessage('The User already exists', 'error')";
			exit;
		}
		$u->save(false);
		$ret= $_MIND['fw']->output();
		if(trim($ret)!='')
		{
			echo "Mind.Dialog.ShowModalMessage('".$ret."', 'error')";
		}else{
				echo "parent.Mind.Dialog.CloseModal();";
			 }
		exit;
	}
?>
<form action='<?php echo $_SERVER['PHP_SELF']; ?>'>
	<div class='userAdd'>
		<div class='errorMessageLabel'
			 id='new_user_errorMessageLabel'
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
						   style='width:40px;'>
				</td>
			</tr>
			<tr>
				<td>
					Description
				</td>
				<td>
					<input type='text'
						   name="description"
						   class='iptText'>
				</td>
			</tr>
			<tr>
				<td>
					Position
				</td>
				<td>
					<input type='text'
						   name="position"
						   class='iptText'>
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
						   label='E-mail'>
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
									   required='true'
									   label='Login'>
							</td>
						</tr>
						<tr>
							<td>
								Password
							</td>
							<td>
								<input type='password'
									   name="pwd"
									   class='iptText'
									   required='true'
									   label='Password'>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</form>
<script>
</script>