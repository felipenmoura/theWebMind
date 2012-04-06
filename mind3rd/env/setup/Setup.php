<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
/**
 * Generic instructions to install the system.
 *
 * This class provides a bunch of methods to interact with the user's OS and
 * install the application and its database.
 * 
 * NOTE: There are no nacionalization for this class, which only show messages
 * in english.
 * 
 * @abstract
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
abstract class Setup {
    
    public static $installationOk  = false;
    public static $readLlineOk     = false;
    public static $phpVsOk         = false;
    public static $sqliteOk        = false;
    public static $projectsDir     = false;
    public static $sqliteDir       = false;
    public static $apiDir          = false;
    
    /**
     * Returns WIN for Windows or unix for any ther OS.
     * 
     * @return string
     */
    public static function getSO()
    {
        if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
            return 'WIN';
        return 'unix';
    }
    
    public static function __getStatic($what)
    {
        if(isset(self::$what))
           return $what;
        else return false;
    }
    
    /**
     * Performs the installation itself.
     * 
     * This method will install the application calling the install method
     * from the class developed to each specific Operational System.
     */
    public static function install()
    {
        if (self::getSO() !== 'WIN')
        {
            require('mind3rd/env/setup/UnixSetup.php');
            $_MIND['sys']= 'unix';
            UnixSetup::install();
        }else{
                require('mind3rd/env/setup/WinSetup.php');
                $_MIND['sys']= 'win';
                WinSetup::install();
             }
    }
    
    public static function databaseAlreadyExists()
    {
        return file_exists(\KNOWLEDGE_BASE);
    }
    
    /**
     * Verifies the system requirements.
     * 
     * This method sets its own static properties to true or false according
     * to each requirement, returning true if the minimum requirement has been
     * reached or false in other case.
     * 
     * @return boolean Whether all the minimum requirements are ok or not.
     */
    public static function checkRequirements()
    {
        $phpVs= explode('-', phpversion());
        $phpVs= $phpVs[0];
        $phpVs= explode('.', $phpVs);
        $phpVsOk= false;    /*********/
        if($phpVs[0] >5)
            $phpVsOk= true;
        else{
            if($phpVs[0] == 5 && $phpVs[1] >= 3)
                $phpVsOk= true;
        }

        $sqliteOk= false;    /*********/
        if(class_exists('SQLite3'))
           $sqliteOk= true; 

        $readLlineOk= false; /*********/
        if(function_exists('readline'))
            $readLlineOk= true;

        $runDirOk= false;    /*********/
        if(self::getSO() == 'WIN')
        {
            $runDir= str_replace('cmd.exe', '', getenv('COMSPEC'));
            if(is_writable($runDir))
                $runDirOk= true;
        }else{
            $runDir= "/bin/mind";
            if(is_writable("/bin/mind"))
                $runDirOk= true;
        }

        $projectsDir= false;  /*********/
        if(is_writable('mind3rd/projects/'))
            $projectsDir= true;

        $sqliteDir= false;
        if(is_writable('mind3rd/SQLite/'))
            $sqliteDir= true;

        $apiDir= false;
        if(is_writable('mind3rd/API/'))
            $apiDir= true;
        
        self::$phpVsOk      = $phpVsOk;
        self::$readLlineOk  = $readLlineOk;
        self::$sqliteOk     = $sqliteOk;
        self::$projectsDir  = $projectsDir;
        self::$sqliteDir    = $sqliteDir;
        self::$apiDir       = $apiDir;
        
        return self::$installationOk= $phpVsOk && $sqliteOk && $projectsDir && $sqliteDir && $apiDir;
    }
    
    /**
     * Return true if mind was already installed in this server.
     * 
     * @return boolean
     */
	public static function isInstalled(){
		return file_exists(\KNOWLEDGE_BASE);
	}
	
    /**
     * Tries to remove completely the database.
     * 
     * This method has NO ROLLBACK and should be called with parsimony.
     * 
     * @return boolean
     */
    public static function removeDataBase(){
        return unlink(\KNOWLEDGE_BASE);
    }
    
    /**
     * Tries to remove all the created projects.
     * 
     * This method has NO ROLLBACK and should be called with parsimony.
     * 
     * @return boolean
     */
    public static function clearProjects(){
        if($ret = shell_exec("sudo rm -rf ".'.'.\PROJECTS_DIR) == '')
            return mkdir('.'.\PROJECTS_DIR);
        return false;
    }
    
    /**
     * Creates the SQLite DataBase.
     * 
     * This method will create the SQLite database the system will use to work.
     * This method also inserts the admin user, as default, with password admin.
     * 
     * @return type 
     */
    public static function createDatabase(){
        GLOBAL $_MIND;
        echo "  creating database...\n";
        $sqlite= class_exists('SQLite3')? 'SQLite3': 'SQLiteDatabase';
        $sqliteDDLFile= \KNOWLEDGE_DDL;
        $sqliteBaseFile= \KNOWLEDGE_BASE;
        
        if(file_exists($sqliteBaseFile))
        {
            echo "  <[warning] Database already exists! It till NOT be touched>\n";
            echo "             If you want to re-install the system, remove the following file:\n";
            echo "             ".str_replace('\\', '/', getcwd()).
                               "/".$sqliteBaseFile."\n";
        }else{
                $email= 'mail@domain.com';
                $pwd= 'admin';
                if(isset($_POST))
                {
                    if(isset($_POST['adminEmail']))
                        $email= $_POST['adminEmail'];
                    if(isset($_POST['adminPWD']))
                        $email= $_POST['adminPWD'];
                }
                
                if(class_exists($sqlite) && $db = new SQLite3($sqliteBaseFile))
                {
                    $DDL= file_get_contents($sqliteDDLFile);
                    if(!$db->exec($DDL))
                    {
                        echo " <[ERROR] Failed creating the SQLite database!>\n";
                        return false;
                    }
                    echo "  adding the main user...\n";
                    $db->exec("INSERT into user(
                                        name,
                                        login,
                                        pwd,
                                        status,
                                        type,
                                        email
                                    )VALUES(
                                        'Administrator',
                                        'admin',
                                        '".sha1($pwd)."',
                                        'A',
                                        'A',
                                        '".$email."'
                                    );");
                    echo "  setting database permissions...\n";

                    if($_MIND['sys']== 'unix')
                        echo shell_exec('sudo chmod 777 '.getcwd().'/mind3rd/SQLite/mind');
                    chmod($sqliteBaseFile, 0777);
                    
                }else{
                    echo " <[ERROR] SQLite Database could not be created. ".
                         " Is your server working properly with SQLite?>\n";
                    echo "   TIP: Remember that, the php.ini for phpcli may be 
                                  different from the php.ini for your http server\n";
                    exit;
                }
             }
             echo "  Finished\n";
	}
    
    public static function init()
    {
        GLOBAL $_MIND;
    }
}