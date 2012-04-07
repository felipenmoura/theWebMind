<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
/**
 * Will keep and deal with the current opened project.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
class MindProject extends VersionManager{
	
	public static $sourceContent= Array();
	public static $currentSource= null;
	
	/**
	 * Uses the QueryFactory to return the sql to create all the database.
	 * 
	 * @static
	 * @param boolean $header shows the header, or not
	 * @return string The entire SQL command to create the database.
	 */
	public static function showSQL($header= true, $table='*', $command='createTable')
	{
		GLOBAL $_REQ;
		\DQB\QueryFactory::$showHeader= $header;
		\DQB\QueryFactory::setUp(Mind::$currentProject['database_drive']);
		\DQB\QueryFactory::buildQuery($table, $command);
		$qrs= \DQB\QueryFactory::getCompleteQuery($_REQ['env']=='http', false, 'string');
		return $qrs;
	}
	
	/**
	 * Uses the QueryFactory to return the sql to create all the database.
	 * 
	 * This method returns an array with all the sql statements, instead
	 * of a long string.
	 * 
	 * @static
	 * @param boolean $header shows the header, or not
	 * @return string An array with the query commands to create the database.
	 */
	public static function getSQL($header= false)
	{
		GLOBAL $_REQ;
		\DQB\QueryFactory::$showHeader= $header;
		\DQB\QueryFactory::setUp(Mind::$currentProject['database_drive']);
		\DQB\QueryFactory::buildQuery();
		$qrs= \DQB\QueryFactory::getCompleteQuery($_REQ['env']=='http', false, 'array');
		return $qrs;
	}
	
    /**
     * Returns the list of registered and active projects.
     * 
     * @return Array
     * @param boolean $detailed
     */
    public static function listProjects($detailed=false)
    {
        $db= new MindDB();
        $hasProject= "SELECT ".($detailed? " pk_project,project.name as name,
                                             info, creator,
                                             dt_creation":
                                          "  distinct pk_project,
                                             project.name as name")."
						from project
					 ";
        
        if(!\API\User::isAdmin())
            $hasProject.= ", project_user
                      where  fk_project = pk_project
                             and fk_user = ".\API\User::code()."
                             and project.status='A'";
        else
            $hasProject.= " WHERE project.status='A'";
        
		$data= $db->query($hasProject);
        return $data;
    }
    
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
    
    /**
	 * Returns true if the project already exists,
	 * false, otherwise
	 *
	 * @global Mind $_MIND
	 * @param String $project
	 * @return boolean
	 */
	public static function projectExists($projectName)
	{
		GLOBAL $_MIND;
		$projectfile= Mind::$projectsDir.$projectName;
		$noAccess= true;

		$db= new MindDB();
		$hasProject= "SELECT pk_project
						from project
					   where project.name = '".$projectName."'
					 ";
		$data= $db->query($hasProject);
		if(sizeof($data)>0)
            return $data;
		return false;
	}
    
    public static function projectsList($user=false)
    {
		GLOBAL $_MIND;
        
        $user= $user? $user: $_SESSION['pk_user'];

		$db= new MindDB();
		$hasProject= "SELECT pk_project,
							 project.name as name
						from project_user,
							 project
					   where fk_user= ".$user."
						 and fk_project = pk_project
					 ";
		$data= $db->query($hasProject);
        return $data;
    }

	public static function loadIdiom($idiom)
	{
		$idiom= str_replace('\\', DIRECTORY_SEPARATOR, $idiom);
		$langPath= LANG_PATH.$idiom.'/';
		
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
		GLOBAL $_REQ;
        if(Mind::$project)
        {
            if($_SESSION['currentProject'] != $p['pk_project'])
                Mind::$project->close();
            else
                return Mind::$project;
        }
        
        $msg= false;
        if(isset($_SESSION['currentProject']) &&
           $_SESSION['currentProject'] != $p['pk_project'])
            $msg= true;
        
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
		
		// loading entities and relations from cache
		$path= Mind::$currentProject['path']."/temp/";
		$entities= $path."entities~";
		$relations= $path."relations~";
		
		$pF= new DAO\ProjectFactory(Mind::$currentProject);
		Mind::$currentProject['version']= $pF->data['version'];
		Mind::$currentProject['pk_version']= $pF->data['pk_version'];
		Mind::$project= $pF;
         
        if($msg)
        {
            Mind::write('projectOpened', true, $p['name']);
        }
		return true;
	}
	
	public static function loadSource($source='main')
	{
		$srcs= Mind::$currentProject['sources'];
		if(file_exists($srcs.'/'.$source.'.mnd'))
		{
			$main= file_get_contents($srcs.'/'.$source.'.mnd');
			return $main;
		}
		Mind::write('sourceFileNotFound', true, $source);
		return false;
	}
	
	public static function import($src, $sourceFile='main')
	{
		self::$sourceContent[$sourceFile]= $src;
		$extraFiles[]= preg_match_all(IMPORT_SOURCE, $src, $matches);
		$matches= $matches[0];
		foreach($matches as &$import)
		{
			$import= substr($import, 8);
			$extraContent= self::loadSource($import);
			self::import($extraContent, $import);
		}
	}
	
	public static function loadSources()
	{
		$main= self::loadSource();
		self::import($main, 'main');
	}
	
	public static function setUp()
	{
		parent::setUp();
		self::$sourceContent= false;
		self::$currentSource= null;
		self::$sourceContent= Array();
		Analyst::reset();
				
		MindTimer::init();
		Mind::$lexer= new Lexer();
		self::loadSources();
	}
	
	/**
	 * Analyzes the current project.
	 * 
	 * @param boolean $autoCommit
	 * @param boolean $echo 
	 */
	public static function analyze($autoCommit=false, $echo=true)
	{
		self::setUp();
		$init= false;
		
		foreach(self::$sourceContent as $k=>&$content)
		{
			$currentSource= $k;
			// search for special/unknown characters
			if(!Mind::$lexer->sweep($content))
				continue;
			// keep substantives and verbs on their canonical form
			if(!Mind::$canonic->sweep())
				continue;
			// mark specific tokens
			if(!Mind::$tokenizer->sweep())
				continue;
			// prepares the model to be used to process data
			// it transforms the original text into the mind code
			// itself
			if(!Mind::$syntaxer->sweep())
				continue;

			if($autoCommit)
			{
				MindProject::commit();
			}
            
			if(sizeof(Analyst::$entities) > 0)
				$init= true;
		}
		
		MindTimer::end();

		if($echo)
		{
			if($init)
				echo Analyst::printWhatYouGet();
			else
				echo "    Nothing to show\n";
			echo "--------------------\n";
			echo "Time: ".
					MindTimer::getElapsedTime().
				 "s\n";
			$memory= ((memory_get_usage() / 1024)/1024);
			$memory= number_format($memory, 2);
			echo "Memory: ".$memory."MBs\n";
		}
		self::cleanUp();
	}
}