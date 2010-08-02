<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	if($_POST)
	{
		set_time_limit(72000); // 20 Minutes
		
		if($_POST['database_pref'] != 'only_database')
		{
			$numberOfSteps= 7; // extra steps Mind uses to itself
		}else{
				$numberOfSteps= 3; // extra steps Mind uses to itself
			 }
		
		$_SESSION['keepGenerating']= true;
		?>
			<style type='text/css'>
				div
				{
					font-weight:normal;
					border-bottom:dashed 1px #eee;
					margin:2px;
				}
				#here
				{
					border-bottom:none;
				}
			</style>
			<body topmargin='1' leftmargin='1' style='font-family:Tahoma; background-color:#fff;color:#666;font-size:12px;'>
				<div id='here'><!-- -->
				</div>
			</body>
		<?php
		$errMsg= "<span style='color:red;font-weight:bold'>ERROR: </span>An error ocurred while trying to generate the project.";
		
		function showLoadStatus($s, $c= false)
		{
			GLOBAL $_MIND;
			if($_SESSION['keepGenerating'] !== true)
			{
				exit;
			}
			?><script><?php
				if($c)
				{
					if($c>100)
						$c= 100;
					if($c<$_SESSION['currentPerc'])
					{
						$_SESSION['currentPerc']= $c;
						echo " parent.\$('#generatingLoadBar').progressbar('value', ".$c."); ";
					}
				}else{
						$_SESSION['currentPerc']+= $_SESSION['perc'];
						echo " parent.\$('#generatingLoadBar').progressbar('value', ".$_SESSION['currentPerc']."); ";
					}
			?>document.getElementById('here').innerHTML+="<div><?php echo $s; ?></div>";parent.document.getElementById('generatingCurrentStatus').innerHTML="<?php echo $s; ?>";window.scrollBy(0,20);</script><?php
			$_MIND['fw']->forceFlush();
		}
		
		$_SESSION['currentPerc']= 0;

		$_SESSION['perc']= 0;
		showLoadStatus("Preparing to generate");
		$data= JSON_decode($_POST['p']);
		if(!$data)
			$data= JSON_decode(stripslashes($_POST['p']));
		
		$data= $_MIND['fw']->objectToArray($data);
		
		$p= new Project($data['name'], $_SESSION['user']['login']);
		$p->populate($data);
		
		$p->version[2]++;
		$p->save();
		
		if($p->process())
		{
			if($_POST['database_pref']!='only_database'){
				showLoadStatus("Negotiating with module", 4);
				$numberOfSteps+= sizeof($p->knowledge->tables);
			}
		}else{
				showLoadStatus($errMsg);
				exit;
			 }
		
/*		echo '<pre>';
		print_r($p->knowledge);
		echo '</pre>';
		exit;*/	
		if($_POST['database_pref'] != 'no' && $_POST['database_pref'] != 'only_database'){
			$numberOfSteps+= sizeOf($p->knowledge->tables) + 2;
		}
		$numberOfSteps++;
		
		$_SESSION['perc']= number_format((96 / $numberOfSteps), 2, '.', '');
		
		
		if($_POST['database_pref'] != 'only_database')
		{
			$m= new Module($_POST['module']);
			unset($p->knowledge->especialChars);
			unset($p->knowledge->fixedChars);
			unset($p->knowledge->relations);
			unset($p->knowledge->tmpSentences);
			unset($p->knowledge->processedWML);
			unset($p->knowledge->unique);
			unset($p->knowledge->required);
			unset($p->knowledge->verbId);
			unset($p->knowledge->quantifierId);
			unset($p->knowledge->quantifiers);
			$m->loadModule($p);
		}
		// preparing bkps and directories structure
		
		
		if($_POST['database_pref'] != 'only_database')
		{
			showLoadStatus("Creating a backup");
		
			$pDir		= $_MIND['rootDir']  .       $_MIND['userDir'] 	        . '/'      . $_SESSION['user']['login'] . '/temp/' . $p->name . '/root/';
			$pDirForFW	= $_MIND['userDir']  . '/' . $_SESSION['user']['login'] . '/temp/' . $p->name.'/root/';
			$mDir		= $_MIND['rootDir']  .       $_MIND['moduleDir']        . '/'      . $m->name       . '/data/';
			$mDirForFW	= $_MIND['moduleDir']. '/' . $m->name       . '/data/';
			$tmpDir		= $_MIND['rootDir']  .       $_MIND['userDir']          . '/'      . $_SESSION['user']['login'] . '/temp/' . $p->name . '/tmp_root';
			
			showLoadStatus("Creating directories structure");
			if(file_exists($tmpDir))
				$_MIND['fw']->deleteDirectory($tmpDir);
			rename($pDir, $tmpDir);
			chmod($tmpDir, 0777);
			mkdir($pDir, 0777);
			
			showLoadStatus("Copying files");
			$m->structure($mDirForFW, $pDirForFW);
			$m->pDir($pDir);
			$m->mDir($mDir);
			
			showLoadStatus("Calling module");
			$m->onStart();
			showLoadStatus("Copies done");
			
			$t= $p->knowledge->tables;
			reset($t);
			showLoadStatus("Starting CRUD");
			while($tb= current($t))
			{
				showLoadStatus('Building files: '.$tb->name);
				$m->askForCRUD($tb);
				next($t);
			}
			
			showLoadStatus("Closing module");
			$m->onFinish();
			
			showLoadStatus("Cleaning the mess...removing temp files");
			$_MIND['fw']->deleteDirectory($tmpDir);
		}
		
		if($_POST['database_pref'] != 'only_database')
		{
			showLoadStatus("Using any extra information");
			$m->callExtra();
		}
		
		/* generating database, if required */
		if($_POST['database_pref'] != 'no')
		{
			$p->generateQueries();
			
			if($_POST['database_pref'] == 'only_database'){
				$_POST['database_pref'] = $_POST['only_database'];
			}
			if($con= @$p->dbmsObj->connectTo($p->environment[$_POST['database_pref']]))
			{
				showLoadStatus("Connecting to ".$_POST['database_pref'] ." database .......");
			}else{
					showLoadStatus("<b>ERROR: </b>Failed to connect to DataBase ... aborting (files have already been created)<br/><br/>");
					exit;
				 }
			
			$t= $p->knowledge->tables;
			reset($t);
				echo '<pre>';
			while($tb= current($t))
			{
				if($p->dbmsObj->tableExists($con, $tb->name))
				{
					if($_POST['existingTables']== '1')
					{
						showLoadStatus("<span style='color:#55C175;'>Skipping</span> table '".$tb->name."' (table already exists)");
						$qr= true;
					}else{
							showLoadStatus("<span style='color:#B80400;'>Replacing</span> table '".$tb->name."'");
							$r= $p->dbmsObj->removeTable($con, $tb->name);
							if(!$r)
							{
								throw new Exception('Couldn\'t drop table '.$tb->name);
								exit;
							}
							$qr= $p->dbmsObj->query($con, $tb->DDL);
							$qr= true;
						 }
				}else{
						showLoadStatus("<span style='color:#55C175;'>Creating table </span>'".$tb->name."'");
						$qr= $p->dbmsObj->query($con, $tb->DDL);
					 }
				
				if(!$qr)
				{
					showLoadStatus("Error when creating the table '".$tb->name."'");
					echo $p->dbmsObj->getLastError($con);
					exit;
				}
				next($t);
			}
			
			showLoadStatus('Refering tables and foreign keys');
			for($i=0, $j=sizeof($p->fkDDL); $i<$j; $i++)
			{
				//echo $p->fkDDL[$i].'<br/>';
				$p->dbmsObj->query($con, $p->fkDDL[$i]);
			}
		}
		showLoadStatus("<b>Finished</b>, the project's been generated".((isset($_POST['database_pref']))? " <b><a href='JavaScript:top.Mind.Project.SeeCurrentUserFiles()'>here</a></b>": ''), 100);
		
		echo "<script>
			top.document.getElementById('currentVersionLabel').innerHTML= '[".$p->version[0].".".$p->version[1].".".$p->version[2]."]';
			top.Mind.Project.attributes.version= ".$p->version[0].";
			top.Mind.Project.attributes.subVersion= ".$p->version[1].";
			top.Mind.Project.attributes.update= ".$p->version[2].";
		</script>";
	}
	exit;
?>
