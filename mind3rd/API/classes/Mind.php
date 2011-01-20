<?php
	/**
	 * This is the main class
	 * It provides a bunch of static methods to deal with the console
	 * and also, methods and properties to deal with the project and
	 * the system itself
	 * 
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 */
	class Mind
	{
		public $about= null;
		public $defaults= null;
		public $conf= null;
		public static $currentProject= null;
		public static $projectsDir= '';
		public static $pluginList= Array();
		public static $l10n= null;
		public static $triggers= Array();
		public static $modelsDir= "";
		public static $lexer;
		public static $canonic;
		public static $syntaxer;
		public static $tokenizer;
		public static $langPath= "";
		public static $content= "";
		public static $curLang= 'en';

		/**
		 * Verifies wheter the software is installed or not
		 * @return boolean
		 */
		public static function isInstalled()
		{
			return file_exists('mind3rd/SQLite/mind');
		}

		/**
		 * This method returns or outputs messages using the L10N library
		 * You can pass a rich string with %s, %i, etc, sending extra parameters
		 * If the boolean flag $echo is sent, it prints it to the output, otherwise,
		 * only returns it
		 * 
		 * @param String $k
		 * @param Bolean $echo
		 * @param mixed... extra parameter to be treated in the string
		 * @return String
		 */
		public static function write($k, $echo=true)
		{
			$msg= Mind::$l10n->getMessage($k);
			if(!$msg)
			{
				$msg= Mind::message("L10N: Message $k does not exist", '[Fail]', false);
			}
			$args= func_get_args();
			$parms= "";
			if(sizeof($args)>2)
			{
				for($i=2; $i<sizeof($args); $i++)
				{
					$parms.= ', "'.$args[$i].'"';
				}
				$parms= '"'.$msg.'"'.$parms;
				eval("\$print= sprintf(".$parms.");");
			}else{
					$print= $msg;
				 }
			$count= 1;
			while(strlen($print) >= _CONSOLE_LINE_LENGTH_ && strpos($print, '..')>-1)
			{
				$print= preg_replace("/\.\./", '.', $print, $count);
			}
			if($echo)
				echo $print;
			return $msg;
		}

		/**
		 * This method returns or prints a message formated to represent failures, passes
		 * or any kind of alert
		 * 
		 * @param String $message The message itself
		 * @param String $status The status to be shown in the end of the message
		 * @param Boolean $echo if it should be printed or not
		 * @return string
		 */
		public static function message($message, $status, $echo=true)
		{
			$msg= str_pad($message, _CONSOLE_LINE_LENGTH_ - strlen($status), '.').$status."\n";
			if($echo)
				echo $msg;
			return $msg;
		}

		/**
		 * Constructor
		 */
		public function Mind(){
			$path= _MINDSRC_;
			Mind::$projectsDir= $path.'/mind3rd/projects/';
			Mind::$modelsDir= $path.'/mind3rd/API/models/';
			$this->about= parse_ini_file($path.'/mind3rd/env/about.ini');
			$this->defaults= parse_ini_file($path.'/mind3rd/env/defaults.ini');
			$this->conf= parse_ini_file($path.'/mind3rd/env/mind.ini');
			include($path.'/mind3rd/API/L10N/'.$this->defaults['default_human_language'].'.php');
			Mind::$curLang= $this->defaults['default_human_languageName'];
			Mind::$l10n= new $this->defaults['default_human_language']();
			Mind::$langPath= $path.'/mind3rd/API/languages/';
			Mind::$curLang= $this->defaults['default_human_language'];
			$langPath= $path.'/mind3rd/API/languages/'.$this->defaults['default_human_languageName'].'/';
			set_include_path(get_include_path() . PATH_SEPARATOR . $langPath);
		}
		/**
		* function taken from: http://www.dasprids.de/blog/2008/08/22/getting-a-password-hidden-from-stdin-with-php-cli
		* this method should read the passwords from console, not showing any character
		* or replacing them by stars(asterisks)
		* @method readPassword
		* @param Boolan $stars if true, show an * for each typed char
		* @return String password
		*/
		public static function readPassword($stars)
		{
			// Get current style
			$oldStyle = shell_exec('stty -g');

			if ($stars === false) {
				shell_exec('stty -echo');
				$password = rtrim(fgets(STDIN), "\n");
			} else {
				shell_exec('stty -icanon -echo min 1 time 0');

				$password = '';
				while (true) {
					$char = fgetc(STDIN);

					if ($char === "\n") {
						break;
					} else if (ord($char) === 127) {
						if (strlen($password) > 0) {
							fwrite(STDOUT, "\x08 \x08");
							$password = substr($password, 0, -1);
						}
					} else {
						fwrite(STDOUT, "*");
						$password .= $char;
					}
				}
			}

			// Reset old style
			shell_exec('stty ' . $oldStyle);

			// Return the password
			return $password;
		}

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

		static function addPlugin(&$plugin)
		{
			if(in_array($plugin->trigger, Mind::$triggers))
			{
				if(!isset(Mind::$pluginList[$plugin->trigger]))
					Mind::$pluginList[$plugin->trigger]= Array( 'before'=>Array(),
																'after'=>Array());
				Mind::$pluginList[$plugin->trigger][$plugin->event][]= $plugin;
			}
		}

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

		public static function openProject($p)
		{
			$_SESSION['currentProject']= $p['pk_project'];
			$_SESSION['currentProjectName']= $p['name'];
			$_SESSION['currentProjectDir']= Mind::$projectsDir.$p['name'];
			$p['path']= Mind::$projectsDir.$p['name'];
			$p['sources']= Mind::$projectsDir.$p['name'].'/sources';
			Mind::$currentProject= $p;
			if(isset($_SESSION['currentProject']))
			{
				if($_SESSION['currentProject'] == $p['pk_project'])
					return true;
			}
			Mind::write('projectOpened', true, $p['name']);
			return true;
		}
	}