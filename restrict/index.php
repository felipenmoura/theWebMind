<?php
	include('config/mind.php');
	include($_MIND['framework']);
	include($_MIND['header']);
	
	$_MIND['fw']->mountIde();
	$_MIND['fw']->output();
	
	echo "<script>
	Mind.Properties.updatePath= '".$_MIND['updateSource']."'; 
</script>";
	
	$opts= $_MIND['fw']->loadOptions();
	
	$current= $_MIND['fw']->loadXML($_MIND['rootDir'].'config/current.xml');
	$localCurrent= $_MIND['rootDir'].'config/current.xml';
	$localCurrentDir= $localCurrent;
	$last= $current->data->lastCheck['value'];
	
	$t= time();
	
	$dia= 60*60*24;
	
	if($opts['lookForUpdate'] != 'never')
	{
		switch($opts['lookForUpdate'])
		{
			case '1':
				$time= 7*$dia;
			break;
			case '2':
				$time= 30*$dia;
			break;
			case '3':
				$time= $dia;
			break;
			default:
				$time= 0;
		}
		
		if($t - $last > $time)
		{
			// time to check for updates
			if($serverCurrent= file_get_contents($_MIND['updateSource']))
			{
				$localCurrent= @simplexml_load_file($localCurrent);
				$serverCurrent= @simplexml_load_string($serverCurrent);
				$updated= false;
				
				//print_r($opts);
					
				if( ((integer)str_replace('.', '', $localCurrent->mindFiles['version']))
					<
					((integer)str_replace('.', '', $serverCurrent->mindFiles['version'])))
				{ // there are updates
				
					$hereVs= explode('.', (string)$localCurrent->mindFiles['version']);
					$curVs= explode('.', (string)$serverCurrent->mindFiles['version']);
					
					$alert= false;
					
					if($hereVs[0] < $curVs[0])
					{
						if($opts['actionWithNewVersion'] == 1)
						{
							$alert= true;
						}
					}
					if($hereVs[1] < $curVs[1])
					{
						if($opts['actionWithNewSubVersion'] == 1)
						{
							$alert= true;
						}
					}
					if($hereVs[2] < $curVs[2])
					{
						if($opts['actionWithNewUpdates'] == 1)
						{
							$alert= true;
						}
					}

					if(!$alert)
					{
						?>
							<script>
								setTimeout(function(){
										Mind.UpdateItSelf();
								}, 10000);
							</script>
						<?php
					}else{
							?>
								<script>
									setTimeout(function(){
										if(confirm("There are new updates, do you want to install them now?"))
										{
											Mind.UpdateItSelf();
										}
									}, 10000);
								</script>
							<?php
						}
					
				}
			}else{
					echo "<script>";
						echo "alert('Couldn\'t reach the server to check for updates!');";
					echo "</script>";
				 }
		}
	}
	exit;
?>