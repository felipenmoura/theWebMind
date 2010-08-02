<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
	
	if($_POST && $_GET['login'])
	{
		$u= new User($_GET['login'], $_POST['pwd']);
		if(trim($_POST['pwd']) == '' || !$u)
		{
			echo "Error: Invalid password to the current user";
			exit;
		}
		echo 'aaaaa '.$_POST['pwd'].' - '.$u->name;
		$u->populate($_POST);
		$u->save();
		exit;
	}
	
	$u= User::getUsers();
?>
<table style="width:100%;height:100%;">
	<tr>
		<td class="user_left_list">
			<div style='overflow:auto;height:470px;overflow-x:hidden;overflow-y:auto;'>
			<table style="width:150px">
			<?php				
				foreach ($u as $a){
					echo "<tr><td userPass='".$a->pwd."' userName='".$a->login."' id='mind_user_manage_".$a->login."' class='list_users'>$a->login</td></tr>";
				}
			?>
			</table>
			</div>
		</td>
		<td class="user_parent"><div class="user_content_list"> </div></td>
	</tr>
	<tr>
		<td colspan="2" id="mind_manage_user_label_message">
		</td>
	<tr>
</table>
<script>
	Mind.View.User.Manage();
</script>
