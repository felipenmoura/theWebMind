<?php 
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
	
	$p = Project::getProjects($_SESSION['user']['login']);
	
?>
<table style="width:100%;height:100%;">
	<tr>
		<td class="projet_left_list">
			<?php
				echo "<ul class=''>";
				foreach ($p as $a){
					echo "<li>" . $a->name . "</li>";
				}
				echo "</ul>";
			?>
		</td>
		<td class="projet_content_list">
			asdfsa
		</td>
	</tr>
</table>