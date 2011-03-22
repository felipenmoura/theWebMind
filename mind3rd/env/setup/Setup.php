<?php
/**
 * Generic instructions to install the system
 *
 * @author felipe
 */
abstract class Setup {
    public function createDatabase(){
		echo "  creating database...\n";
		if($db = new SQLiteDatabase('mind3rd/SQLite/mind'))
		{
			$DDL= file_get_contents('mind3rd/SQLite/ddl.sql');
			if(!$db->queryExec($DDL))
			{
				echo " <[INFO] Database already exists...it wont be touched>\n";
			}
			echo "  adding the main user...\n";
			$db->queryExec("INSERT into user(
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
			echo shell_exec('sudo chmod 777 '.getcwd().'/mind3rd/SQLite/mind');
		}else{
			echo " <[ERROR] SQLite Database could not be created. ".
				 " Is your server working properly with SQLite?>\n";
			exit;
		}
		echo "Finished\n";
	}
}
?>
