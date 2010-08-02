<?php
	// here, we'll simply prepare the url to find the root directory wherever we are
	$slashes= '';
	if(!is_dir('restrict'))
	{
		$curDir= 'config/mind.php';
		$slashes= '';
		while(!file_exists($curDir))
		{
			$curDir= '../'.$curDir;
			$slashes.= '../';
		}
	}
	$_MIND['rootDir']= $slashes;
	
	// this function does nothing. It's called when you want some error to be neutralized
	function _MINDNeutralizeError($errno, $errstr, $errfile, $errline)
	{}
	
	/**
	 *	CLass: Mind
	 *	this is the framework itself
	 *  @package framework
	 *  @author Felipe Nascimento
	*/
	class Mind
	{
		private $output;

		/**
		* this method is used to force the utput to the client
		* @author Felipe Nascimento
		* @name forceFlush
		* @return void
		*/
		function forceFlush()
		{
			ob_start();
			ob_end_clean();
			flush();
			set_error_handler('_MINDNeutralizeError');
			ob_end_flush();
			restore_error_handler();
		}
		
		/**
		* this mathod will remove directories, even if they are not empty. It works recursively
		* @author Felipe Nascimento
		* @name removeDir
		* @param String $dir
		* @return void
		*/
		function removeDir($dir)
		{
			$dir .= "/";
			$files = glob($dir . '*', GLOB_MARK);
			foreach($files as $file){
				if($file != ".svn"){
					if(substr( $file, -1 ) == '/')
						delTree($file);
					else
						unlink($file);
				}
			}
			rmdir($dir);
		}
		
		/**
		* this method is called to build the main client structure, replaceing specific tags applying values to the template
		* @author Felipe Nascimento
		* @name apply
		* @param String $str
		* @return String $str
		*/
		function apply($str)
		{
			GLOBAL $_MIND;
			reset($_MIND);
			foreach($_MIND as $cur)
			{
				$tmp= (isset($_MIND[key($_MIND)]))? $_MIND[key($_MIND)]: false;
				if(is_string($tmp) || is_bool($tmp))
					$str= str_replace('{?$_MIND[\''.key($_MIND).'\']}', $tmp, $str);
				next($_MIND);
			}
			return $str;
		}
		
		/**
		* this method does not really include the file, but loads its results.
		* instead of running the included file where it was included, it loads only the output of that script
		* @author Felipe Nascimento
		* @name import
		* @param String $url
		* @return String $scriptOutput
		*/ 
		function import($url)
		{
			ob_start();
			include($url);
			$scriptOutput= ob_get_contents();
			ob_end_clean();
			return $scriptOutput;
		}
		
		
		/**
		* smaller alias for simplexml_load_file
		* @author Felipe Nascimento
		* @name loadXML
		* @param String $fileURL
		* @return SimpleXMLObject $obj
		*/
		function loadXML($fileURL)
		{
			return simplexml_load_file($fileURL);
		}
		
		/**
		* with this function you can create a new XML file already with the required patterns applied
		* This method returns a SimpleXML Object. If the file doesn't exist, it will be created
		* E.g.: $myXML= $_Mind['fw']->mkXML('my_xml.xml');
		* 		$myXML->addChild('my_tag');
		* @author Felipe Nascimento
		* @name mkXML
		* @param String $fileURL
		* @return SimpleXMLObject $obj
		*/
		function mkXML($fileURL)
		{
			$f = fopen($fileURL,"w+");
			fwrite($f,'<?xml version="1.0" encoding="UTF-8" ?><root></root>');
			fclose($f);
			return simplexml_load_file($fileURL);
		}
		
		/**
		* This method saves the current situation of a SimpleXMLObject to a file
		* as XML. If the file doesn't exist, it will be created
		* @author Felipe Nascimento
		* @name saveXML
		* @param SimpleXMLObject $xml
		* @param String $fileURL
		* @return boolean
		*/
		function saveXML($xml, $fileURL)
		{
			$f = fopen($fileURL, "w+");
			fwrite($f, $xml->asXML());
			if(fclose($f))
				return true;
			else
				return false;
		}
		
		/**
		* This method can be used to encode the pwd. If this algorithm should change, we can simply change this method
		* @author Felipe Nascimento
		* @name getEncodedPwd
		* @param String $pwd
		* @return MD5_String 
		*/
		function getEncodedPwd($pwd)
		{
			return md5($pwd);
		}
		
		/**
		* You can use this method to filter some special chars. Any new future special char can be added here
		* It also replaces any numbers to "_" 
		* @author Felipe Nascimento
		* @name getEncoded
		* @param String $n
		* @return String
		*/
		function getEncoded($n)
		{
			$n= utf8_decode($n);
			$n= addslashes(strip_tags(preg_replace('/[\!\@\#\$\%\&\*\(\)\\_\-\=\+\^\~\,\.\{\[\]\}\?\"\']\;\/\:/', '', $n)));
			$n= preg_replace('/[áàâã]/i', 'a', $n);
			$n= preg_replace('/[éèêẽ]/i', 'e', $n);
			$n= preg_replace('/[íìîĩï]/i', 'i', $n);
			$n= preg_replace('/[óòôõö]/i', 'o', $n);
			$n= preg_replace('/[úùûũü]/i', 'u', $n);
			$n= preg_replace('/ç/i', 'c', $n);
			$n= preg_replace('/ñ/i', 'n', $n);
			//$n= preg_replace('/^[0-9]/', '_', $n);
			return $n;
		}
		
		/**
		* This method is responsable to replace the special tags and prepare all the client interface applying the theme and template
		* The resulted output is set to $this->output property;
		* @author Felipe Nascimento
		* @name mountIde
		* @return void
		*/
		function mountIde()
		{
			GLOBAL $_MIND;
			$ide= file_get_contents($_MIND['fwComponents'].'/ide.php');
			$ide= $this->apply($ide);
			$menus= file_get_contents($_MIND['fwComponents'].'/menus.php');
			$ide= str_replace('{?$_MIND_MENUS}', $menus, $ide);
			$this->output.= $ide.'<br>';
		}
		
		/**
		* It echoes and returns the prepared output for interface
		* @author Felipe Nascimento
		* @name output
		* @return String
		*/
		function output()
		{
			echo $this->output;
			return $this->output;
		}
		
		/**
		* Ouputs a patternized message of error, to be treated by the javascript framework running on the client side
		* @author Felipe Nascimento
		* @name errorOutput
		* @param Integer $erCod
		* @return ErrorObject
		*/
		function errorOutput($erCod)
		{
			GLOBAL $_MIND;
			include($_MIND['rootDir'].'/'.$_MIND['errorMessagesFile']);
			return new Error($erCod);
		}
	
		/**
		* logs the message to the correct log file
		* use the following types:
		* "server conf" due to any requirement the server has not satisfied
		* "error", "warning" or "general" when it was not defined or specified
		* "encoding" when related to localization, idiom, charset or timezone
		* @author Felipe Nascimento
		* @name log
		* @param Strig $message
		* @param Strig $type server conf/error/warning/general/enconding
		* @return boolean
		*/
		function log($message, $type)
		{
			GLOBAL $_MIND;
			if(!preg_match('/log|server conf|error|warning|general|encoding/', $type))
			{
				$this->log('Impossible to log message! Invalid error type.', 'log');
				return false;
			}
			$f= fopen($_MIND['rootDir'].'/'.$_MIND['logDirectory'].'/'.$type.'.log', 'a');
			fwrite($f, $_SESSION['user']['login'].' -- '.date('M/d/Y H:i:s').': '.$message."\n");
			fclose($f);
			return true;
		}
		
		/**
		* This method also transmits the error to the client side, but calling a specific method from the js MindFramework
		* to treat the error
		* @author Felipe Nascimento
		* @name ouputPane
		* @param String/ErrorObject $m
		* @param boolean $flag -- indicates a fatal message, to end the script
		* @return numeric Array
		*/
		function outputPane($m, $flag= false)
		{
			GLOBAL $_MIND;
			$this->output.= $m.'<br>';
			if(is_string($m))
				echo "Mind.Dialog.ShowMessage('".$m."'); ";
			else
				echo "Mind.Dialog.ShowError(".JSON_encode($_MIND['fw']->errorOutput(8))."); ";
			if(!$flag)
				exit;
		}
		
		/**
		* Here, we will load all the extra configuration files indicated into our conf file
		* @author Felipe Nascimento
		* @name loadExternal
		* @return boolean
		*/
		function loadExternal()
		{
			GLOBAL $_MIND;
			reset($_MIND['load']);
			while($m= current($_MIND['load']))
			{
				if(!include($_MIND['load'][key($_MIND['load'])]))
				{
					$this->outputPane("Error when trying to load the config file <b>".key($_MIND['load'])."</b>");
					return false;
				}
				next($_MIND['load']);
			}
			return true;
		}
		
		/**
		* Constructor
		* @author Felipe Nascimento
		* @name _construct
		* @return void
		*/
		public function _construct()
		{
			$this->output= '';
		}
		
		/**
		* Another different option to apply filters to our received data
		* This method add slashes the our quots and clears all the tags
		* @author Felipe Nascimento
		* @name filter
		* @param String $str
		* @return String
		*/
		public function filter($str)
		{
			return addslashes(strip_tags($str));
		}
		
		/**
		* Gets the list of possible idioms
		* @author Felipe Nascimento
		* @name getLanguages
		* @return Array
		*/
		function getLanguages()
		{
			$c=0;
			GLOBAL $_MIND;
			$d = dir($_MIND['rootDir'].$_MIND['languageDir']);
			$ar= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0, 1) != '.' && is_dir($_MIND['rootDir'].$_MIND['languageDir']."/".$entry))
				{
					$ar[]= $entry;
				}
				$c++;
			}
			$d->close();
			return $ar;
		}
		
		/**
		* Get the list of avaliable Users
		* @author Felipe Nascimento
		* @name getUsers
		* @return UserObject Collection
		*/
		function getUsers()
		{
			return User::getUsers();
		}
		
		/**
		* Lists the registered DBMSs
		* @author Felipe Nascimento
		* @name getDBMS
		* @return Array
		*/
		function getDBMSs()
		{
			$c=0;
			GLOBAL $_MIND;
			$d = dir($_MIND['rootDir'].$_MIND['dbmsDir']);
			$ar= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0, 1) != '.' && is_dir($_MIND['rootDir'].$_MIND['dbmsDir'].'/'.$entry))
				{
					$ar[]= $entry;
				}
				$c++;
			}
			$d->close();
			return $ar;
		}
		
		/**
		* Used to copy whole directories. Special use to update your project related to the teamwork
		* @author Felipe Nascimento
		* @name updateCopy
		* @param String $source
		* @param String $dest
		* @return boolean
		*/
		function updateCopy($source, $dest)
		{
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
				  if ($entry == "." || $entry == "..")
				  {
					continue;
				  }
				  // Deep copy directories
				  if ($dest !== "$source/$entry")
				  {
					$this->copyDir("$source/$entry", "$dest/$entry");
				  }
			  }
			  // Clean up
			  $dir->close();
			  return true;
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
		function copyDir($source, $dest, $flag= false)
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
					$this->copyDir("$source/$entry", "$dest/$entry", $flag);
				  }
			  }
			  // Clean up
			  $dir->close();
			  return true;
		}
		
		/**
		* Removes recusrively a directory
		* @author thiago <erkethan@free.fr>
		* @name deleteDirectory
		* @param String $dir
		* @return boolean
		* [EDITOR NOTE: "Credits to erkethan at free dot fr." - thiago]
		*/
		function deleteDirectory($dir)
		{
			if(!file_exists($dir))
				return true;
			if(!is_dir($dir) || is_link($dir))
				return unlink($dir);
			foreach(scandir($dir) as $item)
			{
				if ($item == '.' || $item == '..')
					continue;
				if(!$this->deleteDirectory($dir . "/" . $item))
				{
					chmod($dir . "/" . $item, 0777);
					if(!$this->deleteDirectory($dir . "/" . $item))
						return false;
				};
			}
			return rmdir($dir);
			// [EDITOR NOTE: "Credits to erkethan at free dot fr." - thiago]
		} 
		
		/**
		* Add here, some rules to filter specific information of the pushed info from client
		* @author Felipe Nascimento
		* @name treatClientInfo
		* @param String $clientInfo
		* @return String
		*/
		function treatClientInfo($clientInfo)
		{
			return $clientInfo;
		}
		
		/**
		* A method to decrypt data. For now, it does nothing, but you can add specification here
		* @author Felipe Nascimento
		* @name decrypt
		* @param String $text
		* @return String
		*/
		function decrypt($text)
		{
			return $text;//base64_decode(convert_uudecode($text));
		}
		
		/**
		* A method to encrypt data. For now, it does nothing, but you can add specification here
		* @author Felipe Nascimento
		* @name encrypt
		* @param String $text
		* @return String
		*/
		function encrypt($text)
		{
			return $text;//convert_uuencode(base64_encode($text));
		}
		
		/**
		* Transletes an Object into an Array
		* It is basicaly an alias to get_object_vars
		* @author Felipe Nascimento
		* @name objectToArray
		* @param Object $obj
		* @return Array
		*/
		function objectToArray($obj)
		{
			return get_object_vars($obj);
		}
		
		/**
		* Parses a SimpleXML object into an Array
		* @author Felipe Nascimento
		* @name decrypt
		* @param String $text
		* @return String
		*/
		function xmlObjectToArray($obj)
		{
			return get_object_vars($obj);
		}
		
		/**
		* Loads the list of users
		* @author Felipe Nascimento
		* @name getUserList
		* @return Array
		*/
		function getUsersList($x='')
		{
			GLOBAL $_MIND;
			
			$dir= $x.$_MIND['rootDir'].$_MIND['userDir'];
			$d = dir($dir);
			
			$users= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0,1) !='.')
					array_push($users, $entry);
			}
			return $users;
		}
		
		/**
		* Loads the list of avaliable Modules
		* @author Felipe Nascimento
		* @name getModulesList
		* @return Array
		*/
		function getModulesList($x='')
		{
			GLOBAL $_MIND;
			
			$dir= $x.$_MIND['rootDir'].$_MIND['moduleDir'];
			$d = dir($dir);
			
			$modules= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0,1) !='.')
					array_push($modules, $entry);
			}
			return $modules;
		}
		
		/**
		* Loads a specific module
		* @author Felipe Nascimento
		* @name getModule
		* @param String $mName
		* @return ModuleObject
		*/
		function getModule($mName)
		{
			$m= new Module($mName);
			return $m;
		}
		
		/**
		* an alternative to the default output, to help when deugging
		* @author Felipe Nascimento
		* @name printR
		* @param mixed $var
		* @return void
		*/
		function printR($var)
		{
			echo "<pre>";
			print_r($var);
			echo "</pre>";
		}
		
		/**
		* Fixes names to be used into the database or to name files
		* @author Felipe Nascimento
		* @name fixName
		* @param String $str
		* @return String
		*/
		public function fixName($str)
		{
			return preg_replace("/[^a-zA-Z0-9_]/", "", strtr(utf8_decode($str), "áàâãéêíóôõüçáàâãéêÍÓÔÕÜÇ- ", "aaaaeeioooucAAAAEEIOOOUC__"));
		}
		
		/**
		* Loads the list of past saved ER Diagrams
		* @author Felipe Nascimento
		* @name getSavedERDList
		* @param String $projectName
		* @return Array
		*/
		function getSavedERDList($p)
		{
			GLOBAL $_MIND;
			$d= $_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$p.'/mind/er/';
			if(!file_exists($d))
				mkdir($d, 0777);
			$d= dir($d);
			$der= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0,1) !='.')
					array_push($der, preg_replace('/\.json$/', '', $entry));
			}
			return $der;
		}
		
		/**
		* Loads the data from a specific saved ER Diagram
		* @author Felipe Nascimento
		* @name getSavedERDItem
		* @param String $projectName
		* @param String $diagramName
		* @return JSon
		*/
		function getSavedERDItem($p, $d)
		{
			GLOBAL $_MIND;
			$d= file_get_contents($_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$p.'/mind/er/'.$d.'.json');
			return $d;
		}
		
		/**
		* Loads the list of registered plugins
		* @author Felipe Nascimento
		* @name getPlugins
		* @return Array
		*/
		function getPlugins()
		{
			GLOBAL $_MIND;
			$d= $_MIND['rootDir'].$_MIND['pluginDir'];
			
			$d= dir($d);
			$pList= Array();
			$pListName= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0, 1) != '.' && !isset($pListName[$entry]))
				{
					$pList[]= new Plugin($entry);
					$pListName[$entry]= true;
				}
			}
			return $pList;
		}
		
		/**
		* Gets the current version of the current project
		* @author Felipe Nascimento
		* @name currentVersion
		* @param &ProjectObject $p
		* @return Array
		*/
		function currentVersion(&$p)
		{
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'].$_MIND['publishDir'].'/'.$p->name;
			$dir.= '/conf.xml';
			
			if($p->hasProject($p->name))
			{
				$conf= simplexml_load_file($dir);
				$ar					= Array();
				$ar['version']		= (integer)$conf->version['value'];
				$ar['subVersion']	= (integer)$conf->subVersion['value'];
				$ar['update']		= (integer)$conf->update['value'];
			}
			$ar['date']= $conf->date['value'];
			return $ar;
		}
		
		/**
		* Save any changes made to the options
		* @author Felipe Nascimento
		* @name saveOptions
		* @param Array $ar
		* @return void
		*/
		public function saveOptions($ar)
		{
			GLOBAL $_MIND;
			$opXML= $_MIND['rootDir'].'config/options.xml';
			$xml= $_MIND['fw']->mkXML($opXML);
			
			$xml->addChild('defaultIdiom');
			$xml->defaultIdiom['value']= $ar['defaultIdiom'];
			$xml->addChild('defaultDBMS');
			$xml->defaultDBMS['value']= $ar['defaultDBMS'];
			$xml->addChild('lookForUpdate');
			$xml->lookForUpdate['value']= $ar['lookForUpdate'];
			$xml->addChild('actionWithNewVersion');
			if(isset($ar['actionWithNewVersion']))
				$xml->actionWithNewVersion['value']= $ar['actionWithNewVersion'];
			else
				$xml->actionWithNewVersion['value']= '-1';
			$xml->addChild('actionWithNewSubVersion');
			if(isset($ar['actionWithNewSubVersion']))
				$xml->actionWithNewSubVersion['value']= $ar['actionWithNewSubVersion'];
			else
				$xml->actionWithNewSubVersion['value']= '-1';
			$xml->addChild('actionWithNewUpdates');
			if(isset($ar['actionWithNewUpdates']))
				$xml->actionWithNewUpdates['value']= $ar['actionWithNewUpdates'];
			else
				$xml->actionWithNewUpdates['value']= '-1';
			
			// for I.Q. Options
			if(isset($ar['useGlobalSynDic']))
				$xml->addChild('useGlobalSynDic', $ar['useGlobalSynDic']);
			if(isset($ar['useLocalSynDic']))
				$xml->addChild('useLocalSynDic', $ar['useLocalSynDic']);
			if(isset($ar['addAutomatically']))
				$xml->addChild('addAutomatically', $ar['addAutomatically']);
			if(isset($ar['askForVerbs']))
				$xml->addChild('askForVerbs', $ar['askForVerbs']);
			if(isset($ar['askForTypes']))
				$xml->addChild('askForTypes', $ar['askForTypes']);
			if(isset($ar['reportDecisions']))
				$xml->addChild('reportDecisions', $ar['reportDecisions']);
			if(isset($ar['reportDoubts']))
				$xml->addChild('reportDoubts', $ar['reportDoubts']);
			if(isset($ar['enableMindUniverse']))
				$xml->addChild('enableMindUniverse', $ar['enableMindUniverse']);
			
			$_MIND['fw']->saveXML($xml, $opXML);
			
		}
		
		/**
		* Loads the currently set options 
		* @author Felipe Nascimento
		* @name loadOptions
		* @return Assoc Array
		*/
		public function loadOptions()
		{
			GLOBAL $_MIND;
			$op= $this->loadXML($_MIND['rootDir'].'config/options.xml');
			$opts							= Array();
			$opts['defaultIdiom']			= (string)$op->defaultIdiom['value'];
			$opts['defaultDBMS']			= (string)$op->defaultDBMS['value'];
			$opts['lookForUpdate']			= (string)$op->lookForUpdate['value'];
			$opts['actionWithNewVersion']	= (string)$op->actionWithNewVersion['value'];
			$opts['actionWithNewSubVersion']= (string)$op->actionWithNewSubVersion['value'];
			$opts['actionWithNewUpdates']	= (string)$op->actionWithNewUpdates['value'];
			$opts['useGlobalSynDic']		= (string)$op->useGlobalSynDic;
			$opts['useLocalSynDic']			= (string)$op->useLocalSynDic;
			$opts['addAutomatically']		= (string)$op->addAutomatically;
			$opts['askForVerbs']			= (string)$op->askForVerbs;
			$opts['askForTypes']			= (string)$op->askForTypes;
			$opts['reportDecisions']		= (string)$op->reportDecisions;
			$opts['reportDoubts']			= (string)$op->reportDoubts;
			$opts['enableMindUniverse']		= (string)$op->enableMindUniverse;
			
			return $opts;
		}
		
		/**
		* Lists all the currently registered themes
		* @author Felipe Nascimento
		* @name getThemes
		* @return Array
		*/
		public function getThemes()
		{
			$c=0;
			GLOBAL $_MIND;
			$d = dir($_MIND['rootDir'].$_MIND['themeSrc']);
			$ar= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0, 1) != '.' && is_dir($_MIND['rootDir'].$_MIND['themeSrc'].'/'.$entry))
				{
					$ar[]= $entry;
				}
				$c++;
			}
			$d->close();
			return $ar;
		}
	}
	
	/**
	* Function created to deal with possible errors
	* @author Felipe Nascimento
	* @name errorHandler
	* @param Integer $errno
	* @param String $errstr
	* @param String $errfile
	* @param Integer $errline
	* @return void
	*/
	function errorHandler($errno=499, $errstr="Internal Mind Error", $errfile=false, $errline=0)
	{
		switch($errno)
		{
			case E_USER_NOTICE:
			{
				echo 'Notice: '. $errstr.' - '.$errfile.' <br/>'.$errline.'<br/>-----------------------------------------<br/>';
				break;
			}
			case E_USER_ERROR:
			{
				@header("HTTP/1.0 ".$errno." Mind Error");
				echo $errstr;
				if($errfile)
					echo "<br/>File: " .$errfile." <br/>Line: ". $errline.'<br/>-----------------------------------------<br/>';
				exit;
			}
			case E_USER_WARNING:
			default:
			{
				@header("HTTP/1.0 ".$errno." Mind Warning");
				echo $errstr;
				if($errfile)
					echo "<br/>File: " .$errfile." <br/>Line: ". $errline.'<br/>-----------------------------------------<br/>';
				break;
			}
		}
	}
	
	// instantiates the Framework itself and loas any extra required data 
	$_MIND['fw']= new Mind();
	$_MIND['fw']->loadExternal();
	
	// specifies who is the error handler for this application
	set_error_handler('errorHandler');
?>
