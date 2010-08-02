<?php
	class Modulo2 extends Module implements module_interface
	{
		private $project;
		private $knowledge;
		private $structure;
		
		
		/**
		*Create the header of the file class
		*@param String 	$header - The content that the header must have
		*@param String  $extra - An opcional text
		*@return String
		*/
		public function headerFile($header,$extra=null){
			return $header = $extra != null ? $header." ".$extra."\n" : $header."\n";
		}
		
		/**
		*Create the footer of the file class
		*@param String 	$footer - The content that the footer must have
		*@param String  $extra - An opcional text
		*@return String
		*/
		public function footerFile($footer,$extra=null){
			return $footer = $extra != null ? $footer." ".$extra."\n" : $footer;
		}
		
		/**
		*Create the Attributes for a Class
		*@param String 	$className - The name of a Class
		*@param Array 	$attributes - All attributes of a Class
		*@return String
		*/
		public function createAttributes($className,$attributes){
			$className = ucfirst($className);
			reset($attributes);
			$attrs = "";
			while($cur = current($attributes)){
				$attrs.= $this->createComment(array("@var ".$cur->type),1);
				$attrs.= $this->setTabText(1)."private \$".$cur->name.";\n\n";
				next($attributes);
			}
			return $attrs;
		}
		
		/**
		*Create the Methods for a Class
		*@param String 	$className - The name of a Class
		*@param Array 	$attributes - All attributes of a Class
		*@return String
		*/
		public function createMethods($className,$attributes){
			$className = ucfirst($className);
			$meths = $this->createComment(array("Constructor for a ".$className),1);
			$meths.= $this->setTabText(1)."public function ".$className."(){\n";
			$meths.= $this->setTabText(1)."}\n";
			
			reset($attributes);
			while($cur = current($attributes)){
				$meths.= $this->createComment(array("Set or Get the attribute \$".$cur->name),1);
				$meths.= $this->setTabText(1)."public function ".$cur->name."(\$".$cur->name."=null){\n";
				$meths.= $this->setTabText(2)."if(\$".$cur->name." != null){\n".$this->setTabText(4)."\$this->".$cur->name." = \$".$cur->name.";\n".$this->setTabText(2)."}else{\n".$this->setTabText(3)."return \$this->".$cur->name.";\n".$this->setTabText(2)."}\n	}\n\n";
				next($attributes);
			}
			return $meths;
		}
		
		/**
		*Create a Class
		*@param String 	$visibility - The visibility of a Class
		*@param Table 	$table - Mind Table Object, that contais all knowledge
		*@return String
		*/
		public function createClass($visibility=null,$table){
			$name = ucfirst($table->name);
			$class = $this->createComment(array("Generate by TheWebMind 2.0 Software","@license	http://www.gnu.org/licenses/gpl.html","@link http://thewebmind.org","@author".$this->setTabText(1)."Jaydson Gomes <jaydson@thewebmind.org>","@author".$this->setTabText(1)."Felipe N. Moura <felipe@thewebmind.org>","Base class for ".$name, "@version  1.0"));
			$class.= $visibility != null ? $visibility."\n " : "\n";
			$class.= "class ".$name."{\n";
			$class.= $this->createAttributes($table->name,$table->attributes)."\n";
			$class.= $this->createMethods($table->name,$table->attributes);
			$class.="}\n";
			
			return $class;
		}
		
		/**
		*Create a DAO Class
		*@param Table 	$table - Mind Table Object, that contais all knowledge
		*@return String
		*/
		public function createDAOClass($table){
			//HEADER
			$name = ucfirst($table->name);
			$attributes = $table->attributes;
			$dao_class = $this->createComment(array("Generate by TheWebMind 2.0 Software","@license	http://www.gnu.org/licenses/gpl.html","@link http://thewebmind.org","@author".$this->setTabText(1)."Jaydson Gomes <jaydson@thewebmind.org>","@author".$this->setTabText(1)."Felipe N. Moura <felipe@thewebmind.org>","DAO class for ".$name, "@version  1.0"));
			$dao_class.= "\nrequire('PDOConnectionFactory.php');\n";
			$dao_class.= "\nclass ".$name."DAO extends PDOConnectionFactory{\n";
			$dao_class.= $this->createComment(array($this->setTabText(1)."@var Connection"),1);
			$dao_class.= $this->setTabText(1)."public \$conex = null;\n\n";
			$dao_class.= $this->createComment(array("Constructor"),1);
			$dao_class.= $this->setTabText(1)."public function ".$name."DAO(){\n";
			$dao_class.= $this->setTabText(2)."\$this->conex = PDOConnectionFactory::getConnection();\n".$this->setTabText(1)."}\n\n";
			$dao_class.= $this->createComment(array("Insert an Object ".$name,"@param ".$name." \$".$table->name),1);
			
			//INSERT
			$dao_class.= $this->setTabText(1)."public function Insert(\$".$table->name."){\n";
			$dao_class.= $this->setTabText(2)."try{\n";
			$dao_class.= $this->setTabText(3)."\$stmt = \$this->conex->prepare('".$this->getQueryScript("INSERT",$table)."');\n";
			$dao_class.= $this->bindValues("INSERT",$attributes,$table->name);
			$dao_class.= $this->setTabText(3)."\$stmt->execute();".$this->setTabText(3)."\n";
			$dao_class.= $this->setTabText(2)."}catch (PDOException \$ex){  echo 'Error: '.\$ex->getMessage(); }\n".$this->setTabText(1)."}\n\n";
			
			//UPDATE
			$dao_class.= $this->createComment(array("Update an Object ".$name,"@param ".$name." \$".$table->name),1);
			$dao_class.= $this->setTabText(1)."public function Update(\$".$table->name."){\n";
			$dao_class.= $this->setTabText(2)."try{\n";
			$dao_class.= $this->setTabText(3)."\$stmt = \$this->conex->prepare('".$this->getQueryScript("UPDATE",$table)."');\n";
			$dao_class.= $this->setTabText(3)."\$this->conex->beginTransaction();\n";
			$dao_class.= $this->bindValues("UPDATE",$attributes,$table->name);
			$dao_class.= $this->setTabText(3)."\$stmt->execute();\n ".$this->setTabText(3)."\$this->conex->commit();".$this->setTabText(3)."\n";
			$dao_class.= $this->setTabText(2)."}catch (PDOException \$ex){  echo 'Error: '.\$ex->getMessage(); }\n".$this->setTabText(1)."}\n\n";
			
			//REMOVE
			$dao_class.= $this->createComment(array("Remove an Object ".$name,"@param ".$name." \$".$table->name),1);
			$dao_class.= $this->setTabText(1)."public function Remove(\$".$table->name."){\n";
			$dao_class.= $this->setTabText(2)."try{\n";
			$dao_class.= $this->setTabText(3)."\$stmt = \$this->conex->prepare('".$this->getQueryScript("REMOVE",$table)."');\n";
			$dao_class.= $this->setTabText(3)."\$this->conex->beginTransaction();\n";
			$dao_class.= $this->bindValues("REMOVE",$attributes["pk_".$table->name],$table->name);
			$dao_class.= $this->setTabText(3)."\$stmt->execute();\n ".$this->setTabText(3)."\$this->conex->commit();".$this->setTabText(3)."\n";
			$dao_class.= $this->setTabText(2)."}catch (PDOException \$ex){  echo 'Error: '.\$ex->getMessage(); }\n".$this->setTabText(1)."}\n\n";
			
			//Collection
			$dao_class.= $this->createComment(array("List an Collection ".$name,"@param String \$query"),1);
			$dao_class.= $this->setTabText(1)."public function GetCollection(\$query=null){\n";
			$dao_class.= $this->setTabText(2)."try{\n";
			$dao_class.= $this->setTabText(3)."if(\$query == null){\n";
			$dao_class.= $this->setTabText(4)."\$stmt = \$this->conex->query('SELECT * FROM ".$table->name."');\n";
			$dao_class.= $this->setTabText(3)."}else{\n";
			$dao_class.= $this->setTabText(4)."\$stmt = \$this->conex->query(\$query);\n";
			$dao_class.= $this->setTabText(3)."}";
			$dao_class.= $this->setTabText(3)."\n ".$this->setTabText(3)."return \$stmt;\n";
			$dao_class.= $this->setTabText(2)."}catch (PDOException \$ex){  echo 'Error: '.\$ex->getMessage(); }\n".$this->setTabText(1)."}\n\n";
			
			//BY ID
			$dao_class.= $this->createComment(array("Get an Object ".$name." by id ","@param int \$id"),1);
			$dao_class.= $this->setTabText(1)."public function GetById(\$id){\n";
			$dao_class.= $this->setTabText(2)."try{\n";
			$dao_class.= $this->setTabText(3)."\$stmt = \$this->conex->query('SELECT * FROM ".$table->name." where pk_".$table->name."='.\$id);\n";
			$dao_class.= $this->setTabText(3)."return \$stmt;\n";
			$dao_class.= $this->setTabText(2)."}catch (PDOException \$ex){  echo 'Error: '.\$ex->getMessage(); }\n".$this->setTabText(1)."}\n\n";
			$dao_class.= "}\n";
			return $dao_class;
		}
		
		/**
		*Create a Service Class
		*@param Table 	$table - Mind Table Object, that contais all knowledge
		*@return String
		*/		
		public function createService($table){
			//HEADER
			$name = ucfirst($table->name);
			$service = $this->createComment(array("Generate by TheWebMind 2.0 Software","@license	http://www.gnu.org/licenses/gpl.html","@link http://thewebmind.org","@author".$this->setTabText(1)."Jaydson Gomes <jaydson@thewebmind.org>","@author".$this->setTabText(1)."Felipe N. Moura <felipe@thewebmind.org>","Service class for ".$name, "@version  1.0"));
			$service.= "require_once('BaseService.php');\n\n";
			$service.= "class ".$name."Service extends BaseService {\n\n";
			
			//Constructor
			$service.= $this->createComment(array("Constructor"),1);
			$service.= $this->setTabText(1)."public function ".$name."Service(){\n";
			$service.= $this->setTabText(2)."parent::BaseService('".$name."');\n";
			$service.= $this->setTabText(1)."}\n\n";
			
			//SAVE
			$service.= $this->createComment(array("Save or Insert an object ".$name,"If the object has a code, then Update.","Else, if is a new Object, then Insert","@param \$".$table->name),1);
			$service.= $this->setTabText(1)."public function Save(\$".$table->name."){\n";
			$service.= $this->setTabText(2)."if(\$".$table->name."->pk_".$table->name."()){\n";
			$service.= $this->setTabText(3)."return \$this->DAO()->Update(\$".$table->name.");\n";
			$service.= $this->setTabText(2)."}else{\n";
			$service.= $this->setTabText(3)."return \$this->DAO()->Insert(\$".$table->name.");\n";
			$service.= $this->setTabText(2)."}\n";
			$service.= $this->setTabText(1)."}\n\n";
			
			//REMOVE
			$service.= $this->createComment(array("Remove an object ".$name,"@param \$".$table->name),1);
			$service.= $this->setTabText(1)."public function Remove(\$".$table->name."){\n";
			$service.= $this->setTabText(2)."return \$this->DAO()->Remove(\$".$table->name.");\n";
			$service.= $this->setTabText(1)."}\n\n";
						
			//BY ID
			$service.= $this->createComment(array("Get an object ".$name." by id","@param int \$id","@return ".$name.""),1);
			$service.= $this->setTabText(1)."public function GetById(\$id){\n";
			$service.= $this->setTabText(2)."foreach (\$this->DAO()->GetById(\$id) as \$arr){\n";
			$service.= $this->setTabText(3)."\$".$table->name." = \$this->Load(\$arr,'".$name."');\n";
			$service.= $this->setTabText(2)."}\n";
			$service.= $this->setTabText(2)."return \$".$table->name.";\n";
			$service.= $this->setTabText(1)."}\n\n";
			
			//Collection
			$service.= $this->createComment(array("Get an ".$name." Collection","@return ".$name.""),1);
			$service.= $this->setTabText(1)."public function GetCollection(){\n";
			$service.= $this->setTabText(2)."\$collection = Array();\n";
			$service.= $this->setTabText(2)."foreach (\$this->DAO()->GetCollection() as \$arr){\n";
			$service.= $this->setTabText(3)."\$collection[]= \$this->Load(\$arr,'".$name."');\n";
			$service.=$this->setTabText(2)."}\n";
			$service.=$this->setTabText(2)."return \$collection;\n";
			$service.=$this->setTabText(1)."}\n";
			$service.=$this->setTabText(0)."}\n";
			
			return $service;
		}
		
		/**
		*Bind values for a script Query
		*@param  Table->atttributes $attributes - Mind Table->attributes Object
		*@param  String $name Table Name
		*@return String
		*/
		public function bindValues($command=null,$attributes,$tableName){
			$values = "";
			reset($attributes);
			$index=0;
			while($cur = current($attributes)){			
				$k = key($attributes);				
				if(is_object($cur)){
					if(!$cur->pk){
						$index++;
						$values.= $this->setTabText(3)."\$stmt->bindValue(".$index.", \$".$tableName."->".$k."());\n";
					}
				};
				next($attributes);
			}
			if($command=="UPDATE"){
				$index++;
				$values.= $this->setTabText(3)."\$stmt->bindValue(".$index.", \$".$tableName."->pk_".$tableName."());\n";
			}
			if($command=="REMOVE"){
				$index++;
				$values.= $this->setTabText(3)."\$stmt->bindValue(".$index.", \$".$tableName."->pk_".$tableName."());\n";
			}
			return $values;
		}
		
		/**
		*Create a Class comment
		*@param Array 	$commentLines - An array with n lines that you want to comment
		*@param int 		$tabs A number that define how much tabs have in the comment
		*@return String
		*/
		public function createComment($commentLines,$tabs=null){
			$tabs = $tabs != null ? $tabs : 0;
			$tab=$this->setTabText($tabs);
			$c = $tab."/**\n";
			for($i=0;$i<sizeof($commentLines);$i++){
				$c.= $i==sizeof($commentLines) ? $tab."* ".$commentLines[$i] : $tab."* ".$commentLines[$i]."\n";
			}
			$c.= $tab."*/\n";
			return $c;
		}
		
		/**
		*Generate the Entities Classes
		*@param Table 	$tableObject - Mind Table Object, that contais all knowledge
		*@param String  $lang - The language of the entities classes
		*@return String
		*/
		public function generateEntities($tableObject,$lang){
			switch($lang){
				case "PHP":
					$content = $this->headerFile("<?php");
					$content.= $this->createClass(null,$tableObject);
					$content.= $this->footerFile("?>");
				break;
			}
			return $content;
		}
		
		/**
		*Generate the DAO Classes
		*@param Table 	$tableObject - Mind Table Object, that contais all knowledge
		*@param String  $lang - The language of the entities classes
		*@return String
		*/
		public function generateDAO($tableObject,$lang){
			switch($lang){
				case "PHP":
					$content = $this->headerFile("<?php");
					$content.= $this->createDAOClass($tableObject);
					$content.= $this->footerFile("?>");
				break;
			}
			return $content;
		}
		
		/**
		*Generate the Service Classes
		*@param Table 	$tableObject - Mind Table Object, that contais all knowledge
		*@param String  $lang - The language of the entities classes
		*@return String
		*/
		public function generateService($tableObject,$lang){
			switch($lang){
				case "PHP":
					$content = $this->headerFile("<?php");
					$content.= $this->createService($tableObject);
					$content.= $this->footerFile("?>");
				break;
			}
			return $content;
		}
		
		public function getStructure()
		{
			return $this->structure;
		}
		
		/**
		*Method Called for each table
		*@param Table 	$tableObject - Mind Table Object, that contais all knowledge
		*@return void
		*/
		public function applyCRUD($tableObject){
			$this->fw->mkFile('entity/'.$tableObject->name.'.php', $this->generateEntities($tableObject,"PHP"));
			$this->fw->mkFile('DAO/'.$tableObject->name.'DAO.php', $this->generateDAO($tableObject,"PHP"));
			$this->fw->mkFile('service/'.$tableObject->name.'Service.php', $this->generateService($tableObject,"PHP"));
		}
		
		public function onStart(){
			$file = $this->fw->getContent("PDOConnectionFactory.php");
			$file = str_replace("<dbType>",$this->project->dbms,$file);
			$file = str_replace("<host>",$this->project->environment["development"]["dbAddress"],$file);
			$file = str_replace("<user>",$this->project->environment["development"]["user"],$file);
			$file = str_replace("<pass>",$this->project->environment["development"]["userPwd"],$file);
			$file = str_replace("<db>",$this->project->environment["development"]["dbName"],$file);
			$file = str_replace("<port>",$this->project->environment["development"]["dbPort"],$file);
			$this->fw->mkFile('DAO/PDOConnectionFactory.php',$file);
			
			$fileService = $this->fw->getContent("BaseService.php");
			$this->fw->mkFile('service/BaseService.php',$fileService);
		}
		public function onFinish(){
			//echo "YYYYYY";
		}		
		public function callExtra(){
		}
		
		public function __construct($project)
		{
			GLOBAL $_FW;
			$this->fw= $_FW;
			$this->structure= 'structure'; // tells Mind what is the structure directory to be based on
			$this->project = $project;
			$this->knowledge= $project->knowledge;
		}
	}
?>