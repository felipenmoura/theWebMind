<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
	
	if($_POST){
		echo "
				Mind.Dialog.CloseModal();
				setTimeout(function(){
					Mind.Project.Load('".$_POST['pName']."');
				}, 1000)
			 ";
		exit;
	}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<table align='center'>
			<tr>
				<td>
					Select the Project
				</td>
				<td>
					<select name='pName' required='true' label='Project'>
						<option value=''>...</option>
					<?php
						$projectList= Project::getProjects($_SESSION['user']['login']);
						foreach($projectList as $p)
						{
							echo "<option value='".$p->name."'>".$p->name."</option>";
						}
					?>
					</select>
				</td>
				<td>
					or 
					<input type='button'
						   class='iptButton'
						   value='Import'
						   onclick="Mind.Dialog.OpenModal(true,'600','170','Import ','midle','import.php',false);">
				</td>
			</tr>
		</table>
	</fieldset>
</form>