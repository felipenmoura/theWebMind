<?php
	require_once('config/mind.php');
	require_once(''.$_MIND['framework']);
	require_once(''.$_MIND['header']);
	set_time_limit(72000); // 20 Minutes
	
	$current= $_MIND['fw']->loadXML($_MIND['rootDir'].'config/current.xml');
	$localCurrent= $_MIND['rootDir'].'config/current.xml';
	$localCurrentDir= $localCurrent;

	function changeStatus($n)
	{
		GLOBAL $_MIND;
		echo "<script>";
			echo "parent.document.getElementById('currentChangesToTake').innerHTML= parent.document.getElementById('currentChangesToTake').innerHTML -1; ";
			echo "parent.document.getElementById('updateListItem_".$n."').style.color= '#777'; ";
			echo "parent.document.getElementById('updateListItem_".$n."').style.textDecoration= 'line-through'; ";
		echo "</script>";
		$_MIND['fw']->forceFlush();
	}
	
	if(!@$serverCurrent= @file_get_contents($_MIND['updateSource']))
	{
		?>
				<div style='border:solid 1px #666;padding:4px;margin:8px;text-align:center; background-color:#f99;color:#fff;font-weight:bold;'>
					The server couldn't be reached. The system could not search for updates.
				</div>
		<?php
		exit;
	}
	echo "<div id='mindUpdateShowDataDiv'>";
	$localCurrent= @simplexml_load_file($localCurrent);
	$serverCurrent= @simplexml_load_string($serverCurrent);
	
	$hereVs= explode('.', (string)$localCurrent->mindFiles['version']);
	$curVs= explode('.', (string)$serverCurrent->mindFiles['version']);
	$updated= false;
	
	$changedFiles= Array();
	
	if(!isset($_GET['confirmUpdate']))
		echo 'Current installed version: <b><i>'.((string)$localCurrent->mindFiles['version']) .'</i></b><br/>Current version to download: <b><i>'. ((string)$serverCurrent->mindFiles['version']).'</i></b>&nbsp;&nbsp;&nbsp; <img src="'.$_MIND['imageDir'].'/load.gif" id="mindUpdateImageLoader" style="display:none;"/><br/>';

	$currentChangesToTake= 0;
	if((string)$localCurrent->mindFiles['version'] != (string)$serverCurrent->mindFiles['version'])
	{
		if(!isset($_GET['confirmUpdate']))
			echo "<br/><b>Changes:</b> <span id='currentChangesToTake'>0</span><hr/><div style='padding-left: 20px;'>";
		foreach($serverCurrent->mindFiles->file as $file)
		{
			$continue= false;
			if(!file_exists('../'. $file['addr']))
			{
				$continue= true;
				$currentChangesToTake++;
				if(!isset($_GET['confirmUpdate']))
				{
					echo "<span style='color:#4a4;' id='updateListItem_".$file['addr']."'>- To be Added: ".$file['addr'].'</span><br/>';
				}else{
						changeStatus($file['addr']);
					 }
			}else{
					$match= (array)$localCurrent->xpath("mindFiles/file[@addr='".$file['addr']."']");
					
					if(isset($match[0]))
					{
						$match= $match[0];
						//echo ((integer)$match['version']) .' < '. ((integer)$file['version']).'<br/>';
						if((integer)$match['version'] < (integer)$file['version'])
						{
							$continue= true;
							$currentChangesToTake++;
							if(!isset($_GET['confirmUpdate']))
							{
								echo "<span style='color:#f44;' id='updateListItem_".$file['addr']."'>- To be Updated: ".$file['addr'].'</span><br/>';
							}else{
									changeStatus($file['addr']);
								 }
						}
					}else{
							$continue= true;
							$currentChangesToTake++;
							if(!isset($_GET['confirmUpdate']))
							{
								echo "<span style='color:#f44;' id='updateListItem_".$file['addr']."'>- To be Updated: ".$file['addr'].'</span><br/>';
							}else{
									changeStatus($file['addr']);
								 }
						 }
				 }
			$curPath= '';
			if($continue && isset($_GET['confirmUpdate']))
			{
				$updated= true;
				$addr= explode('/', (string)$file['addr']);
				$curPath= '';
				for($i=0, $j= sizeof($addr)-1; $i<$j; $i++)
				{
					if(!file_exists('../'.$curPath.$addr[$i]))
					{
						mkdir('../'.$curPath.$addr[$i]);
					}
					$curPath.= $addr[$i].'/';
				}
				fclose(fopen('../'.$curPath.$addr[$i], 'w+'));
				file_put_contents('../'.$curPath.$addr[$i],
								  file_get_contents(preg_replace('/current\.xml$/', '', $_MIND['updateSource']).'/index.php?update='.$curPath.$addr[$i]));
				$changedFiles[]= $addr[$i];
				$localCurrent->details= $serverCurrent->details;
			}
		}
		if(!isset($_GET['confirmUpdate']))
		{
			echo "</div><hr/>";
			echo "<b>Details:</b><br/>
					<div style='padding-left:20px; background-color:#e0e0ff;border:solid 1px #99c;'>
						<pre>";
			echo (string)$serverCurrent->details;
			echo '</pre></div>';
			// let's use it to update the value of how many changes we have
			echo "<span id='hiddenCurrentChangesToTake' style='display:none;'>".$currentChangesToTake.'</span>';
		}
	}else{
			$updated= false;
		 }
	
	if(!isset($_GET['confirmUpdate']) && $currentChangesToTake > 0)
	{
		echo '<center>';
		echo "<input type='button' class='ui-state-default ui-corner-all ui-state-focus ui-state-hover' value='Update' onclick='Mind.ConfirmUpdate();'>";
		echo '<br/><iframe id="tempIframeForUpdate" style="display:none;"></iframe>';
		echo '</center>';
		echo '</div>';
		exit;
	}
	if($updated)
	{
		$localCurrent->data->lastUpdate['value']= time();
		$localCurrent->mindFiles= $serverCurrent->mindFiles;
		
		for($i=0, $j=sizeof($serverCurrent->mindFiles->file); $i<$j; $i++)
		{
			$localCurrent->mindFiles->addChild('file');
			$localCurrent->mindFiles->file[$i]['addr']= (string)$serverCurrent->mindFiles->file[$i]['addr'];
			$localCurrent->mindFiles->file[$i]['version']= (string)$serverCurrent->mindFiles->file[$i]['version'];
		}
		
		if(!isset($opts))
			$opts= $_MIND['fw']->loadOptions();
		
		/* if(
			((integer)$hereVs[0] < (integer)$curVs[0] && (integer)$opts['actionWithNewVersion']!= 2)
			||
			((integer)$hereVs[1] < (integer)$curVs[1] && (integer)$opts['actionWithNewSubVersion']!= 2)
			||
			((integer)$hereVs[2] < (integer)$curVs[2] && (integer)$opts['actionWithNewUpdates']!= 2)
		  )
		{ */
			//echo "<img src='".$_MIND['imageDir']."/visto.png'>";
			?>
				<div style='border:solid 1px #666;padding:20px;margin:8px; background-color:#cfc;color:#393;font-weight:bold;'>
					<img src='<?php echo $_MIND['imageDir'].'/'; ?>visto.png' />
					<b>theWebMind</b> has just been updated to its latest version, <?php echo implode('.', $curVs); ?>
				</div>
					<fieldset>
						<legend>
							Details
						</legend>
						<pre style='text-align:left;'><?php
							echo (string)$serverCurrent->details;
						?></pre>
					</fieldset>
				</div>
			<?php
		//}
	}else{
			//echo "<img src='".$_MIND['imageDir']."/visto.png'>Your system is already in the lates version.";
			?>
			<div style='border:solid 1px #666;padding:20px;margin:8px; background-color:#cfc;color:#393;font-weight:bold;'>
					<img src='<?php echo $_MIND['imageDir'].'/'; ?>visto.png' />
					Your system is already using the latest version
			</div>
			<?php
		 }
	$localCurrent->mindFiles['version']= (string)$serverCurrent->mindFiles['version'];
	$localCurrent->data->lastCheck['value']= time();
	$_MIND['fw']->saveXML($localCurrent, $localCurrentDir);
	
?>
</div>
<script>
	setTimeout(function(){
		parent.document.getElementById('mindUpdateShowDataDiv').innerHTML= document.getElementById('mindUpdateShowDataDiv').innerHTML;
	}, 1000);
</script>