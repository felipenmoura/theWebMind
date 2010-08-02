<?php
	/**
	 * Class related to Plugin Objects
	 * @author Felipe Nascimento
	 * @name Plugin
	 * @package framework
	 */
	class Plugin
	{
		public $name= '';
		public $date= '';
		public $authors= Array();
		public $link= '';
		public $description= '';
		public $conf= '';
		public $dir= '';
		public $disabled = false;
		
		public function code($c = false)
		{
			if($c)
				$this->name= $c;
			else
				return $this->name;
		}
		
		function conf($confName, $confValue=false)
		{
			if($confValue)
			{
				$this->conf[$confName]= $confValue;
			}else{
					return $this->conf[$confName];
				 }
		}
		function name($n=false)
		{
			if($n)
				$this->name= $n;
			else
				return $this->name;
		}
		function date($n=false)
		{
			if($n)
				$this->date= $n;
			else
				return $this->date;
		}
		function authors($n=false)
		{
			if($n)
				$this->authors[]= $n;
			else
				return $this->authors;
		}
		function link($n=false)
		{
			if($n)
				$this->link= $n;
			else
				return $this->link;
		}
		function description($n=false)
		{
			if($n)
				$this->description= $n;
			else
				return $this->description;
		}
		function __construct($pluginName= false)
		{
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'];
			$this->dir= $_MIND['pluginDir'];
			if($pluginName)
				$this->load($pluginName);
		}
		
		/**
		 * Loads the information of one plugin
		 * @name getPlugin
		 * @param $cod the name of the plugins, itself
		 * @return Plugin Object
		 */
		static function getPlugin($cod)
		{
			GLOBAL $_MIND;
			$d = dir($_MIND['rootDir'].$_MIND['pluginDir']);
			while (false !== ($entry = $d->read()))
			{
				if($entry == $cod)
				{
					$pl= new Plugin($entry);
					break;
				}
			}
			$d->close();
			if(isset($pl))
				return $pl;
			else
				return false;
		}
		
		/**
		 * Loads the list of plugins
		 * @name getPlugins
		 * @author Felipe Nacimento
		 * @return Numeric Array of Plugin objects
		 */
		static function getPlugins()
		{
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'];
			$d = dir($dir.$_MIND['pluginDir']);
			$ret= Array();
			$list= Array();
			while (false !== ($entry = $d->read()))
			{
				if($entry != "." && $entry != ".." && $entry != ".svn" && is_dir($d->path)
					&& !isset($list[$entry]))
				{
					$ret[]= new Plugin($entry);
					$ret[sizeof($ret)-1]->code($ret[sizeof($ret)-1]->name());
					$list[$entry]= true;
				}
			}
			return $ret;
		}
		
		/**
		 * Populates the Plugin Object
		 * @param $ar with the data to be used
		 * @author Felipe Nascimento
		 */
		function populate($ar)
		{
			$this->code($ar['name']);
			$this->name($ar['name']);
			$this->date($ar['date']);
			$this->author($ar['author']);
			$this->link($ar['link']);
			$this->description($ar['description']);
			$this->conf['openAs']= $ar['openAs'];
			$this->conf['runAt']= $ar['runAt'];
			$this->conf['openEvent']= $ar['openEvent'];
			$this->conf['status']= $ar['status'];
			$this->conf['useIcon']= $ar['useIcon'];
		}
		
		/**
		 * Loads a specific plugin data (conf and info)
		 * @param $plugin Object
		 * @author Felipe Nascimento
		 */
		function load($plugin)
		{
			$flagActive = false;
			GLOBAL $_MIND;
			if(!file_exists($_MIND['rootDir'].$_MIND['pluginDir'].'/'.$plugin)){
				$plugin = ".".$plugin;
				$flagActive = true;
			}
			
			if(file_exists($_MIND['rootDir'].$_MIND['pluginDir'].'/'.$plugin))
			{
				$conf= simplexml_load_file($_MIND['rootDir'].$_MIND['pluginDir'].'/'.$plugin.'/conf.xml');
				$info= simplexml_load_file($_MIND['rootDir'].$_MIND['pluginDir'].'/'.$plugin.'/info.xml');
				
				$this->name= (string)$info->name['value'];
				$this->date= (string)$info->date['value'];
				foreach($info->authors->author as $aut)
					$this->authors[]= (string)$aut['value'];
				$this->link= (string)$info->link['value'];
				$this->description= (string)$info->description;
				
				$this->conf['openAs']= (string)$conf->openas['value'];
				$this->conf['runAt']= (string)$conf->runAt['value'];
				$this->conf['extraConfFile']= (string)$conf->extraconffile['value'];
				$this->conf['version']= (string)$conf->version['value'];
				$this->conf['useIcon']= (string)$conf->useicon['value'];
				$this->conf['dependsOnProject']= (string)$conf->dependsonproject['value'];
				if($flagActive){
					$this->disabled = true;
				}else{
					$this->disabled = false;
				}
			}
		}
		
		/**
		 * Saves the current situation of the instance
		 * @param [$obj] Optionaly, you can send other object to save
		 */
		function save($obj=false)
		{
			$obj= ($obj)? $obj: $this;
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'].$_MIND['pluginDir'].'/'.$obj->name();
			if(!file_exists($dir))
			{
				mkdir($dir);
			}
			$confF= $dir.'/conf.xml';
			$infoF= $dir.'/info.xml';
			$conf= $_MIND['fw']->mkXML($confF);
			$info= $_MIND['fw']->mkXML($infoF);
			$info->addChild('name');
			$info->name['value']= $this->name();
			$info->addChild('date');
			$info->date['value']= $this->date();
			$info->addChild('author');
			$info->author['value']= $this->author();
			$info->addChild('link');
			$info->link['value']= $this->link();
			
			$conf->addChild('openAs');
			$conf->openAs['value']= $this->conf['openAs'];
			$conf->addChild('runAt');
			$conf->runAt['value']= $this->conf['runAt'];
			$conf->addChild('openEvent');
			$conf->openEvent['value']= $this->conf['openEvent'];
			$conf->addChild('openEvent');
			$conf->openEvent['value']= $this->conf['openEvent'];
			$conf->addChild('useicon');
			$conf->useicon['value']= $this->conf['useIcon'];
			
			$_MIND['fw']->saveXML($conf, $confF);
			$_MIND['fw']->saveXML($info, $infoF);
		}
		
		/**
		* Disable a Plugin (Just add dot(".") in the begin of folder)
		* @name disable
		* @author Felipe Nascimento
		* @return void 
		*/
		function disable(){
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'].$_MIND['pluginDir'].'/';
			rename($dir.$this->code(), $dir.".".$this->code());
		}
		
		/**
		* Remove a Plugin (Delete the folder)
		* @name remove
		* @author Felipe Nascimento
		* @return boolean
		*/
		function remove(){
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'].$_MIND['pluginDir'].'/';
			if(!file_exists($dir))
				$dir= '.'.$dir;
			if(!file_exists($dir))
				return false;
			$_MIND['fw']->deleteDirectory($dir.$this->code());
			return true;
		}
		
		/**
		* Enable a Plugin (Just remove the dot(".") in the begin of folder)
		* @name enable
		* @author Felipe Nascimento
		* @return void
		*/
		function enable(){
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'].$_MIND['pluginDir'].'/';
			rename($dir.".".$this->code(), $dir.$this->code());
		}
	}