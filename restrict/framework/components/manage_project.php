<?php 
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
	
	if(isset($_POST["action"]))
		$_GET["action"] = $_POST["action"];
	if(isset($_POST["code"]))
		$_GET["code"] = $_POST["code"];
	
	if(isset($_GET["action"]))
	{
		$action = $_GET["action"];
		switch($action){
			case "getProject" :
				$p = Project::getProject($_POST["code"],$_SESSION['user']['login']);
				echo(json_encode($p));
				exit;
			break;
			case "saveProject" :							
				$p = new Project($_GET["code"],$_SESSION['user']['login']);
				$_POST = $p->preparePost($_POST);
				$p->populate($_POST);
				if($p->save())
					echo $_GET["code"];
				else
					echo "Error";
				exit;
			break;
			case "removeProject" :
				$p = new Project($_GET["code"],$_SESSION['user']['login']);
				$p->remove($_GET["code"]);
				exit;
			break;
		}		
	}
	
	$p = Project::getProjects($_SESSION['user']['login']);	
?>
<table style="width:100%;height:100%;">
	<tr>
		<td class="projet_left_list">
			<div style='overflow:auto;height:470px;overflow-x:hidden;overflow-y:auto;'>
			<table style="width:150px">
			<?php				
				foreach ($p as $a){
					echo "<tr><td projectName='".$a->name."' id='mind_project_manage_".$a->name."' class='list_projects'>$a->name</td></tr>";
				}
			?>
			</table>
			</div>
		</td>
		<td class="project_parent"><div class="projet_content_list"> </div></td>
	</tr>
	<tr>
		<td colspan="2" id="mind_manage_project_label_message">			
		</td>
	<tr>
</table>
<script>
	Mind.View.Project.Manage();
</script>
