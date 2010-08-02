<?php
/**
* Class that generate a ZendModel project
*  @filesource
 * @author			Jaydson Gomes
 * @author			Felipe Nascimento
 * @copyright		Copyright <2009> TheWebMind.org
 * @package			ZendModels
 * @subpackage		restrict.modules
 * @version			0.1
*/
	class ZendModels extends Module implements module_interface
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
		* Get who references to the table
		*@param Object 	$tableObject - The Table
		*@return Array
		*/
		public function getReferences($tableObject){
			$Arrayreferenceds = $tableObject->refered;
			$referenceds = Array();						
			for($i=0;$i<sizeof($Arrayreferenceds);$i++){
				$referenceds[$i] = explode("|",$Arrayreferenceds[$i]);
				$referenceds[$i] = $referenceds[$i][1];
			}
			return $referenceds;
		}
		
		/**
		* Get foreign keys
		*@param Object 	$tableObject - The Table
		*@return Array
		*/
		public function getForeignKeys($tableObject){
			$foreignKeys = $tableObject->foreignKeys;
			$ret= Array();
			for($i=0;$i<sizeof($foreignKeys);$i++){
				$ret[$foreignKeys[$i][0]]= $foreignKeys[$i][1];
			}
			return $ret;
		}
		
		/**
		* Get primary keys
		*@param Object 	$tableObject - The Table
		*@return String
		*/
		public function getPrimaryKey($tableObject){
			$attr = $tableObject->attributes;
			reset($attr);
			return key($attr);
		}
		
		/**
		* Prepare the name of Class
		*@param String 	$name - The name of class
		*@return String
		*/
		public function prepareClassName($name){
			$tempName = str_replace('_',' ',$name);
			$tempName = ucwords($tempName);
			return str_replace(' ','',$tempName);
		}
		
		/**
		* Prepare the name of Label
		*@param String 	$name - The name of class
		*@return String
		*/
		public function prepareLabelName($name){
			return ucwords(str_replace("_"," ",$name));
		}
		
		/**
		* Prepare the folder name
		*@param String 	$name - The name of class
		*@return String
		*/
		public function prepareFolderName($name){
			//return strtolower(str_replace("_","-",$name));
			$tempName = str_replace('_',' ',$name);
			$tempName = ucwords($tempName);
			return str_replace(' ','',$tempName);
		}
		
				
		/**
		* Prepare name to show on select options
		*@param String 	$name - The name of table
		*@return String
		*/
		public function getOptionLabel($table){	
			$table = $this->knowledge->tables[$table];
			reset($table->attributes);
			$temp;
			$counter=0;
			while($cur = current($table->attributes)){
				if($counter<2){
					$temp = $cur->name;
				}
				switch($cur->type){
					case 'string':
					case 'char':
					case 'varchar':
					case 'text':
						return $cur->name;
					break;
				} 
				next($table->attributes);
				$counter++;
			}
			return $temp;
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
		* Create Models
		*@param Table 	$tableObject - Mind Table Object, that contais all knowledge
		*@return String
		*/
		public function models(&$tableObject){
			$name = $tableObject->name;
			$attributes = $tableObject->attributes;
			$referenceds = $this->getReferences($tableObject);
			
			//Header
			$content = $this->headerFile("<?php");						
			$content.= $this->createComment(array("Generate by TheWebMind 2.0 Software","Model Class for ".$this->prepareClassName($name),"@license	http://www.opensource.org/licenses/mit-license.php","@link http://thewebmind.org","@author".$this->setTabText(1)."Jaydson Gomes <jaydson@thewebmind.org>","@author".$this->setTabText(1)."Felipe N. Moura <felipe@thewebmind.org>", "@version  1.0"));
			
			//Class
			$content.= "class ".$this->prepareClassName($name)." extends Zend_Db_Table {\n";
			
			//Protecteds
			$content.= $this->setTabText(1)."protected \$_name = '".$name."';\n";
			if(sizeof($referenceds)>0){
				$content.= $this->setTabText(1)."protected \$_dependentTables = array(";
				for($i=0;$i<sizeof($referenceds);$i++){
					if($i>0){
						$content.= ", ";
						$content.= "\n".$this->setTabText(3)."                            ";
					}
					$content.="'".$this->prepareClassName($referenceds[$i])."'";
				}
				$content.= ");\n";
			}
			
			//Reference Map
			if(sizeof($tableObject->foreignKeys) > 0)
			{
				$content.="
	protected \$_referenceMap = array(";
				$fks= $this->getForeignKeys($tableObject);
				reset($fks);
				$i= 0;
				while($cur= current($fks))
				{
					$tmpPk= $this->getPrimaryKey($this->knowledge->tables[$cur]);
					if($i>0)
					{
						$content.=",
									  '".$this->prepareClassName($cur)."'";
					}else{
							$content.= "'".$this->prepareClassName($cur)."'";
						 }
					$content.=" => Array(";
					$content.="'columns'   => Array('".key($fks)."'),
													    ";
					$content.="'refTableClass'   => '".$this->prepareClassName($cur)."',
													    ";
					$content.="'refColumns'   => Array('".$tmpPk."')";
					$content.="
														)";
					next($fks);
					$i++;
				}
				$content.="
										);\n";
			}
			// Save
			$content.= $this->createComment(array("Save an object ".$this->prepareClassName($name)." into Database","@return void"),1);
			$content.= $this->setTabText(1)."public function save(){\n";
			$content.= $this->setTabText(2)."\$post = Zend_Registry::get('post');\n";
			reset($attributes);
			next($attributes);
			while($cur = current($attributes)){
				if($cur->required)
				{
					$content.= $this->setTabText(2)."if(!isset(\$post->".$cur->name.") || str_replace('/ /', '', \$post->".$cur->name.") == '')
		{
			die('Required field: ".(isset($cur->comment) ? $cur->comment : $this->prepareLabelName($cur->name))."');
		}\n";
				}
				next($attributes);
			}
			reset($attributes);
			$pk = key($attributes);
			$content.= $this->setTabText(2)."if(isset(\$post->".$pk.")){\n";
			$content.= $this->setTabText(3)."\$where = \$this->getAdapter()->quoteInto('".$pk." = ?', \$post->".$pk.");\n";
			$content.= $this->setTabText(3)."\$data = array(";
			$counter=0;
			
			//Ignoring primary key
			next($attributes);
			while($cur = current($attributes)){
				if($counter>0){
					$content.= ",\n".$this->setTabText(6)."  ";
				}
				$content.= "'".$cur->name."' => \$post->".$cur->name;
				next($attributes);
				$counter++;
			}
			
			// Foreign Keys
			$fks = $this->getForeignKeys($tableObject);
			while($cur = current($fks)){
				if($counter>0){
					$content.= ",\n".$this->setTabText(6)."  ";
				}
				$pk = reset($this->knowledge->tables[$cur]->attributes);
				$content.= "'".key($fks)."' => \$post->".$pk->name;
				next($fks);
				$counter++;
			}
			
			$content.= ");\n";
			$content.= $this->setTabText(3)."\$this->update(\$data,\$where);\n";
			$content.= $this->setTabText(2)."}else{\n";
			reset($attributes);
			$content.= $this->setTabText(4)."\$data = array(";
			$counter=0;
			//Ignoring primary key
			next($attributes);
			while($cur = current($attributes)){
				if($counter>0){
					$content.= ",\n".$this->setTabText(7)."  ";
				}
				$content.= "'".$cur->name."' => \$post->".$cur->name;
				next($attributes);
				$counter++;
			}
			
			// Foreign Keys
			$fks = $this->getForeignKeys($tableObject);
			while($cur = current($fks)){
				if($counter>0){
					$content.= ",\n".$this->setTabText(7)."  ";
				}
				$pk = reset($this->knowledge->tables[$cur]->attributes);
				$content.= "'".key($fks)."' => \$post->".$pk->name;
				next($fks);
				$counter++;
			}
			$content.= ");\n";
			$content.= $this->setTabText(4)."\$this->insert(\$data);\n";
			$content.= $this->setTabText(3)."};\n";
			$content.= $this->setTabText(1)."}\n\n";
			
			// getCollection
			$content.= $this->createComment(array("List of objects ".$this->prepareClassName($name)." from Database","@return Collection of ".$this->prepareClassName($name)),1);
			$content.= $this->setTabText(1)."public function getCollection(){\n";
				$content.= $this->setTabText(2)."\$view = Zend_Registry::get('view');\n";
				$content.= $this->setTabText(2)."\$collection = \$this->fetchAll();\n";
				$content.= $this->setTabText(2)."\$view->assign('".$name."Collection',\$collection);\n";
				$content.= $this->setTabText(2)."\$view->assign('header','pageHeader.phtml');\n";
				$content.= $this->setTabText(2)."\$view->assign('body','".$this->prepareClassName($name)."/bodyList.phtml');\n";
				$content.= $this->setTabText(2)."\$view->assign('footer','pageFooter.phtml');\n";
				$content.= $this->setTabText(2)."if(sizeof(\$collection)==0){\n";
				$content.= $this->setTabText(3)."\$view->assign('message','Empty');\n";
				$content.= $this->setTabText(2)."}\n";
			$content.= $this->setTabText(1)."}\n\n";
			
			//Edit
			$content.= $this->createComment(array("Edit an object ".$this->prepareClassName($name),"@return void"), 1);
			$content.= $this->setTabText(1)."public function edit(){\n";
			$content.= $this->setTabText(2)."\$get = Zend_Registry::get('get');\n";
			$content.= $this->setTabText(2)."\$view = Zend_Registry::get('view');\n";
			$content.= $this->setTabText(2)."if (!isset(\$get->id))\n";
			$content.= $this->setTabText(2)."{\n";
				$content.= $this->setTabText(3)."\$this->_redirect('/".$this->prepareClassName($name)."/list');\n";
				$content.= $this->setTabText(3)."exit;\n";
			$content.= $this->setTabText(2)."}\n";
			$content.= $this->setTabText(2)."\$id = (int)\$get->id;\n";
			$content.= $this->setTabText(2)."\$".$name."Selected = \$this->find(\$id)->toArray();\n";
			
			$content.= $this->setTabText(2)."\$view->assign('".$name."',\$".$name."Selected[0]);\n";
			$content.= $this->setTabText(2)."\$view->assign('header','pageHeader.phtml');\n";
			$content.= $this->setTabText(2)."\$view->assign('body','".$this->prepareClassName($name)."/bodyEdit.phtml');\n";
			$content.= $this->setTabText(2)."\$view->assign('footer','pageFooter.phtml');\n";
			$content.= $this->setTabText(1)."}\n\n";
			
			//Delete
			$pk = $this->getPrimaryKey($tableObject);
			$content.= $this->createComment(array("Remove an object ".$this->prepareClassName($name),"@return void"), 1);
			$content.= $this->setTabText(1)."public function remove(){\n";
			$content.= $this->setTabText(2)."\$get = Zend_Registry::get('get');\n";
			$content.= $this->setTabText(2)."if(!isset(\$get->id)){\n";
			$content.= $this->setTabText(3)."\$this->_redirect('/".$this->prepareClassName($name)."/list');\n";
			$content.= $this->setTabText(3)."exit;\n";
			$content.= $this->setTabText(2)."}\n";
			$content.= $this->setTabText(2)."\$".$pk." = (int)\$get->id;\n";
			$content.= $this->setTabText(2)."\$where = \$this->getAdapter()->quoteInto('".$pk." = ?', \$".$pk.");\n";
			$content.= $this->setTabText(2)."\$this->delete(\$where);\n";
			$content.= $this->setTabText(1)."}\n\n";
			
			// End of Class
			$content.= "\n}";
			return $content;
		}
		
		/* Create Controllers
		*@param Table $tableObject - Mind Table Object, that contais all knowledge
		*@return String
		*/
		public function controllers(&$tableObject){
			$name = $tableObject->name;
			
			$referenceds = $this->getReferences($tableObject);
			$fks = $this->getForeignKeys($tableObject);
			// Header File
			$content = $this->headerFile("<?php");			
			$content.= $this->createComment(array("Generate by TheWebMind 2.0 Software","Controller Class for ".$name,"@license	http://www.opensource.org/licenses/mit-license.php","@link http://thewebmind.org","@author".$this->setTabText(1)."Jaydson Gomes <jaydson@thewebmind.org>","@author".$this->setTabText(1)."Felipe N. Moura <felipe@thewebmind.org>", "@version  1.0"));
			
			// Class
			$content.= "class ".$this->prepareClassName($name)."Controller extends Zend_Controller_Action {\n";			
			
			// Init
			$content.= $this->createComment(array('Overrides Zend_Controller_Action.init','Load classes needed'),1);
			$content.= $this->setTabText(1)."public function init(){\n";
			$content.= $this->setTabText(2)."Zend_Loader::loadClass('".$this->prepareClassName($name)."');";
			reset($fks);
			while($cur = current($fks)){
				$content.= "\n".$this->setTabText(2)."Zend_Loader::loadClass('".$this->prepareClassName($cur)."');";
				next($fks);
			}
			$content.= "\n".$this->setTabText(1)."}\n\n";
			
			// indexAction
			$content.= $this->createComment(array('Redirect to View List'),1);
			$content.= $this->setTabText(1)."public function indexAction(){\n";
			$content.= $this->setTabText(2)."\$this->_redirect('".$this->prepareClassName($name)."/list');";
			$content.= "\n".$this->setTabText(1)."}\n\n";
			
			//Add
			$content.= $this->createComment(array("Action to add object ".$this->prepareClassName($name),"@return void"), 1);
			$content.= $this->setTabText(1)."public function addAction(){\n";
			reset($fks);
			while($cur = current($fks)){
				$content.= $this->setTabText(2)."\$".$cur." = new ".$this->prepareClassName($cur)."();\n";
				next($fks);
			}
			$content.= $this->setTabText(2)."\$view = Zend_Registry::get('view');\n";
			$content.= $this->setTabText(2)."\$view->assign('header','pageHeader.phtml');\n";
			$content.= $this->setTabText(2)."\$view->assign('body','".$this->prepareClassName($name)."/bodyAdd.phtml');\n";
			$content.= $this->setTabText(2)."\$view->assign('footer','pageFooter.phtml');\n";
			reset($fks);
			while($cur = current($fks)){
				$content.= $this->setTabText(2)."\$view->assign('".$cur."Collection',\$".$cur."->fetchAll());\n";
				next($fks);
			}			
			$content.= $this->setTabText(2)."\$this->_response->setBody(\$view->render('default.phtml'));\n";
			$content.= $this->setTabText(1)."}\n\n";

			//Save
			$content.= $this->createComment(array("Action to save object ".$this->prepareClassName($name),"@return void"), 1);
			$content.= $this->setTabText(1)."public function saveAction(){\n";
			$content.= $this->setTabText(2)."\$table = new ".$this->prepareClassName($name)."();\n";
			$content.= $this->setTabText(2)."\$table->save();\n";
			$content.= $this->setTabText(2)."\$this->_redirect('".$this->prepareClassName($name)."/list');\n";
			$content.= $this->setTabText(1)."}\n\n";
			
			//List
			$content.= $this->createComment(array("Action to list object ".$this->prepareClassName($name),"@return void"), 1);
			$content.= $this->setTabText(1)."public function listAction(){\n";
			$content.= $this->setTabText(2)."\$view = Zend_Registry::get('view');\n";
			$content.= $this->setTabText(2)."\$table = new ".$this->prepareClassName($name)."();\n";
			$content.= $this->setTabText(2)."\$table->getCollection();\n";
			$content.= $this->setTabText(2)."\$this->_response->setBody(\$view->render('default.phtml'));\n";
			$content.= $this->setTabText(1)."}\n\n";
			
			//Edit
			$content.= $this->createComment(array("Action to edit object ".$this->prepareClassName($name),"@return void"), 1);
			$content.= $this->setTabText(1)."public function editAction(){\n";
			$content.= $this->setTabText(2)."\$view = Zend_Registry::get('view');\n";
			reset($fks);
			while($cur = current($fks)){
				$content.= $this->setTabText(2)."\$".$cur." = new ".$this->prepareClassName($cur)."();\n";
				$content.= $this->setTabText(2)."\$view->assign('".$cur."Collection',\$".$cur."->fetchAll());\n";
				next($fks);
			}
			$content.= $this->setTabText(2)."\$table = new ".$this->prepareClassName($name)."();\n";
			$content.= $this->setTabText(2)."\$table->edit();\n";
			$content.= $this->setTabText(2)."\$this->_response->setBody(\$view->render('default.phtml'));\n";
			$content.= $this->setTabText(1)."}\n\n";
			
			//Remove
			$content.= $this->createComment(array("Action to remove object ".$this->prepareClassName($name),"@return void"), 1);
			$content.= $this->setTabText(1)."public function removeAction(){\n";
			$content.= $this->setTabText(2)."\$table = new ".$this->prepareClassName($name)."();\n";
			$content.= $this->setTabText(2)."\$table->remove();\n";
			$content.= $this->setTabText(2)."\$this->_redirect('".$name."/list');\n\n";
			$content.= $this->setTabText(1)."}\n\n";
			$content.= "}";
			
			return $content;
		}
		
		
		/* Create Body Add Form
		*@param Table $tableObject - Mind Table Object, that contais all knowledge
		*@return String
		*/
		public function bodyAddForm(&$tableObject,$edit=false){
			$name = $tableObject->name;
			$attributes = $tableObject->attributes;
			$content = "<h2 id='warning'><?php echo \$this->message?></h2>\n";
			$content.= "<center>\n";
			$content.= "<form action='<?php echo PATH_APPLICATION; ?>/".$this->prepareClassName($name)."/save' method='post'>\n";
			$content.= "<table>\n";
				reset($attributes);
				if($edit){
					$cur = current($attributes);
					$label = isset($cur->comment) ? $cur->comment : $this->prepareLabelName($cur->name);
					$content.= $this->setTabText(1)."<tr style='display:none'>\n";
					
						//Field
						$content.= $this->setTabText(2)."<td colspan='2'>\n";
							$content.= $this->setTabText(3)."<input name='".$cur->name."' value='<?php echo \$this->".$name."['".$cur->name."'];?>'";
							if($cur->size > 0)
							{
								$content.= " maxlength='".$cur->size."'";
							}
							$content.= " id='".$cur->name."' type='hidden'>\n";
						$content.= $this->setTabText(2)."</td>\n";
					$content.= $this->setTabText(1)."</tr>\n";
				}
				$dateInputs= Array();
				$maskedInputs= Array();
				$multiPart= false;
				$textarea = false;
				next($attributes);
				while($cur = current($attributes)){
					$def= (substr($cur->defaultValue, 0, 5)=='Exec:')? '': preg_replace('/^\'|\'$/', '', $cur->defaultValue);
					$value = $edit ? "<?php echo \$this->".$name."['".$cur->name."']; ?>" : $def;
					$label = isset($cur->comment) ? $cur->comment : $this->prepareLabelName($cur->name);
					$textarea = ($cur->type == 'text' || $cur->size > 200) ? true : false;
					if($cur->type== 'time')
					{
						$dateInputs[]= $cur->name;
					}elseif($cur->type== 'file')
						 {
							$multiPart= true;
						 }
					if($cur->mask)
					{
						$maskedInputs[]= Array($cur->name, $cur->mask);
					}
					$content.= $this->setTabText(1)."<tr>\n";
						//Label
						$content.= $this->setTabText(2)."<td>\n";
							$content.= $this->setTabText(3).$label.":\n";
						$content.= $this->setTabText(2)."</td>\n";
						
						//Field
						$content.= $this->setTabText(2)."<td>\n";
						if(!isset($cur->options))
						{
							if($textarea){
								$content.= $this->setTabText(3)."<textarea name='".$cur->name."'";
								$content.= " id='".$cur->name."' style='width:360px; height:210px;'>".$value."</textarea> \n";
							}else{
									$content.= $this->setTabText(3)."<input name='".$cur->name."' value='".$value."'";
									if($cur->size > 0)
									{
										$content.= " maxlength='".$cur->size."'";
									}
									$content.= " id='".$cur->name."' type='".(($cur->type== 'file')? 'file': 'text')."'>\n";
								}
						}else{
								$content.= $this->setTabText(3)."<select id='".$cur->name."' name='".$cur->name."'>";
								if(!$cur->required)
								{
									$content.= $this->setTabText(4)."<option value=''></option>";
								}
								for($i=0, $j=sizeof($cur->options); $i<$j; $i++)
								{
									$content.= "\n".$this->setTabText(4)."<option value='".$cur->options[$i][0]."' ";
									if($edit)
									{
										$content.= "<?php echo (\$this->".$name."['".$cur->name."'] == '".$cur->options[$i][0]."')? \" selected='selected'\" : ''; ?> ";
									}
									$content.= ">\n".$this->setTabText(5).$cur->options[$i][1]."\n".$this->setTabText(4)."</option>";
								}
								$content.= "\n".$this->setTabText(3)."</select>\n";
								//print_r($cur->options);
							 }
						$content.= $this->setTabText(2)."</td>\n";
					$content.= $this->setTabText(1)."</tr>\n";
					//enctype="multipart/form-data"
					next($attributes);
				}
				if($multiPart)
				{
					$content= preg_replace("/\<form /", "<form enctype='multipart/form-data' ", $content, 1);
				}
				
				// Foreign Keys
				$fks = $this->getForeignKeys($tableObject);
				reset($fks);
				while($cur = current($fks)){
					$pk = reset($this->knowledge->tables[$cur]->attributes);
					$content.= $this->setTabText(1)."<tr>\n";
						//Label
						$content.= $this->setTabText(2)."<td>\n";
							$content.= $this->setTabText(3).$this->prepareLabelName($cur).":\n";
						$content.= $this->setTabText(2)."</td>\n";
						
						//Field						
						$content.= $this->setTabText(2)."<td>\n";
							$content.= $this->setTabText(3)."<select name='".$pk->name."'>\n";
								$content.= $this->setTabText(4)."<?php\n";
									$content.= $this->setTabText(5)."foreach (\$this->".$cur."Collection as \$".$cur.")\n";
									$content.=$this->setTabText(5)."{\n";
										$content.=$this->setTabText(6)."?>\n";
											$content.=$this->setTabText(7)."<option <?php echo \$this->".$name."['".$this->getPrefixNameFK().$cur."'] == \$".$cur."->".$this->getPrefixNamePK().$cur." ? 'selected' : ''; ?> value=\"<?php echo \$".$cur."->".$pk->name."; ?>\">\n";
												$content.=$this->setTabText(8)."<?php echo \$".$cur."->".$this->getOptionLabel($cur)."; ?>\n";
											$content.=$this->setTabText(7)."</option>\n";
										$content.=$this->setTabText(6)."<?php\n";
									$content.=$this->setTabText(5)."}\n";
								$content.= $this->setTabText(3)."?>\n";
							$content.= $this->setTabText(3)."</select>\n";
						$content.= $this->setTabText(2)."</td>\n";
					$content.= $this->setTabText(1)."</tr>\n";
					next($fks);
				}
			$content.= $this->setTabText(1)."<tr>\n";
				$content.= $this->setTabText(2)."<td>\n";
					$content.= $this->setTabText(3)."<input type='submit'>\n";
				$content.= $this->setTabText(2)."</td>\n";
			$content.= $this->setTabText(1)."</tr>\n";
			$content.= "</table>\n";
			$content.= "</form>\n</center>";
			$content.= "
	<script type='text/javascript'>
		$(function() {";
			if(sizeof($maskedInputs)>0)
			{
				for($i=0, $j=sizeof($maskedInputs); $i<$j; $i++)
				{
					$content.= "
			$('#".$maskedInputs[$i][0]."').mask('".$maskedInputs[$i][1]."');";
				}
			}
			if(sizeof($dateInputs)>0)
			{
				for($i=0, $j=sizeof($dateInputs); $i<$j; $i++)
				{
					$content.= "
			$('#".$dateInputs[$i]."').datepicker({
				changeMonth: true,
				changeYear: true
			});";
				}
			}
			$content.= "
		});
	</script>";
			return $content;
		}
		
		/* Create Body Edit Form
		*@param Table $tableObject - Mind Table Object, that contais all knowledge
		*@return String
		*/
		public function bodyEditForm(&$tableObject){
			return $this->bodyAddForm($tableObject,true);
		}
		
		/* Create Body List Form
		*@param Table $tableObject - Mind Table Object, that contais all knowledge
		*@return String
		*/
		public function bodyListForm(&$tableObject){
			$name = $tableObject->name;
			$attributes = $tableObject->attributes;
			$referenceds = $this->getReferences($tableObject);
			$pk = $this->getPrimaryKey($tableObject);
			$content= "<h2 id='warning'><?php echo \$this->message?></h2>
<div id='list'>
	<?php 
	foreach (\$this->".$name."Collection as $".$name.")
	{
		";
		for($i=0;$i<sizeof($referenceds);$i++){
			$content.= "\$".$referenceds[$i]."Collection = \$".$name."->findDependentRowset('".$this->prepareClassName($referenceds[$i])."');\n";
		}
		$content.= "
		?>
			<table border='0'>
				<tr>
					<td style='text-align: left'>
							<a href='<?php echo PATH_APPLICATION; ?>/".$this->prepareClassName($name)."/edit?id=<?php echo (\$".$name."->".$pk."); ?>'>
								<?php echo (\$".$name."->".$this->getOptionLabel($name).");?>
							</a><br>
							";
		for($i=0;$i<sizeof($referenceds);$i++){
			$content.= "<?php 
								if(sizeof(\$".$referenceds[$i]."Collection".")>0){
							?>
							<i>".$this->prepareClassName($referenceds[$i]).":</i><br>
								<div style='color:blue;font-size: 12px'>
								<?php
								foreach (\$".$referenceds[$i]."Collection"." as $".$referenceds[$i].")
								{
									echo \$".$referenceds[$i]."->".$this->getOptionLabel($referenceds[$i]).".'<br>';
								}
								?>
								</div>
								<?php }?>\n";
		}
		$content.="				</td>
					<td style='vertical-align: top'>
						<a href='<?php echo PATH_APPLICATION; ?>/".$this->prepareClassName($name)."/remove?id=<?php echo (\$".$name."->".$pk."); ?>'><button>Delete</button></a>	
					</td>
				</tr>
			</table>
		<?php		
	}
	?>
</div>
";
			return $content;
		}
		
		/* Create pageHeader
		*@return String
		*/
		public function pageHeader(){
			$tables = $this->knowledge->tables;
			$content = "<?php
\$session = Zend_Registry::get('session');
?>
<div id='header'>
	Generate by TheWebMind beta 2.0 at ".date('m/d/Y')."
</div>

<div id='menu' class='menu'>
	<ul id='browser' class='filetree'>";
			reset($tables);
			while($table = current($tables)){
				$content.= "
		<li>
			<span class='folder'>".$this->prepareLabelName($table->name)."</span>
			<ul>
				<li><span class='file'><a href='<?php echo PATH_APPLICATION; ?>/".$this->prepareClassName($table->name)."/add'>Add</a></span></li>
				<li><span class='file'><a href='<?php echo PATH_APPLICATION; ?>/".$this->prepareClassName($table->name)."/list'>List</a></span></li>
			</ul>
		</li>";
			next($tables);
			}
			$content .= "	</ul>
</div>";
			return $content;
		}
		
		/* Create Default.phtml		
		*@return String
		*/
		public function defaultPhtml(){
			$content = "<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<title>".$this->project->name."</title>
<link rel='stylesheet' type='text/css' href='/".$this->project->name."/public/styles/zend.generic.css' />
<link rel='stylesheet' href='/".$this->project->name."/public/styles/jquery.treeview.css' />
<script src='/".$this->project->name."/public/scripts/jquery.js' type='text/javascript'></script>
<script src='/".$this->project->name."/public/scripts/default.js' type='text/javascript'></script>
<script src='/".$this->project->name."/public/scripts/jquery.treeview.js' type='text/javascript'></script>
</head>
<body>
	<?php echo \$this->render(\$this->header)?>
	<div id='container'>
	<!-- Content -->
	<?php echo \$this->render(\$this->body)?>
	</div>
</body>
</html>";
			return $content;
		}
		
			
		/* Create Views
		*@param Table $tableObject - Mind Table Object, that contais all knowledge
		*@return String
		*/
		public function views(&$tableObject){
			$name = $tableObject->name;
			$attributes = $tableObject->attributes;
			$referenceds = $this->getReferences($tableObject);			
			$folder = $this->prepareFolderName($name);
			$this->fw->mkDir('project_name/application/views/scripts/'.$folder);
			$this->fw->mkDir('project_name/application/views/scripts/'.strtolower($folder));
			
			//Add
			$this->fw->mkFile('project_name/application/views/scripts/'.strtolower($folder).'/add.phtml','');
			$this->fw->mkFile('project_name/application/views/scripts/'.$folder.'/add.phtml','');
			
			//Edit
			$this->fw->mkFile('project_name/application/views/scripts/'.strtolower($folder).'/edit.phtml','');
			$this->fw->mkFile('project_name/application/views/scripts/'.$folder.'/edit.phtml','');
			
			//List
			$this->fw->mkFile('project_name/application/views/scripts/'.strtolower($folder).'/list.phtml','');
			$this->fw->mkFile('project_name/application/views/scripts/'.$folder.'/list.phtml','');
			
			//bodyAdd.phtml
			$this->fw->mkFile('project_name/application/views/scripts/'.$folder.'/bodyAdd.phtml',$this->bodyAddForm($tableObject));
			
			//bodyList.phtml
			$this->fw->mkFile('project_name/application/views/scripts/'.$folder.'/bodyList.phtml',$this->bodyListForm($tableObject));
			
			//bodyEdit.phtml
			$this->fw->mkFile('project_name/application/views/scripts/'.$folder.'/bodyEdit.phtml',$this->bodyEditForm($tableObject));			
						
		}
		
		/**
		* Tells mind what the structure to copy
		*@return String
		*/
		public function getStructure(){
			return $this->structure;
		}
		
		/**
		*Method Called for each table
		*@param Table 	$tableObject - Mind Table Object, that contais all knowledge
		*@return void
		*/
		public function applyCRUD($tableObject){
			//Models
			$this->fw->mkFile('project_name/application/models/'
							  .$this->prepareClassName($tableObject->name).'.php',
							  $this->models($tableObject));
			
			//Controllers
			$this->fw->mkFile('project_name/application/controllers/'
							  .$this->prepareClassName($tableObject->name).'Controller.php',
							  $this->controllers($tableObject));

			//Views
			$this->views($tableObject);
		}
		
		/**
		*Method that return the correct PDO Adapter name
		*@param type A String with the type of DBMS
		*@return String
		*/
		public function getPDOAdapter($type){
			$pdoAdapter = '';
			
			switch(strtolower($type)){
				case 'mysql' :
					$pdoAdapter = 'Pdo_Mysql';
				break;
				case 'postgresql' :
					$pdoAdapter = 'Pdo_Pgsql';
				break;
				case 'sqlite' :
					$pdoAdapter = 'Pdo_Sqlite';
				break;
				case 'oracle':
					$pdoAdapter = 'Pdo_Oci';
				break;
				case 'mssql':
					$pdoAdapter = 'Pdo_Mssql';
				break;
				case 'db2':
					$pdoAdapter = 'Pdo_Ibm';
				break;
				default : $pdoAdapter = 'Pdo_Mysql';
			}
			return $pdoAdapter;
		}		
		
		/**
		* Method executed when module start
		*/
		public function onStart(){
			// Create database config file
			$file = $this->fw->getContent("config.ini");
			$file = str_replace("<dbType>",$this->getPDOAdapter($this->project->dbms),$file);
			$file = str_replace("<host>",$this->project->environment["development"]["dbAddress"],$file);
			$file = str_replace("<user>",$this->project->environment["development"]["user"],$file);
			$file = str_replace("<pass>",$this->project->environment["development"]["userPwd"],$file);
			$file = str_replace("<db>",$this->project->environment["development"]["dbName"],$file);
			$file = str_replace("<port>",$this->project->environment["development"]["dbPort"],$file);
			$this->fw->mkFile('project_name/application/config.ini',$file);
		}
		
		/**
		* Override Module.onFinish
		* Method executed when module start
		*/
		public function onFinish(){
			// Renames the root dir
			$this->fw->rename('project_name',$this->project->name);
		}
		
		/**
		* Override Module.callExtra
		* Call extra function
		*/
		public function callExtra(){
			//Create pageHeader 
			$this->fw->mkFile($this->project->name.'/application/views/scripts/pageHeader.phtml',$this->pageHeader());
			
			//Create default.phtml
			$this->fw->mkFile($this->project->name.'/application/views/scripts/dafault.phtml',$this->defaultPhtml());
			
			// Replace project information
			$this->fw->mkFile($this->project->name."/index.php",
							  str_replace("<projectName>",
										  $this->project->name,
										  $this->fw->getContent("structure/project_name/index.php")));
		}
		
		/**
		* Construct
		*/
		public function __construct($project){
			GLOBAL $_FW;
			$this->fw= $_FW;
			$this->structure= 'structure'; // tells Mind what is the structure directory to be based on
			$this->project = $project;
			$this->knowledge= $project->knowledge;
		}
	}
?>
