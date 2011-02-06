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
		public static $ref= Array();
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
		public static $originalContent= "";
		public static $curLang= 'en';

		/**
		 * Verifies wheter the software is installed or not
		 * @return boolean
		 */
		public static function isInstalled()
		{
			return file_exists(_MINDSRC_.'/mind3rd/SQLite/mind');
		}

		/**
		 * This method returns or outputs messages using the L10N library
		 * You can pass a rich string with %s, %i, etc, sending extra parameters
		 * If the boolean flag $echo is sent, it prints it to the output, otherwise,
		 * only returns it
		 * This is an alias for MindSpeaker::write
		 * 
		 * @param String $k
		 * @param Bolean $echo
		 * @param mixed... extra parameter to be treated in the string
		 * @return String
		 */
		public static function write($k, $echo=true)
		{
			return MindSpeaker::write($k, $echo, func_get_args());
		}

		/**
		 * This method returns or prints a message formated to represent failures, passes
		 * or any kind of alert
		 * This is an alias for MindSpeaker::message
		 *
		 * @param String $message The message itself
		 * @param String $status The status to be shown in the end of the message
		 * @param Boolean $echo if it should be printed or not
		 * @return string
		 */
		public static function message($message, $status, $echo=true)
		{
			return MindSpeaker::message($message, $status, $echo);
		}
		
		/**
		 * Alias for MindCommand::readPassword
		 * @param String $stars
		 * @return String $password
		 */
		public static function readPassword($stars)
		{
			return MindCommand::readPassword($stars);
		}

		/**
		* This method will copy the whole directory, recursively
		* but it is focused to the "generate project" tool
		* This is an alias for MindDir::copyDir
		*
		* @author Felipe Nascimento
		* @name copyDir
		* @param String $source
		* @param String $dest
		* @param [String $flag]
		* @return boolean
		*/
		static function copyDir($source, $dest, $flag= false)
		{
			return MindDir::copyDir($source, $dest, $flag);
		}

		/**
		* Removes recusrively a directory
		* @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
		* @method deleteDir
		* @param String $dir
		* @return boolean
		*/
		static function deleteDir($dir)
		{
			return MindDir::deleteDir($dir);
		}

		/**
		 * Adds a MindPlugin based object to the
		 * plugins and triggers list
		 * Alias for MindPlugin::addPlugin
		 * 
		 * @param MindPlugin $plugin
		 */
		static function addPlugin(&$plugin)
		{
			MindPlugin::addPlugin($plugin);
		}

		/**
		 * Returns true if the project already exists,
		 * false, otherwise
		 * Alias for MindProject::hasProject
		 *
		 * @global Mind $_MIND
		 * @param String $project
		 * @return boolean
		 */
		static function hasProject($project)
		{
			GLOBAL $_MIND;
			return MindProject::hasProject($project);
		}

		/**
		 * Loads data from the passed project
		 * Alias for MindProject::openProject
		 *
		 * @param AssocArray $p
		 * @return boolean
		 */
		public static function openProject($p)
		{
			return MindProject::openProject($p);
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

			$langPath= $path.'/mind3rd/API/languages/';//.$this->defaults['default_human_languageName'].'/';
			set_include_path(get_include_path() . PATH_SEPARATOR . $langPath);
		}
	}