<?php
	class Plugin
	{
		public $code= '';
		public $name= '';
		public $date= '';
		public $author= '';
		public $link= '';
		public $detail= '';
		public $conf= Array();
		
		function conf($confName, $confValue=false)
		{
			if($confValue)
			{
				$this->conf[$confName]= $confValue;
			}else{
					return $this->conf[$confName];
				 }
		}
		function code($n)
		{
			if($n)
				$this->code= $n;
			else
				return $this->name;
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
		function author($n=false)
		{
			if($n)
				$this->author= $n;
			else
				return $this->author;
		}
		function link($n=false)
		{
			if($n)
				$this->link= $n;
			else
				return $this->link;
		}
		function detail($n=false)
		{
			if($n)
				$this->detail= $n;
			else
				return $this->detail;
		}
		function __construct($pluginName= false)
		{
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'];
			if($pluginName)
				if(file_exists($dir.$_MIND['pluginDir'].'/'.$pluginName))
				{
					$conf= simplexml_load_file($dir.$_MIND['pluginDir'].'/'.$pluginName.'/conf.xml');
					$info= simplexml_load_file($dir.$_MIND['pluginDir'].'/'.$pluginName.'/info.xml');
					$this->name= (string)$info->name['value'];
					$this->date= (string)$info->date['value'];
					$this->author= (string)$info->author['value'];
					$this->link= (string)$info->link['value'];
					$this->detail= (string)$info->detail['value'];
					
					
					$this->conf['openAs']= (string)$conf->name['openAs'];
					$this->conf['openEvent']= (string)$conf->name['openEvent'];
					$this->conf['status']= (string)$conf->name['status'];
					$this->name= (string)$info->name['value'];
					$this->date= (string)$info->date['value'];
					$this->author= (string)$info->author['value'];
				}else{
						if($pluginName)
							$this->name= $pluginName;
					 }
		}
		function getPlugins()
		{
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'];
			$d = dir($dir.$_MIND['pluginDir']);
			$ret= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0, 1) != '.' && is_dir($d->path))
				{
					$ret[]= new Plugin($entry);
					$ret[sizeof($ret)-1]->code($ret[sizeof($ret)-1]->name());
				}
			}
			$d->close();
			return $ret;
		}
		function populate($ar)
		{
			$this->code($ar['name']);
			$this->name($ar['name']);
			$this->date($ar['date']);
			$this->author($ar['author']);
			$this->link($ar['link']);
			$this->detail($ar['detail']);
			$this->conf['openAs']= $ar['openAs'];
			$this->conf['openEvent']= $ar['openEvent'];
			$this->conf['status']= $ar['status'];
		}
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
			$conf->addChild('openEvent');
			$conf->openEvent['value']= $this->conf['openEvent'];
			$conf->addChild('openEvent');
			$conf->openEvent['value']= $this->conf['openEvent'];
			
			/*reset($this->conf);
			while($cur= current($this->conf))
			{
				$conf->addChild(key($this->conf));
				//$conf->{key($this->conf)}['value']= $this->conf[key($this->conf)];
				next($this->conf);
			}*/
			$_MIND['fw']->saveXML($conf, $confF);
			$_MIND['fw']->saveXML($info, $infoF);
		}
	}
	
?>