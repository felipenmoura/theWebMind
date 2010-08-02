<?php
	/**
	* Class that contains the methods to manipulate Modules		
	*  @filesource
	 * @author			Jaydson Gomes
	 * @author 			Felipe Nascimento
	 * @copyright		Copyright <2009> TheWebMind.org
	 * @package			classes
	 * @subpackage		restrict.framework
	 * @version			1.0
	*/	
    	
	$_FW= null; //@GLOBAL Mind Framework
	
	class Module
	{
		public $name;
		public $version;
		public $fullName;
		public $language;
		public $details;
		public $authors;
		public $description;
		public $date;
		public $thumb;
		public $license;
		private $module;
		public $dependences; // scripts, styles and options the current module has/needs
		public $configPage;
		private $moduleData;  // the options or additional information added to the loaded module
		

		public function pDir($d){
			GLOBAL $_FW;
			$_FW->pDir($d);
		}
		public function mDir($d)
		{
			GLOBAL $_FW;
			$_FW->mDir($d);
		}
		
		protected function load($m)
		{
			GLOBAL $_MIND;
			if($this->moduleExists($m))
			{
				$m= $_MIND['rootDir'].$_MIND['moduleDir'].'/'.$m.'/';
				if(($confXML= @simplexml_load_file($m.'conf.xml')) && $infoXML= @simplexml_load_file($m.'info.xml'))
				{
					// creating the info array
					$ar= Array();
					$ar['name']= (string)$infoXML->name['value'];
					$ar['fullName']= (string)$infoXML->fullName['value'];
					$ar['language']= (string)$infoXML->language['value'];
					$ar['details']= Array();
					foreach($infoXML->details->detail as $d)
						array_push($ar['details'], Array('name'=>(string)$d['name'], 'value'=>(string)$d['value']));
					$ar['authors']= Array();
					foreach($infoXML->authors->author as $d)
						array_push($ar['authors'], Array('name'=>(string)$d['value'], 'email'=>(string)$d['email']));
					//$ar['details']= $infoXML->details['value'];
					
					$ar['version']= (string)$infoXML->version['value'];
					$ar['description']= (string)$infoXML->description['value'];
					$ar['date']= (string)$infoXML->date['value'];
					$ar['thumb']= (string)$infoXML->thumb['value'];
					$ar['license']= (string)$infoXML->license['value'];
					$ar['configPage']= (string)$confXML->config['src'];
					
					// creating the config array
					$ar['dependences']= Array();
					$ar['dependences']['scripts']= Array();
					$ar['dependences']['styles']= Array();
					
					foreach($confXML->scripts->js as $d)
						array_push($ar['dependences']['scripts'],
									(string)$d['src']);
					foreach($confXML->styles->css as $d)
						array_push($ar['dependences']['styles'],
									(string)$d['src']);
					
					$this->populate($ar);
					//$this->populate($_MIND['fw']->objectToArray($infoXML));
				}else{
						echo "Mind.Dialog.ShowError(".JSON_encode($_MIND['fw']->errorOutput(7)).")";
					 }
			}else{
					echo "Mind.Dialog.ShowError(".JSON_encode($_MIND['fw']->errorOutput(7)).")";
				 }
			return $this;
		}
		
		public function populate($ar)
		{
			GLOBAL $_MIND;
			$this->name			= $_MIND['fw']->getEncoded($ar['name']);
			$d= $_MIND['moduleDir'].'/'.$this->name.'/data/';
			$this->version 		= $ar['version'];
			$this->fullName		= $_MIND['fw']->filter($ar['fullName']);
			$this->language		= $_MIND['fw']->filter($ar['language']);
			$this->description	= $_MIND['fw']->filter($ar['description']);
			$this->date			= $ar['date'];
			$this->thumb		= $d.$_MIND['fw']->getEncoded($ar['thumb']);
			$this->license		= $d.$_MIND['fw']->getEncoded($ar['license']);
			$this->authors		= Array();
			for($i=0; $i<sizeof($ar['authors']); $i++)
			{
				$this->authors[$i]= $ar['authors'][$i];
			}	
			$this->details		= Array();
			for($i=0; $i<sizeof($ar['details']); $i++)
			{
				$this->details[$i]= Array('name'=>$ar['details'][$i]['name'], 'value'=>$ar['details'][$i]['value']);
			}
			$this->dependences	= Array();
			if(isset($ar['dependences']))
			{
				if(isset($ar['dependences']['scripts']))
					for($i=0; $i<sizeof($ar['dependences']['scripts']); $i++)
					{
						$this->dependences['scripts'][$i]= $d.$_MIND['fw']->getEncoded($ar['dependences']['scripts'][$i]);
					}
				if(isset($ar['dependences']['styles']))
					for($i=0; $i<sizeof($ar['dependences']['styles']); $i++)
					{
						$this->dependences['styles'][$i]= $d.$_MIND['fw']->getEncoded($ar['dependences']['styles'][$i]);
					}
			}
			$this->configPage= $ar['configPage'];
			return $this;
		}
		
		public function moduleExists($m)
		{
			GLOBAL $_MIND;
			return file_exists($_MIND['rootDir'].$_MIND['moduleDir'].'/'.$m.'/conf.xml');
		}
		
		public function loadModule($p)
		{
			GLOBAL $_MIND;
			$mDir= $_MIND['rootDir'].$_MIND['moduleDir'].'/';
			include($mDir.'module_interface.php');
			include($mDir.$this->name.'/'.$this->name.'.php');
			$this->module= new $this->name($p);
		}
		
		public function askForCRUD($tableObject)
		{
			$this->module->applyCRUD($tableObject);
		}
		
		public function structure($m, $p)
		{
			GLOBAL $_MIND;
			$x= $this->module->getStructure();

			if(trim($x)==''){
				return true;
			}
				
			$m= $_MIND['rootDir'].$m.$x;
			$p= $_MIND['rootDir'].$p;
			
			if(!$_MIND['fw']->copyDir($m, $p, true, true))
				return false;
		}
		
		public function callExtra()
		{
			$this->module->callExtra();
		}
		
		public function onFinish()
		{
			$this->module->onFinish();
		}
		
		public function onStart()
		{
			$this->module->onStart();
		}
		
		/**
		*Return the prefix name to Primary Key
		*@return String
		*/
		public function getPrefixNamePK(){
			GLOBAL $_MIND;
			return $_MIND['primaryKeyPrefix'];
		}
		
		/**
		*Return the prefix name to Foreign Key
		*@return String
		*/
		public function getPrefixNameFK(){
			GLOBAL $_MIND;
			return $_MIND['foreignKeyPrefix'];
		}
		
		/**
		*Set carriage tab in a text
		*@param int 	$tabs - Number of carriage tabs
		*@return String
		*/
		public function setTabText($tabs){
			$tab="";
			for($t=0;$t<$tabs;$t++){
				$tab.="	";
			}
			return $tab;
		}
		
		/**
		*Get the Query Script of an Table
		*@param String  $command - INSERT,DELETE,UPDATE,SELECT
		*@param Table 	$table - Mind Table Object, that contais all knowledge
		*@return String
		*/
		public function getQueryScript($command,$table){
			$query = "";
			$fields = "";
			$attributes = $table->attributes;			
			switch($command){
				case "INSERT" :
					$size=0;
					$count=0;
					$values="";
					reset($attributes);
					while($cur = current($attributes)){
						if(!$cur->pk)
							$size++;
						next($attributes);
					}
					reset($attributes);
					while($cur = current($attributes)){
						if(!$cur->pk){
							$count++;
							$values.= $count==$size ? "?" : "?,";
							$fields.= $count==$size ? $cur->name : $cur->name.",";
						}
						next($attributes);
					}
					$query = "INSERT INTO ". $table->name." (".$fields.") VALUES (".$values.")";
					return $query;
				break;
				
				case "UPDATE" :
					$size=0;
					$count=0;
					reset($attributes);
					while($cur = current($attributes)){
						if(!$cur->pk){
							$comma = $count==0 ? "" : ",";
							$fields.= $comma.$cur->name."=?";
							$count++;
						}
						next($attributes);
					}
					$query = "UPDATE ". $table->name." SET ".$fields." WHERE pk_".$table->name."=?";
					return $query;					
				break;
				
				case "REMOVE" :
					$query = "DELETE FROM ". $table->name." WHERE pk_".$table->name."=?";
					return $query;
				break;
			}
		}
		
		public function getContent($file){
			GLOBAL $_FW;
			return $_FW->getContent($file);
		}
		
		public function __construct($module= false)
		{
			GLOBAL $_MIND;
			GLOBAL $_FW;
			
			$this->authors		= Array();
			$this->details		= Array();
			$this->dependences	= Array();
			
			// includes the framework that will be used by the selected module
			include_once('fw.php');
			$_FW= new FW();
			if($module)
			{
				$this->load($module);
			}
		}
	}
?>