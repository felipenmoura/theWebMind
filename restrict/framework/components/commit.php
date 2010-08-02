<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	$p= new Project($_SESSION['currentProject'], $_SESSION['user']['login']);
	
	$curVersion= $_MIND['fw']->currentVersion($p);
	
	$p->version[1]++;
	$p->version[2]= 0;
	$p->date= date('m/d/Y - H:i:s');
	
	if($p->version[1] <= $curVersion['subVersion'])
	{
		header("Location:update.php");
		exit;
	}else{
			$userDir= $_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$p->name.'/root';
			$projDir= $_MIND['rootDir'].$_MIND['publishDir'].'/'.$p->name.'/root';
			
			
			if(isset($_POST['confirmCommit']))
			{
				$p->save();
				$dirFrom= $_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$p->name;
				$dirTo= $_MIND['rootDir'].$_MIND['publishDir'].'/'.$p->name;
				
				$dirBackUp= $_MIND['rootDir'].$_MIND['publishDir'].'/'.$p->name.'/'.$_MIND['backupDir'];
				if(!file_exists($dirBackUp))
					mkdir($dirBackUp);
				$dirBackUp.= '/'.date('Ymdhis').'_'.$_SESSION['user']['login'];
				mkdir($dirBackUp);
				
				$_MIND['fw']->updateCopy($dirTo.'/root', $dirBackUp);
				
				$_MIND['fw']->updateCopy($dirFrom, $dirTo);
				
				$usrs= User::getUsers($p->users); // loads only the users who are working on the current project
				echo sizeof($usrs).' <<< ';
				for($i=0, $j= sizeof($usrs); $i<$j; $i++)
				{
					//echo $usrs[$i]->email;
					if(mail($usrs[$i]->email, '[mind2.0] Project commited', "The project ".$p->name." was commited by ".$usrs[$i]->name.".

--
TheWebMind.org 2.0
http://thewebmind.org/
Documentation: http://docs.thewebmind.org/"));
					else
						$_MIND['fw']->log('SMTP Server not found! Mail message culd not be sent.', 'server conf');
				}
				
				echo 'The project has been successfully commited to the server';
				exit;
			}
			
			
			?>
				<table style='width:100%;'>
					<tr style='font-size:10px;'>
						<td>
							<div style='width:10px;
										height:10px;
										border:solid 1px #000;
										background-color:#393;
										float:left;'>
								<br/>
							</div>&nbsp;
							New Items
						</td>
						<td>
							<div style='width:10px;
										height:10px;
										border:solid 1px #000;
										background-color:#c96;
										float:left;'>
								<br/>
							</div>&nbsp;
							Items to replace
						</td>
						<td>
							<div style='width:10px;
										height:10px;
										border:solid 1px #000;
										background-color:#f00;
										float:left;'>
								<br/>
							</div>&nbsp;
							Items to replace that have been changed
						</td>
					</tr>
					<tr>
						<td colspan='3'>
							<div style='background-color:#fff;
										border:solid 1px #666;
										height:334px;
										overflow:auto;
										font-size:10px;
										padding:4px;'>
			<?php
			
			function throughDir($dir)
			{
				GLOBAL $_MIND;
				GLOBAL $userDir;
				GLOBAL $projDir;
				GLOBAL $curVersion;
				
				$d = dir($dir);
				$tmpDir;
				//echo $curVersion['date'].'<hr>';
				
				while (false !== ($entry = $d->read()))
				{
					if($entry != '.' && $entry != '..')
					{
						$tmpDir= str_replace($userDir, '', $dir.'/'.$entry);
						$pos= sizeof(explode('/', $tmpDir)) -2;
						if(is_dir($dir.'/'.$entry))
						{
							echo "<span style='color:#ddd;'>".str_repeat("&nbsp;|&nbsp;", $pos)."</span><img src='".$_MIND['imageDir']."/icon_folder.gif' width='16'/> ".$tmpDir.'<br/>';
							throughDir($dir.'/'.$entry);
						}else{
								echo "<span style='color:#ddd;'>".str_repeat("&nbsp;|&nbsp;", $pos).'</span>';
								echo "<img src='".$_MIND['imageDir']."/icon_file.gif' width='16'/>";
								if(!file_exists($projDir.$tmpDir))
									echo '<span style="color:#393;">'.$entry.'</span><br/>';
								else{
										if(date('YmdHi', filemtime($projDir.$tmpDir)) > date('YmdHi', filemtime($projDir.'/../conf.xml')))
											echo '<span style="color:#f00;font-weight:bold;">'.$entry.'</span><br/>';
										else
											echo '<span style="color:#c96;">'.$entry.'</span><br/>';
									}
							 }
						
					}
				}
				$d->close();
			}
			throughDir($userDir);
	
			?>
							</div>
						</td>
					</tr>
				</table>
				<center>
					<input type='button'
						   value='Commit'
						   class='ui-state-default ui-corner-all'
						   style='height:26px;'
						   onclick="Mind.Project.ConfirmCommit();"/>
				</center>
			<?php
		 }
?>