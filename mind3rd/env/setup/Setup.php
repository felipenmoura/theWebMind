<?php
/**
 * Generic instructions to install the system
 *
 * @author felipe
 */
abstract class Setup {
    public function createDatabase(){
                GLOBAL $_MIND;
		echo "  creating database...\n";
                $sqlite= class_exists('SQLite3')? 'SQLite3': 'SQLiteDatabase';
                $sqliteDDLFile= 'mind3rd/SQLite/ddl.sql';
                $sqliteBaseFile= 'mind3rd/SQLite/mind';
                if(file_exists($sqliteBaseFile))
                {
                    echo "  <[warning] Database already exists! It till NOT be touched>\n";
                    echo "             If you want to re-install the system, remove the followinf file:\n";
                    echo "             ".str_replace('\\', '/', getcwd()).
                                       "/".$sqliteBaseFile."\n";
                    return true;
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
		echo "Finished\n";
	}
}
?>
