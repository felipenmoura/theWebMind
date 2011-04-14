<?php
/**
 * Generic instructions to install the system
 *
 * @author felipe
 */
abstract class Setup {
    
    public static $installationOk  = false;
    public static $readLlineOk     = false;
    public static $phpVsOk         = false;
    public static $sqliteOk        = false;
    public static $projectsDir     = false;
    public static $sqliteDir       = false;
    public static $apiDir          = false;
    
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
    
    public static function createDatabase(){
        GLOBAL $_MIND;
		echo "  creating database...\n";
        $sqlite= class_exists('SQLite3')? 'SQLite3': 'SQLiteDatabase';
        $sqliteDDLFile= 'mind3rd/SQLite/ddl.sql';
        $sqliteBaseFile= 'mind3rd/SQLite/mind';
        if(file_exists($sqliteBaseFile))
        {
            echo "  <[warning] Database already exists! It till NOT be touched>\n";
            echo "             If you want to re-install the system, remove the following file:\n";
            echo "             ".str_replace('\\', '/', getcwd()).
                               "/".$sqliteBaseFile."\n";
        }else{
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
                                        type
                                    )VALUES(
                                        'Administrator',
                                        'admin',
                                        '".sha1('admin')."',
                                        'A',
                                        'A'
                                    );");
                    echo "  setting database permissions...\n";

                                if($_MIND['sys']== 'unix')
                                    echo shell_exec('sudo chmod 777 '.getcwd().'/mind3rd/SQLite/mind');
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