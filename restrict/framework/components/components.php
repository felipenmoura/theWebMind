<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
	$r = Array();
	function getComponent($name,$data=false)
	{
		GLOBAL $r;
		GLOBAL $_MIND;
		
		switch($name){
			case "projectList" :
				$p = new Project();
				$projectList= $p->getProjects($_SESSION['user']['login']);
				$r['projectList']= ($projectList)? json_encode($projectList): 'false';
			break;
			case "home" :
				$r['home']= json_encode(str_replace("{MIND['home_page']}", $_MIND['home_page'], file_get_contents('home_page.php')));
			break;
			case "right_panel" :
				$r['right_panel']= json_encode("Painel da direita");
			break;
			case "projectData" :
				$p= new Project($data->name, $_SESSION['user']['login']);
				$langDir= $_MIND['rootDir'].$_MIND['languageDir'].'/';
				include($langDir.'language.php');
				$lang= new Language($p->lang);
				$p->verb= $lang->verbs[0];
				$r['projectData']= json_encode($p);
			break;
			case "pluginData" :
				$pl= new Plugin($data->name, $_SESSION['user']['login']);
				$r['pluginData']= json_encode($pl);
			break;
			case "userData" :
				$u= new User($data->name, $_SESSION['user']['login']);
				$r['userData']= json_encode($u);
			break;
			case "mindApplicationList" :
				$r['mindApplicationList']= json_encode("");
			break;
			case "project" :
				$_POST['pName']= $data->name;
				$_SESSION['currentProject']= $_POST['pName'];
				$r['project']= json_encode($_MIND['fw']->import($_MIND['rootDir'].$_MIND['components'].'/project.php'));
			break;
			case "DBMSsList":
				GLOBAL $_MIND;
				$l= $_MIND['fw']->getDBMSs();
				$r['DBMSsList']= json_encode($l);
			break;
			case "languagesList":
				GLOBAL $_MIND;
				$l= $_MIND['fw']->getLanguages();
				$r['languagesList']= json_encode($l);
			break;
			case "usersList":
				GLOBAL $_MIND;
				$l= $_MIND['fw']->getUsers();
				$r['usersList']= json_encode($l);
			break;
			case "savedDERList":
				GLOBAL $_MIND;
				$l= $_MIND['fw']->getSavedERDList($data->project);
				$r['savedDERList']= json_encode($l);
			break;
			case "savedDERItem":
				GLOBAL $_MIND;
				$l= $_MIND['fw']->getSavedERDItem($data->project, $data->diagram);
				$r['savedDERItem']= $l; // it is already a json encoded value
			break;
			case "moduleList":
				GLOBAL $_MIND;
				$l= $_MIND['fw']->getModulesList();
				$r['moduleList']= json_encode($l);
			break;
			case "getModule":
				GLOBAL $_MIND;
				$l= $_MIND['fw']->getModule($data->moduleName);
				$r['getModule']= json_encode($l);
			break;
			case "plugins":
				GLOBAL $_MIND;
				$l= $_MIND['fw']->getPlugins();
				$r['plugins']= json_encode($l);
			break;
			case "pluginsUnlinkThis":
				GLOBAL $_MIND;
				$d= $_MIND['rootDir'].$_MIND['pluginDir'].'/'.$data->pName.'/data/'.$data->file;
				if(@unlink($d))
					$l= true;
				else
					$l= false;
				$r['pluginsUnlinkThis']= json_encode($l);
			break;
			case "pluginsSave":
				GLOBAL $_MIND;
				if(isset($data->flag))
					$flag= FILE_APPEND;
				else
					$flag= null;
				$d= $_MIND['rootDir'].$_MIND['pluginDir'].'/'.$data->pName.'/data/'.$data->file;
				if(!file_exists($d))
				{
					$f= fopen($d, 'w+');
					fclose($f);
				}
				
				$l= (@file_put_contents($d, $data->content, $flag))? true: false;
				
				$r['pluginsSave']= json_encode($l);
			break;
			case "pluginsLoadFile":
				GLOBAL $_MIND;
				$d= $_MIND['rootDir'].$_MIND['pluginDir'].'/'.$data->pName.'/data/'.$data->file;
				if(!file_exists($d))
					$l= false;
				else{
						$l= Array();
						$l['name']= $data->file;
						$l['address']= $_MIND['pluginDir'].'/'.$data->pName.'/data/'.$data->file;
						$l['size']= filesize($d);
						$l['content']= file_get_contents($d);
						$l['lastChange']= filectime($d);
					}
				$r['pluginsLoadFile']= json_encode($l);
			break;
			case "pluginsMkDir":
				GLOBAL $_MIND;
				$d= $_MIND['rootDir'].$_MIND['pluginDir'].'/'.$data->pName.'/data/'.$data->dir;
				if(!file_exists($d))
					@mkdir($d);
				if(file_exists($d))
					$l= true;
				else
					$l= false;
				$r['pluginsMkDir']= json_encode($l);
			break;
			case "pluginList":
				GLOBAL $_MIND;
				$dAdd= $_MIND['rootDir'].$_MIND['pluginDir'].'/'.$data->pName.'/data/'.$data->dir;
				if(!file_exists($dAdd))
					return $r['pluginList']= json_encode(false);
				$d = dir($dAdd);
				$l= Array();
				while (false !== ($entry = $d->read()))
				{
					$type= (is_dir($dAdd.'/'.$entry))? 'directory':'file';
					if(substr($entry, 0,1) != '.')
						$l[]= Array('name'=>$entry, 'type'=>$type, 'address'=>$_MIND['pluginDir'].'/'.$data->pName.'/data/'.$data->dir.'/'.$entry);
				}
				$d->close();
				
				if(sizeof($l) == 0)
					$l= false;
				
				$r['pluginList']= json_encode($l);
			break;
		}
	}
	
	$objJSON = json_decode(stripslashes($_POST['component']));
	
	for($i=0;$i<sizeof($objJSON);$i++)
	{	
		if(is_string($objJSON[$i]))
		{
			getComponent($objJSON[$i]);
		}else{
				if(@isset($objJSON[$i]->data))
					getComponent($objJSON[$i]->componentName, $objJSON[$i]->data);
				else
					getComponent($objJSON[$i]->componentName);
			 }
	}
	echo json_encode($r);
?>