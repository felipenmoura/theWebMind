<?php
/**
 * Will keep and deal with the current opened project
 *
 * @author felipe
 */
class MindProject {
    /**
	 * Returns true if the project already exists,
	 * false, otherwise
	 *
	 * @global Mind $_MIND
	 * @param String $project
	 * @return boolean
	 */
	static function hasProject($project)
	{
		GLOBAL $_MIND;
		$projectfile= Mind::$projectsDir.$project;
		$noAccess= true;

		$db= new MindDB();
		$hasProject= "SELECT pk_project,
							 project.name as name
						from project_user,
							 project
					   where fk_user= ".$_SESSION['pk_user']."
						 and project.name = '".$project."'
						 and fk_project = pk_project
					 ";
		$data= $db->query($hasProject);
		if(sizeof($data)>0)
			foreach($data as $row)
			{
				$noAccess= false;
				break;
			}

		if(!file_exists($projectfile) || $noAccess)
		{
			Mind::write('noProject', true, $project);
			return false;
		}
		return $row;
	}

	public static function loadIdiom($idiom)
	{
		$idiom= str_replace('\\', DIRECTORY_SEPARATOR, $idiom);
		$langPath= $path.'/mind3rd/API/languages/'.$idiom.'/';
		
		set_include_path(get_include_path() . PATH_SEPARATOR . $langPath);
	}

	/**
	 * Loads data from the passed project
	 *
	 * @param AssocArray $p
	 * @return boolean
	 */
	public static function openProject($p)
	{
		$_SESSION['currentProject']= $p['pk_project'];
		$_SESSION['currentProjectName']= $p['name'];
		$_SESSION['currentProjectDir']= Mind::$projectsDir.$p['name'];
		$p['path']= Mind::$projectsDir.$p['name'];
		$p['sources']= Mind::$projectsDir.$p['name'].'/sources';
		$ini= parse_ini_file(Mind::$projectsDir.$p['name'].'/mind.ini');
		$p= array_merge($p, $ini);

		Mind::$currentProject= $p;
		Mind::$curLang= Mind::$currentProject['idiom'];
		Mind::$content= '';
		Mind::write('projectOpened', true, $p['name']);
		return true;
	}
}
?>
