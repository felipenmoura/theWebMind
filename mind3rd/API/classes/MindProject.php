<?php
/**
 * Will keep and deal with the current opened project
 *
 * @author felipe
 */
class MindProject extends VersionManager{
	
	public static $sourceContent= Array();
	public static $currentSource= null;
	
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
		if($_REQ['env']=='shell')
		{
			if(file_exists($entities) && $f= fopen($entities, 'r'))
			{
				while (($buffer = fgets($f, 51200)) !== false)
				{
					if($tmpObj= @unserialize($buffer))
						Analyst::$entities[$tmpObj->name]= $tmpObj;
				}
				$f= fopen($relations, 'r');
				while (($buffer = fgets($f, 51200)) !== false)
				{
					if($tmpObj= @unserialize($buffer))
						Analyst::$relations[$tmpObj->name]= $tmpObj;
				}
			}
		}
		
		Mind::write('projectOpened', true, $p['name']);
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
	
	public static function import($src)
	{
		self::$sourceContent[$src]= $src;
		$extraFiles[]= preg_match_all(IMPORT_SOURCE, $src, $matches);
		$matches= $matches[0];
		foreach($matches as &$import)
		{
			$import= substr($import, 8);
			$extraContent= self::loadSource($import);
			self::import($extraContent);
		}
	}
	
	public static function loadSources()
	{
		$main= self::loadSource();
		self::import($main);
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
	public static function analyze($autoCommit=false)
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
		
		self::cleanUp();
	}
}