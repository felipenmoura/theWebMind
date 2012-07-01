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
	
	public static $sourceContent   = Array();
	public static $currentSource   = null;
	public static $adminValidAttrs = Array('creator', 'info', 'name');
	public static $availableAttrs  = Array('idiom',
                                           'technology',
                                           'title',
                                           'database_drive',
                                           'database_addr',
                                           'database_name',
                                           'database_port',
                                           'database_user',
                                           'database_pwd',
                                           'source');
	
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
     * Retrieves the Project Object, found by its name.
     * 
     * Must be an administrator to call this method.
     * 
     * @param String $pName
     * @return ProjectObject Or false, if does not exist
     */
    public static function getProjectByName($pName){
        if(!\MindUser::isAdmin()){
            \Mind::write('mustBeAdmin');
            return false;
        }
            
        $db= self::getDBConn();
        $project= false;
        $projs= $db->query("SELECT * from project where name = '".addslashes($pName)."'");
        foreach($projs as $k=>$p)
        {
            $project= $p;
            break;
        }
        return $project;
    }
    
    protected static function getDBConn()
    {
        if(!self::$dbConn)
            self::$dbConn= new \MindDB();
        return self::$dbConn;
    }
    
    /**
     * Sets a property of the currend project.
     * 
     * If admin, you can pass the project to be changed.
     * This method actually changes AND PERSISTS the change to the
     * database.
     * 
     * To change the source of a project, the user MUST be with the project opened.
     * Neither Admin users have access to change a source  of a project, without
     * loging into it(opening the project).
     * 
     * @param String $attr
     * @param Mixed $value
     * @param String $proj The project or the source file to be chenged
     * @return boolean
     */
    public static function set($attr, $value, $proj=false)
    {
        if(\in_array($attr, self::$adminValidAttrs) || ($proj && $attr != 'source')){
            if(!\MindUser::isAdmin()){
                \Mind::write('mustBeAdmin');
                return false;
            }
        }
        if(\in_array($attr, self::$adminValidAttrs)){
            if($proj && !is_numeric($proj)){
                $proj= \MindProject::getProjectByName($proj);
                if(!$proj){
                    \MindSpeaker::write('noProject', true, $proj);
                    return false;
                }
                $proj= $proj['pk_project'];
            }
            $pk_project= $proj?
                            \MindProject::getProjectByName($proj):
                            isset(Mind::$currentProject['pk_project'])?
                                Mind::$currentProject['pk_project']:
                                false;
            if(!$pk_project){
                \MindSpeaker::write('noProject', true, $proj);
                return false;
            }
                
            $qr_updProj= "UPDATE project
                             SET ".$attr."=".(is_numeric($val)? $val: "'".$val."'")."
                           WHERE pk_project = ".$pk_project;
            $db= self::getDBConn();
            if($db->execute($qr_updProj))
                return $db->execute('COMMIT');
            else
                return false;
        }else{
            $proj= $proj?
                        $proj:
                        isset(Mind::$currentProject['pk_project'])?
                            Mind::$currentProject['name']:
                            false;
            if(!$proj){
                \MindSpeaker::write('noProject', true, $proj);
                return false;
            }
            $iniSource= Mind::$projectsDir.$proj.'/mind.ini';
            if(!file_exists($iniSource)){
                \MindSpeaker::write('noProject', true, $proj);
                return false;
            }
            try{
                $iniContent= file_get_contents($iniSource);
            }catch(Exception $e){
                \MindSpeaker::write('permissionDenied', true, $proj);
                return false;
            }
            
            $attr= trim($attr);
            if(!\in_array($attr, self::$availableAttrs)){
                \MindSpeaker::write('invalidCreateParams');
                echo "Available list: ".implode(', ', self::$availableAttrs)."\n";
                return false;
            }
            
            if($attr == 'source'){
                $srcs= Mind::$currentProject['sources'];
                $source= func_get_arg(2);
                if(!$source)
                    $source= 'main';
                //echo "\n\n".$srcs.'/'.addslashes($source).'.mnd'."\n\n";
                if(file_put_contents($srcs.'/'.addslashes($source).'.mnd', $value))
                    return true;
                Mind::write('permissionDenied');
                return false;
            }else{
                $iniContent= preg_replace("/".$attr."(( |\t)+)?=.+(\n|$)/", $attr."=".str_replace('"', '', $value)."\n", $iniContent);
                try{
                    file_put_contents($iniSource, $iniContent);
                    \MindProject::reload();
                    return true;
                }catch(Excepption $e){
                    \MindSpeaker::write('permissionDenied', true, $proj);
                    return false;
                }
            }
        }
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
        $hasProject= "SELECT ".($detailed? " pk_project, p.name as name,
                                             info, creator,
                                             dt_creation":
                                          "  distinct pk_project,
                                             name")."
						from project p
					 ";
        
        if(!\API\User::isAdmin())
            $hasProject.= ", project_user pu
                      where  pu.fk_project = p.pk_project
                             and pu.fk_user = ".\API\User::code()."
                             and p.status='A'";
        else
            $hasProject.= " WHERE p.status='A'";
        
		$data= $db->query($hasProject);
        return $data;
    }
    
    /**
	 * Returns true if the project already exists,
	 * false, otherwise
	 *
	 * @global Mind $_MIND
	 * @param String $project
	 * @return Mixed False, or an Array with the project ID, Name and creator
	 */
	static function hasProject($project, $u=false)
	{
		GLOBAL $_MIND;
		$projectfile= Mind::$projectsDir.$project;
		$noAccess= true;

		$db= new MindDB();
		$hasProject= "SELECT pk_project,
                             creator,
							 project.name as name
						from project_user,
							 project
					   where name = '".$project."'
						 and fk_project = pk_project
					 ";
        
        if(!\MindUser::isAdmin())
            $hasProject.= " and fk_user= ".$_SESSION['pk_user'];
        else if($u)
                $hasProject.= " and fk_user= ".((int)$u);
        
		$data= $db->query($hasProject);
		if(sizeof($data)>0)
			foreach($data as $row)
			{
				$noAccess= false;
				break;
			}

		if(!file_exists($projectfile) || $noAccess)
		{
            if($u && \MindUser::isAdmin())
                return false;
			\MindSpeaker::write('noProject', true, $project);
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
		$hasProject= "SELECT pk_project,
                             creator,
							 project.name as name
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
					   where fk_project = pk_project
                       ";
        if(!\API\User::isAdmin())
            $hasProject.= "  and fk_user= ".$user."
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

    public static function renew(){
        if(isset($_SESSION['currentProjectName']) && $_SESSION['currentProjectName']){
            if(!$projectData= \Mind::hasProject($_SESSION['currentProjectName']))
                return false;
            \MindProject::close();
            \Mind::openProject($projectData, true);
        }else{
            \MindSpeaker::write('currentProjectRequired');
        }
    }
    public static function reload(){
        self::renew();
    }
    
    public static function close(){
        $_SESSION['currentProject']= false;
        Mind::$project= false;
        Mind::$currentProject= false;
        //session_unset($_SESSION['currentProject']);
    }
    
	/**
	 * Loads data from the passed project
	 *
	 * @param AssocArray $p
	 * @return boolean
	 */
	public static function openProject($p, $silent=false)
	{
		GLOBAL $_REQ;
        if(Mind::$project)
        {
            if($_SESSION['currentProject'] != $p['pk_project'])
                self::close();
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
         
        if($msg && !$silent)
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