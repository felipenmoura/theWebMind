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
				$projectList= Project::getProjects($_SESSION['user']['login']);
				$r['projectList']= ($projectList)? json_encode($projectList): 'false';
			break;
			case "home" :
				//$r['home']= json_encode("<iframe style='width:100%;height:100%;border:none;' frameborder='no' src='home_page.php'></iframe>");
				$r['home']= json_encode(file_get_contents('home_page.php'));
			break;
			case "right_panel" :
				$r['right_panel']= json_encode("Painel da direita");
			break;
			case "projectData" :
				$p= new Project($data->name, $_SESSION['user']['login']);
				$r['projectData']= json_encode($p);
			break;
			case "mindApplicationList" :
				$r['mindApplicationList']= json_encode("");
			break;
			case "project" :
				$_POST['pName']= $data->name;
				$r['project']= json_encode($_MIND['fw']->import($_MIND['rootDir'].$_MIND['components'].'/project.php'));
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