<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?>
<?php

	if(isset($_POST["action"]))
		$_GET["action"] = $_POST["action"];
	if(isset($_POST["code"]))
		$_GET["code"] = $_POST["code"];
	
	if(isset($_GET["action"]))
	{
		$action = $_GET["action"];
		switch($action){
			case "disablePlugin" :
				$pl = Plugin::getPlugin($_GET["code"],$_SESSION['user']['login']);
				$pl->disable();
			break;
			case "enablePlugin" :
				$pl = Plugin::getPlugin($_GET["code"],$_SESSION['user']['login']);
				$pl->enable();
			break;
			case "removePlugin" :
				$pl = Plugin::getPlugin($_GET["code"],$_SESSION['user']['login']);
				$pl->remove();
			break;
		}		
	}
	$pl= Plugin::getPlugins();
	
?>
<table style="width:100%;height:100%;">
	<tr>
		<td class="plugin_left_list">
			<div style='overflow:auto;height:470px;overflow-x:hidden;overflow-y:auto;'>
			<table style="width:150px">
			<?php				
				foreach ($pl as $a){
					echo "<tr><td pluginName='".$a->name."' id='mind_plugin_manage_".$a->name."' class='list_plugins'>$a->name</td></tr>";
				}
			?>
			</table>
			</div>
		</td>
		<td class="plugin_parent"><div class="plugin_content_list"> </div></td>
	</tr>
	<tr>
		<td colspan="2" id="mind_manage_plugin_label_message">			
		</td>
	<tr>
</table>
<script>
	Mind.View.Plugin.Manage();
</script>