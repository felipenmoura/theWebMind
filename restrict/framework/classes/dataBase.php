<?php
	//require_once('sgbd.php');
	//require_once('schema.php');
	//require_once('foreignKey.php');
	$l;
	class dataBase
	{
		public  $name   = '';
		public  $SGBD   = null;
		public  $schemas= Array();
		public  $dbAddress= 'localhost';
		public  $rootUser= 'root';
		public  $rootUserPwd= '';
		public  $user= '';
		public  $userPwd= '';
		public  $description= '';
		
		public function addSchema($name)
		{
			$this->schemas[$name]= new Schema($name, $this->SGBD);
			return $this->schemas[$name];
		}
		public function showTables($name)
		{
			$ts= $this->schemas[$name]->getTables();
			foreach($ts as $linha)
			{
				echo $linha->getTableName().'<br/>';
			}
		}
		public function showQuery($b= false)
		{
			$this->query= $this->generateQuery($b);
			if($b)
				return '<pre>'.$this->query.'</pre>';
			else
				return $this->query;
		}
		private function generateQuery($b= false)
		{
			//echo $this->SGBD->execute->createSchema();
			//echo ($b)? '1': '0';
			$this->query= '';
			$r= '';
			foreach($this->schemas as $schema)
			{
				$r.= str_replace('<databasename>', $this->name, $this->SGBD->execute->header()).$schema->generateSchemaQuery($b, $this->SGBD);
			}
			if($b)
			{
				$ar_tmpR= explode('
', $r);
				$tmpR = "<ol type='1' style='background-color: #dedede; border: solid 1 #000000;'>";
				for($i=0; $i<count($ar_tmpR); $i++)
				{
					$tmpR.= '<li style="background: white;">';
						$tmpR.= $ar_tmpR[$i].'<br/>';
					$tmpR.= '</li>';
				}
				$tmpR.="</ol>";
				return $tmpR;
			}
			return $r;
		}
		/*public function setLanguage($language)
		{
			if(!$language || $language=='')
				$language= 'en';
			require_once('languages/'.$language.'.php');
		}*/
		public function __construct($sgbd)
		{
			$this->SGBD= new SGBD($sgbd);
		}
		
		public function setSGBD($SGBD)
		{
			$this->SGBD= new SGBD($SGBD);
		}
		
		public function verifyFKs($obj, $fkName)
		{
			//print_r($obj->attributes);
			//echo "procurando por $fkName na tabela ".$obj->tableName.'<br/>';
			foreach($obj->foreignKeys as $fk)
			{
				//echo 'comparando '.$fk->name .' com '. $fkName;
				if($fk->name == $fkName)
				{
					//echo ' retorna 1<br/>';
					return $fk;
				}
			}
			return false;
		}
		/*public function showTables($schemaName)
		{
			$ar= $this->schemas[$schemaName]->tables;
			foreach($arPrincipal as $table)
			{ // para cada tabela
				echo $table->tableName.'<br>';
			}
		}*/
		public function runOverTheTree($schemaName, $table)
		{
			foreach($table->foreignKeys as $fk)
			{
				reset($this->schemas[$schemaName]->tables[$fk->target]->foreignKeys);
				$contiue= true;
				while($backFk= current($this->schemas[$schemaName]->tables[$fk->target]->foreignKeys))
				{
					if($backFk->target == $table->tableName)
					{
						if($this->schemas[$schemaName]->tables[$fk->target]->becomes == false)
						{
							$contiue= false;
							$tableToPoint= $table;
						}else{
								if($this->schemas[$schemaName]->tables[$fk->target]->becomes->tableName != $table->tableName)
								{
									$contiue= false;
								}
							 }
						if(!$contiue && $backFk->target == $table->tableName)
						{
							if($tableToPoint->becomes->tableName)
							{
								$tableToPoint= $this->runOverTheTree($schemaName, $tableToPoint->becomes);
							}
							if($tableToPoint)
							{
								$this->schemas[$schemaName]->tables[$fk->target]->becomes= $tableToPoint;
								break;
							}
						}else{
								$continue= true;
							 }
					}
					next($this->schemas[$schemaName]->tables[$fk->target]->foreignKeys);
				}
				//echo '<br>';
			}
			return $table;
		}
		public function getTheRootTable($schemaName, $table)
		{
			/*foreach($table->foreignKeys as $fk)
			{
				reset($this->schemas[$schemaName]->tables[$fk->target]->foreignKeys);
				$contiue= true;
				while($backFk= current($this->schemas[$schemaName]->tables[$fk->target]->foreignKeys))
				{
					//if($backFk->target == $table->tableName)
					//{
						if($this->schemas[$schemaName]->tables[$fk->target]->becomes == false)
						{
							$contiue= false;
							$tableToPoint= $table;
						}else{
								if($this->schemas[$schemaName]->tables[$fk->target]->becomes->tableName != $table->tableName)
								{
									$contiue= false;
								}
							 }
						if(!$contiue && $backFk->target == $table->tableName)
						{
							if($tableToPoint->becomes->tableName)
							{
								$tableToPoint= $this->runOverTheTree($schemaName, $tableToPoint->becomes);
							}
							$this->schemas[$schemaName]->tables[$fk->target]->becomes= $tableToPoint;
							break;
						}else{
								$continue= true;
							 }
					//}
					next($this->schemas[$schemaName]->tables[$fk->target]->foreignKeys);
				}
			}*/
			return $table;
		}
		public function organizeTables($schemaName)
		{
			$arPrincipal= $this->schemas[$schemaName]->tables;
			$total= count($arPrincipal);
			$arWithoutFK= Array();
			$arWithFK= Array();
			$arBuffer= Array();
			
			foreach($arPrincipal as $table)
			{ // para cada tabela
				if(count($table->foreignKeys) == 0)
				{
					if(!in_array($table, $arWithoutFK))
						$arWithoutFK[]= $table;
				}else{
						if(!in_array($table, $arWithFK))
							$arWithFK[]= $table;
					 }
			}
			
			unset($arPrincipal);
			$arPrincipal= Array();
			$arPrincipal= $arWithoutFK;
			
			$c=0;
			$ar_mpRelation= Array();
			while($c<$total)
			{
				for($i=0; $i<count($arWithFK); $i++)
				{
					$preFk= true;
					if($arWithFK[$i] != false)
					{
						foreach($arWithFK[$i]->foreignKeys as $fk)
						{
							if(!in_array($this->schemas[$schemaName]->tables[$fk->target], $arPrincipal))
							{
								$preFk= false;
								break;
							}else{
								 }
						}
						if($preFk == true)
						{
							$arPrincipal[]= $arWithFK[$i];
							$arWithFK[$i]= false;
							unset($arWithFK[$i]);
						}
					}
				}
				$c++;
			}
			
			if(count($arWithFK) > 0)
			{
				reset($arWithFK);
				while($c= current($arWithFK))
				{
					$key= key($arWithFK);
					if(trim($arWithFK[$key]->tableName) != '')
					{
						$table= $this->runOverTheTree($schemaName, $arWithFK[$key]);
						$ar_mpRelation[$key]= $arWithFK[$key];
						if($table->becomes == false)
						{
							$arPrincipal[]= $table;
						}else{
								$atts= $table->getOnlyAttributes();
								foreach($atts as $att)
								{
									if($table->foreignKeys[$att->attName])
									{
										$target= $this->schemas[$schemaName]->tables[$table->foreignKeys[$att->attName]->target];
										if($target->becomes)
										{
											if($target->becomes->tableName == $target->tableName)
												$target->becomes= false;
											$tmpTable= $this->schemas[$schemaName]->tables[$table->foreignKeys[$att->attName]->target]->becomes;
											while($tmpTable->becomes != false && $tmpTable->becomes->tableName != $tmpTable->tableName)
											{
												$tmpTable= $this->schemas[$schemaName]->tables[$table->foreignKeys[$att->attName]->target]->becomes;
											}
										}else{
												$tmpTable= $this->schemas[$schemaName]->tables[$table->foreignKeys[$att->attName]->target];
											 }
										if($tmpTable->tableName != $table->tableName)
										{
											$table->becomes->attributes[$att->attName]= $att;
											$table->becomes->foreignKeys[$att->attName]= $table->foreignKeys[$att->attName];
										}
									}else{
											$att->attName= $table->tableName.'_'.$att->attName;
											$table->becomes->attributes[$att->attName]= $att;
											//echo ' é campo normal';
										 }
									//echo '<br>';
								}
								unset($table->becomes->foreignKeys['fk_'.$table->tableName]);
								unset($table->becomes->attributes['fk_'.$table->tableName]);
							 }
					}
					next($arWithFK);
				}
			}

			
			for($i=0; $i<count($arPrincipal); $i++)
			{
				foreach($arPrincipal[$i]->foreignKeys as $fk)
				{
					if($arPrincipal[$i]->tableName == $fk->target || !$this->schemas[$schemaName]->tables[$fk->target])
					{
						unset($arPrincipal[$i]->attributes[$fk->name]);
						unset($arPrincipal[$i]->foreignKeys[$fk->name]);
						unset($fk);
					}else{
							if($this->schemas[$schemaName]->tables[$fk->target]->becomes->tableName)
							{
								if($this->schemas[$schemaName]->tables[$fk->target]->becomes->tableName != $arPrincipal[$i]->tableName)
								{
									$tmpFk= $fk;
									$bcm= $this->schemas[$schemaName]->tables[$fk->target]->becomes;
									$arPrincipal[$i]->attributes[$fk->name]->attName= 'fk_'.$bcm->tableName;
									$fk->name= 'fk_'.$bcm->tableName;
									$fk->target= $bcm->tableName;
									if(!$arPrincipal[$i]->foreignKeys[$fk->name])
										$arPrincipal[$i]->foreignKeys[$fk->name]= $fk;
								}
								if($this->schemas[$schemaName]->tables[$arPrincipal[$i]->tableName]->foreignKeys['fk_'.$fk->target])
								{
									if($this->schemas[$schemaName]->tables[$fk->target]->becomes && $this->schemas[$schemaName]->tables[$fk->target]->becomes->tableName != $fk->target)
									{
										unset($arPrincipal[$i]->attributes[$fk->name]);
										unset($arPrincipal[$i]->foreignKeys['fk_'.$fk->target]);
									}
								}else{
										unset($arPrincipal[$i]->attributes[$tmpFk->name]);
										unset($arPrincipal[$i]->foreignKeys[$tmpFk->name]);
									 }
							}
						 }
				}
			}
			//echo '</pre>';
			//$actual
			//echo count($arPrincipal);
			$this->schemas[$schemaName]->tables= $arPrincipal;
			$arPrincipal= '';
			$arPrincipal= Array();
			$limit= count($this->schemas[$schemaName]->tables);
			for($i=0; $i<$limit; $i++)
			{
				//echo '---'.$i.' '.$this->schemas[$schemaName]->tables[$i]->tableName.'<br>';
				$arPrincipal[$this->schemas[$schemaName]->tables[$i]->tableName]= $this->schemas[$schemaName]->tables[$i];
				unset($this->schemas[$schemaName]->tables[$i]);
			}
			//echo count($arPrincipal);
			$pronta= Array();
			$continue= false;
			//$cu= 0;
			while($continue == false)
			{
				//echo count($arPrincipal) .' ==== '. count($pronta).'<br>';
				if(count($arPrincipal) != count($pronta))
				{
					foreach($arPrincipal as $table)
					{
						if(count($table->foreignKeys) == 0)
						{
							$pronta[$table->tableName]= $table;
						}else{
								$c= true;
								foreach($table->foreignKeys as $fk)
								{
									//echo $fk->name.' - ';
									if(!$pronta[$fk->target])
									{
										$c= false;
									}
								}
								if($c)
								{
									$pronta[$table->tableName]= $table;
								}
							 }
					}
				}else{
						$continue= true;
					 }
				/* $cu++;
				if($cu > 100)
					break; */
			}
			/*echo '<pre>';
			print_r($pronta);
			echo '</pre>';*/
			
			foreach($pronta as $table)
			{
				//echo "<pre>";
				//print_r($table);
				foreach($table->foreignKeys as $fk)
				{
					$pronta[$table->tableName]->addRequiredTable($fk->target);
					//$pronta[$fk->target]->addRequiredTable($table->tableName);
					//print_r($fk);
				}
			}
			
			$this->schemas[$schemaName]->tables= $pronta;
			
			/*#############################################*/
			//$pronta= Array();
			//echo '<pre>';
			/*while(!) // retorna 
			{
				
			}*/
			/*for($i= 0; $i< count($this->schemas[$schemaName]->tables); $i++)
			{
				if(count($this->schemas[$schemaName]->tables[$i]->foreignKeys) == 0)
				{
					$pronta[]= $this->schemas[$schemaName]->tables[$i];
				}else{
						//for($j=0; $j<count($this->schemas[$schemaName]->tables[$i]->foreignKeys); $j++)
						foreach($this->schemas[$schemaName]->tables[$i]->foreignKeys as $fk)
						{
							if(in_array($fk, $pronta))
						}
					 }
			}*/
			//print_r($pronta);
			//echo '</pre>';
			/*#############################################*/
			return;
		}
		public function generate($name, $t)
		{
		}
		public function getDetails($textOnly=false)
		{
			if($textOnly)
			{
				$ret= str_replace('<br/>', '\n', $this->getDetails());
				return str_replace('<br/>', '\n', $ret);
			}
			return $this->details;
		}
		public function showDetails($textOnly=false)
		{
			if($textOnly)
			{
				$ret= 'puta que pariu, caralho \n '.str_replace('<br/>', '\n', $this->getDetails());
				return str_replace('<br/>', '\n', $ret);
			}else
				return '<pre>'.$this->getDetails().'</pre>';
		}
		/**/
		public function generateXML()
		{
			foreach($this->schemas as $schema)
			{
				//$this->xml.= $schema->generateSchemaXML();
			}
			return $this->xml;
		}
		public function getXML()
		{
			return $this->xml;
		}
		public function showXML()
		{
			return '<pre>'.$this->xml.'</pre>';
		}
	}
?>