<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	$p= new Project($_SESSION['currentProject'], $_SESSION['user']['login']);
	
	$curVersion= $_MIND['fw']->currentVersion($p);
	
	if($curVersion['subVersion'] <= $p->version[1])
	{
		?>
			<center>
				<div style='border:solid 1px #666;padding:20px;margin:8px; background-color:#cfc;color:#393;font-weight:bold;'>
					<img src='<?php echo $_MIND['imageDir'].'/'; ?>visto.png' />
					Your project is up to date.
				</div>
				<br/>
				<input type='button'
					   value='Ok'
					   class='ui-state-default ui-corner-all'
					   style='height:26px;'
					   onclick="Mind.Dialog.CloseModal();"/>
			<center>
		<?php
	}
	else{
			$dir= $_MIND['rootDir'].$_MIND['publishDir'].'/'.$p->name;
			if($p->hasProject($p->name))
			{
				$dir.= '/mind/mind_code.php';
				?>
				<div style='border:solid 1px #666;padding:4px;margin:8px;text-align:center; background-color:#f99;color:#fff;font-weight:bold;'>
					Your project is out of date !!
				</div>
				<div style='padding-left:15px;'>
					Currently in version <span id='newUpToDateVersion'><?php echo $curVersion['version'].'.'.$curVersion['subVersion'].'.'.$curVersion['update'];?></span><br/>
					With the following code:<br/>
				</div>
				<div style='background-color:white;border:solid 1px #666;padding:8px;margin:8px;height:265px;overflow:auto;'>
					<pre id='newUpToDateCode'><?php
							echo file_get_contents($dir);
						?></pre>
				</div>
				<center>
					<input type='button'
						   value='Proceed'
						   class='ui-state-default ui-corner-all'
						   style='height:26px;'
						   onclick="Mind.Project.ConfirmUpdate()"/>
				</center>
				<?php
			}
		}
?>