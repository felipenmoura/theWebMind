<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
	/**
	 * The class which will deal with Directories and files.
     * This is not the most complete fileManager and will be used by the
     * application's core. To deal in a more advanced way with files, or to
     * interact easily with files within a project, use the FileManager class.
	 *
	 * @author felipe
	 */
	class MindDir {
		/**
		* This method will copy the whole directory, recursively
		* but it is focused to the "generate project" tool
		* @author Felipe Nascimento
		* @name copyDir
		* @param String $source
		* @param String $dest
		* @param [String $flag]
		* @return boolean
		*/
		static function copyDir($source, $dest, $flag= false)
		{
			  // Simple copy for a file
			  if($flag)
			  {
				$s= '...'.substr($source, -30);
				showLoadStatus("Copying ".$s, $_SESSION['currentPerc']);
			  }
			  if (is_file($source))
			  {
				  $c = copy($source, $dest);
				  chmod($dest, 0777);
				  return $c;
			  }
			  // Make destination directory
			  if(!is_dir($dest))
			  {
				$oldumask = umask(0);
				mkdir($dest, 0777);
				umask($oldumask);
			  }
			  // Loop through the folder
			  $dir = dir($source);
			  while(false !== $entry = $dir->read())
			  {
				  // Skip pointers
				  if ( in_array($entry, array(".","..",".svn") ) )
				  {
					continue;
				  }
				  // Deep copy directories
				  if ($dest !== "$source/$entry")
				  {
					Mind::copyDir("$source/$entry", "$dest/$entry", $flag);
				  }
			  }
			  // Clean up
			  $dir->close();
			  return true;
		}

		/**
		* Removes recusrively a directory
		* @author thiago <erkethan@free.fr>
		* @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
		* @method deleteDir
		* @param String $dir
		* @return boolean
		*/
		static function deleteDir($dir)
		{
			if(!file_exists($dir))
				return true;
			if(!is_dir($dir) || is_link($dir))
				return unlink($dir);
			foreach(scandir($dir) as $item)
			{
				if ($item == '.' || $item == '..')
					continue;
				if(!$this->deleteDir($dir . "/" . $item))
				{
					chmod($dir . "/" . $item, 0777);
					if(!$this->deleteDir($dir . "/" . $item))
						return false;
				}
			}
			return rmdir($dir);
		}
		
	}