<?php
	/**
	 * This class takes care of the version control methods
	 *
	 * @author felipe
	 */
	class VersionManager {
		public static function commit()
		{
			// TODO: commiting a project should save the current
			// situation of the current project
			echo "COMMITING";
		}
		
		public static function setUp()
		{
		}
		
		public static function cleanUp()
		{
			$path= Mind::$currentProject['path']."/temp/";
			$entities= $path."entities~";
			$relations= $path."relations~";

			$fEnt= fopen($entities, "w+");
			$fRel= fopen($relations, "w+");
			if(!$fEnt)
			{
				Mind::write('permissionDenied');
				return;
			}
			ftruncate($fEnt, 0);
			ftruncate($fRel, 0);
			@chmod($entities, 0777);
			@chmod($relations, 0777);


			foreach(Analyst::$entities as &$entity)
			{
				file_put_contents($entities, serialize($entity)."\n", FILE_APPEND);
			}

			foreach(Analyst::$relations as &$relation)
			{
				file_put_contents($relations, serialize($relation)."\n", FILE_APPEND);
			}
			fclose($fEnt);
			fclose($fRel);
		}
	}