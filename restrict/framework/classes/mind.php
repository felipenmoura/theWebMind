<?php
	//header('Content-Type: text/plain; charset=ISO-8859-1');
	//include('scripts/functions.php');
	include('sgbd.php');
	include('dataBase.php');
	include('schema.php');
	include('table.php');
	include('attribute.php');
	include('primaryKey.php');
	include('foreignKey.php');
	session_start();
	ini_set("memory_limit","80M");
	set_time_limit(3600);	//	uma hora
	//include('');
	class MindProcessor
	{
		private $HAS 	= ' |||||||HAS||||||| ';
		private $OR  	= ' |||||||OR||||||| ';
		private $USELESS= ' |||||||USELESS||||||| ';
		private $DEFAULTSIZEATTRIBUTE= 16;
		private $details= '<pre>';
		private $configDirectory= '';//$_SESSION['mind']['configDirectory'];
		public  $query  = '';
		private $xml	= '';
		private $base	= '';
		public  $dataBases= Array();
		public  $description= '';
		public  $projectName= "";
		public  $language= 'en';
		private $defaultDataBase= 'Postgres';
		private $versionFirstLevel= '0';
		private $versionSecondLevel= '1';
		private $versionThirdLevel= '0';
		
		public function setLanguage($language)
		{
			if(!$language || $language=='')
				$language= 'en';
			$this->language= $language;
			require_once('languages/'.$language.'.php');
		}
		public function formatParam($x)
		{
			GLOBAL $l;
			$x= trim($this->fixChars($x, $l['charsToFix'], $l['fixedChars']));
			if(strpos($x, '\"') !== false)
				$x= str_replace(' ', '_', trim(str_replace('\"', '', substr($x, strpos($x, '\"'), strrpos($x, '\"')))));
			if(strpos($x, ' ') !== false)
				$x= substr($x, 0, strpos($x, ' '));
			$p= '/\W/';
			return preg_replace($p, '', $x);
		}
		public function var2attribute($str)
		{
			$reg= '/[A-Z*]/';
			$matched = preg_split($reg, $str, -1, PREG_SPLIT_OFFSET_CAPTURE);
			$len= count($matched);
			for($i=0; $i<$len; $i++)
			{
				if($i!=$len-1)
				{
					$char= '_'.strtolower(substr($str, $matched[$i+1][1]-1, 1));
				}else
					$char= '';
				$matched[$i]= $matched[$i][0].$char;
			}
			return $str= preg_replace('/^_/', '', join($matched));
		}
		public function attribute2var($str)
		{
			$str= preg_replace('/_$/', '', preg_replace('/^_/', '', $str));
			$str= explode('_', $str);
			for($i=1; $i<count($str); $i++)
			{
				$str[$i]= ucfirst($str[$i]);
			}
			return join($str);
		}
		public function fixChars($text, $ar1, $ar2, $var= false)
		{
			for($c=0; $c<count($ar1); $c++)
			{
				$text= str_replace($ar1[$c], $ar2[$c], $text);
			}
			if($var)
				$text= $this->attribute2var($text);
			else
				$text= $this->var2attribute($text);
			return $text;
		}
		public function addDetails($d)
		{
			$this->details.= $d;
		}
		public function getDetails($textOnly=false)
		{
			if($textOnly)
			{
				$header= "Generated automatically by Mind

";
				$ret= str_replace('<br>', '\n', $this->getDetails());
				return str_replace('<br/>', '\n', $ret);
			}
			return $this->details;
		}
		public function showDetails($textOnly=false)
		{
			if($textOnly)
			{
				$ret= str_replace('<br>', '\n', $this->getDetails());
				return str_replace('<br/>', '\n', $ret);
			}else
				return '<pre>'.$this->getDetails().'</pre>';
		}
		public function replaceFromArray($ar, $r, $text)
		{
			for($c=0; $c<count($ar); $c++)
			{
				$text= str_replace($ar[$c], $r, $text);
			}
			return $text;
		}
		public function arrayInString($ar, $text, $b= false, $dir=false)
		{
			foreach($ar as $l)
			{
				if(preg_match('/^'.trim($l).'/', $text) > 0)
				{
					$r= Array(''.$l.'', ''.strpos($text, trim($l)).'', ''.strlen($l).'');
					return $r;
				}
			}
			return false;
		}
		public function generate($schemaName, $db, $cd)
		{
			//  Data Base
			GLOBAL $l;
			$this->base= trim($cd);
			$this->projectName= trim($this->fixChars($this->projectName, $l['charsToFix'], $l['fixedChars']));
			$this->tmpXMLFile= 'tmp/structure_'.$_SESSION['user']['cod'].'.xml';
			fopen($this->tmpXMLFile, 'w+');
			file_put_contents($this->tmpXMLFile, '<?xml version="1.0" encoding="ISO-8859-1"?>
<mind>
</mind>');
			 
			$this->xml = simplexml_load_file($this->tmpXMLFile);

			$this->xml->addChild('base');
			$this->xml->base['language']= $this->language;
			$this->xml->base->addChild('name', urlencode($this->projectName));
			$this->xml->base->addChild('version', $this->versionFirstLevel.'.'.$this->versionSecondLevel.'.'.$this->versionThirdLevel);
			$this->xml->base->addChild('date', date('m/d/Y'));
			$this->xml->base->date['day']= date('d');
			$this->xml->base->date['month']= date('m');
			$this->xml->base->date['year']= date('Y');
			$this->xml->base->date['hour']= date('H');
			$this->xml->base->date['minute']= date('i');
			$this->xml->base->date['second']= date('s');
			$this->xml->base->addChild('description', urlencode($this->description));
			$this->xml->base->addChild('mindcode', (str_replace('&', '&amp;', urlencode($this->base))));
			$this->xml->addChild('databases');
			
			$dbCount= 0;
			foreach($this->dataBases as $db)
			{
				$db->name= trim($this->fixChars($db->name, $l['charsToFix'], $l['fixedChars']));
				$this->xml->databases->addChild('database');
				$this->xml->databases->database[$dbCount]['address']= $db->dbAddress;
				$this->xml->databases->database[$dbCount]['name']= $db->name;
				$port= explode(':', $db->dbAddress);
				$port= (count($port)>1)? $port[1]: $db->SGBD->execute->defaultPort;
				$this->xml->databases->database[$dbCount]['port']= $port;
				$this->xml->databases->database[$dbCount]['sgbd']= (trim($db->SGBD->name) != '')? $db->SGBD->name: $this->defaultDataBase;
				$this->xml->databases->database[$dbCount]->addChild('root');
				$this->xml->databases->database[$dbCount]->root['username']= $db->rootUser;
				$this->xml->databases->database[$dbCount]->root['password']= $db->rootUserPwd;
				$this->xml->databases->database[$dbCount]->addChild('user');
				$this->xml->databases->database[$dbCount]->user['username']= $db->user;
				$this->xml->databases->database[$dbCount]->user['password']= $db->userPwd;
				$this->xml->databases->database[$dbCount]->addChild('schemas');
				$atualSchema= $db->name;
				foreach($db->schemas as $schema)
				{
					$this->xml->databases->database[$dbCount]->schemas->addChild('schema');
					$this->xml->databases->database[$dbCount]->schemas->schema['name']= $schema->name;
					$this->xml->databases->database[$dbCount]->schemas->schema['description']= $schema->description;
					$this->xml->databases->database[$dbCount]->schemas->schema->addChild('tables');
					
					$p= '/\/\*(.|\W)*?\*\//';
					$t= preg_replace($p, '', $cd);
					$t= preg_split('/\n/', $t);
					
					GLOBAL $l;
					$this->details.= "Creating schema: ".$atualSchema."<br>";
					foreach($t as $linha)
					{
						$linha= $this->replaceFromArray($l['links'], $this->HAS, $linha);
						$linha= explode($this->HAS, $linha);
						if(count($linha) > 1)
						{ // possui HAS ou derivados
							$lin= $linha[0];
							$lin= explode(' ', trim($lin));
							$tableLeft= $lin[count($lin)-1];
							// tabela da esquerda
							/*if(substr($tableLeft, strlen($tableLeft)-1) == '"')
								$tableLeft= substr(join('_', $lin), strpos(join('_', $lin), '"')+1, -2);*/
							$tableLeft= $this->formatParam($tableLeft);
							if(!$schema->tables[$tableLeft])
							{
								$schema->addTable($tableLeft, false, $db->SGBD);
								$this->addDetails("Creating table: ".$tableLeft."<br>");
							}
							// tabela da direita
							$lin= $linha[1];
							$lin= $this->replaceFromArray($l['or'], $this->OR, $lin);
							$lin= explode('//', $lin, 2);
							$comment= $lin[1];
							if(strpos($lin[0], ":"))
							{ // atributo
								$attName= substr($lin[0], 0, strpos($lin[0], ":"));
								//$attName= str_replace($this->USELESS, '', $lin);
								if(substr($attName, strlen(trim($attName))-1) == '"')
								{
									$tmpAttName= strpos($attName, '"');
									//$attName= str_replace(' ', '_', trim(str_replace('\"', '', substr($attName, $tmpAttName+1, strrpos($attName, '\"')))));
									$attName= $this->formatParam($attName);
								}else{
										$attName= substr($attName, strrpos($attName, ' '));
									 }
								$attName= trim($this->fixChars($attName, $l['charsToFix'], $l['fixedChars']));
								$attType=  substr(strchr($lin[0], ":"), 1);
								// verifica aqui, os tipos de attributos
								$attType= Attribute::getCaracteristics($attType);
								$attTypeName= '';
								if(strpos($attType, '('))
								{
									$attType= trim($attType);
									$strParPos= strpos($attType, '(');
									$attTypeName= substr($attType, 0, $strParPos);
									$attTypeSP= substr($attType, $strParPos+1, -1);
									$attTypeSP= explode(',', $attTypeSP, 2);
									$attTypeSize= trim($attTypeSP[0]);
								}else{
										$attTypeName= $attType;
										$attTypeSize= $this->DEFAULTSIZEATTRIBUTE;
									 }
								reset($l['type']);
								while(next($l['type']))
								{
									if(in_array($attTypeName, $l['type'][key($l['type'])]))
									{
										$attTypeName= key($l['type']);
										$attTypeName= $db->SGBD->getSGBD()->attType[$attTypeName];
										break;
									}
								}
								$attTypeName= trim($this->fixChars($attTypeName, $l['charsToFix'], $l['fixedChars']));
								$attName= trim($this->fixChars($attName, $l['charsToFix'], $l['fixedChars']));
								
								if(!$schema->tables[$tableLeft]->attributes[$attName])
								{
									$attTypeName= Array($attTypeName);
									$attTypeName[1]= $attTypeSize;
									$attTypeName[2]= $attTypeSP[1];
									$this->details.= 'Creating attribute: '.$attName.' of type '.$attTypeName[0].' on table '.$tableLeft.'<br>';
									if($schema->tables[$tableLeft]->tableName != $tableLeft)
										$attName= $tableLeft.'_'.$attName;
									$schema->tables[$tableLeft]->addAttribute($attName, $attTypeName, $comment);
								}
								$attTypeSP= false;
								$attTypeSize= false;
								$attName= false;
								$attTypeName= false;
							}else{  // ligação de tabela
									$cu++;
									if(strpos($lin[0], $this->OR))
									{ // min E max
										$lin= explode($this->OR, $lin[0], 2);
										$tmpMin= ($this->arrayInString($l['cardinalityMin'][0], $lin[0], true))? '0':(($this->arrayInString($l['cardinalityMin'][1], $lin[0], true))? '1': 'q');
										$rghtTbl= $this->arrayInString($l['cardinalityMax'][1], ' '.$lin[1], true);
										
										$x= $this->arrayInString($l['cardinalityMax'][0], $lin[1], true);
										if($x === false)
										{
											$x= $this->arrayInString($l['cardinalityMax'][1], $lin[1], true);
											$tmpMax= 'n';
										}else
											$tmpMax= '1';
										$tmpRhtTbl= $lin[1];
										
									}else{ // somente max
											$tmpRhtTbl= $lin[0];
											$tmpMin= 0;
										
											$x= $this->arrayInString($l['cardinalityMax'][0], $tmpRhtTbl, true);
											if($x === false)
											{
												$x= $this->arrayInString($l['cardinalityMax'][1], $tmpRhtTbl);
												$tmpMax= 'n';
											}else
												$tmpMax= '1';
										 }
									$sum= $x[1]+$x[2];
									if($sum!=0)
										$sum--;
									$rghtTbl= substr($tmpRhtTbl, $sum);
									$rghtTbl= $this->formatParam($rghtTbl);
									
									if(!$schema->tables[$rghtTbl])
									{
										$schema->addTable($rghtTbl, false, $db->SGBD);
										$this->addDetails("Creating table: ".$rghtTbl."<br>");
									}
									if($tmpMax == 1)
									{
										if($db->verifyFKs($schema->tables[$schema->tables[$rghtTbl]->tableName], 'fk_'.$schema->tables[$tableLeft]->tableName))
										{	// sao 1 para 1
											$rghtTbl= $schema->tables[$rghtTbl]->tableName;
											$tableRight= $schema->tables[$schema->tables[$tableLeft]->tableName]->addAttribute('fk_'.$rghtTbl, Attribute::getCaracteristics($l['type']['integer'][0].'(8)'), $comment);
											$schema->tables[$schema->tables[$tableLeft]->tableName]->addForeignKey($tableRight, $rghtTbl, $schema->name);
											$this->details.= 'Adding Foreign key on '.$schema->tables[$tableLeft]->tableName.': '.'fk_'.$rghtTbl.' references '.$rghtTbl.'<br>';
										}else{
												if($schema->tables[$rghtTbl]->tableName != $tableLeft && $schema->tables[$schema->tables[$tableLeft]->tableName]->tableName != $rghtTbl)
												{
													$rghtTbl= $schema->tables[$rghtTbl]->tableName;
													$tableRight= $schema->tables[$schema->tables[$tableLeft]->tableName]->addAttribute('fk_'.$rghtTbl, Attribute::getCaracteristics($l['type']['integer'][0].'(8)'), $comment);
													$schema->tables[$schema->tables[$tableLeft]->tableName]->addForeignKey($tableRight, $rghtTbl, $schema->name);
													$this->details.= 'Adding Foreign key on '.$schema->tables[$tableLeft]->tableName.': '.'fk_'.$rghtTbl.' references '.$rghtTbl.'<br>';
												}
											 }
									}elseif($tmpMax == 'n')
										{
											if($db->verifyFKs($schema->tables[$schema->tables[$tableLeft]->tableName], 'fk_'.$schema->tables[$rghtTbl]->tableName))
											{	// sao n para n
												//$newTable= $schema->addTable($tableLeft.'_'.$rghtTbl, true);	// GAMBI !! melhorar esta parte
												$newTable= $schema->addTable($tableLeft.'_'.$rghtTbl, false, $db->SGBD);
												// fim GAMBI
												$this->addDetails("Creating table: ".$newTable->tableName."<br>");
												$fk1= $newTable->addAttribute('fk_'.$tableLeft, Attribute::getCaracteristics($l['type']['integer'][0].'(8)'), $comment);
												$fk2= $newTable->addAttribute('fk_'.$rghtTbl, Attribute::getCaracteristics($l['type']['integer'][0].'(8)'), $comment);
												
												$newTable->addForeignKey($fk2, $schema->tables[$rghtTbl]->tableName, $schema->name);
												$this->details.= 'Adding Foreign key on '.$schema->tables[$newTable->tableName]->tableName.': '.'fk_'.$rghtTbl.' references '.$rghtTbl.'<br>';
												$newTable->addForeignKey($fk1, $schema->tables[$tableLeft]->tableName, $schema->name);
												$this->details.= 'Adding Foreign key on '.$schema->tables[$newTable->tableName]->tableName.': '.'fk_'.$tableLeft.' references '.$tableLeft.'<br>';
												
												unset($schema->tables[$schema->tables[$tableLeft]->tableName]->foreignKeys['fk_'.$schema->tables[$rghtTbl]->tableName]);
												unset($schema->tables[$schema->tables[$tableLeft]->tableName]->attributes['fk_'.$schema->tables[$rghtTbl]->tableName]);
												
												$newTable->addPrimaryKey($fk1, $name);
												$newTable->addPrimaryKey($fk2, $name);
											}else{
													$tableLeftReference= $schema->tables[$schema->tables[$rghtTbl]->tableName]->addAttribute('fk_'.$schema->tables[$schema->tables[$tableLeft]->tableName]->tableName, Attribute::getCaracteristics($l['type']['integer'][0].'(8)'), $comment);
													$schema->tables[$schema->tables[$rghtTbl]->tableName]->addForeignKey($tableLeftReference, $schema->tables[$schema->tables[$tableLeft]->tableName]->tableName, $schema->name);
													$this->details.= 'Adding Foreign key on '.$schema->tables[$rghtTbl]->tableName.': '.'fk_'.$schema->tables[$schema->tables[$tableLeft]->tableName]->tableName.' references '.$schema->tables[$schema->tables[$tableLeft]->tableName]->tableName.'<br>';
												 }
										}
								 }
						}
						$comment= '';
					}
					$db->organizeTables($schema->name);
					
					$dbCount= 0;
					$scCount= 0;
					$tableCount= 0;
					$attCound= 0;
					foreach($this->dataBases as $db)
					{
						foreach($db->schemas as $schema)
						{
							foreach($schema->tables as $table)
							{
								$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->addChild('table');
								$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]['name']= $table->tableName;
								$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]['id']= $table->getSerial();
								$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]['description']= $table->description;
								$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->addChild('attributes');
								$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->addChild('requires');
								for($kk=0;$kk<count($table->requires);$kk++)
								{
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->requires->addChild("required", $table->requires[$kk]);
									//echo '<b><i>'.$table->requires[$kk].' - '.'</i></b>';
									//$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->requires->addChild("required", $table->requires[$kk]);
									
								}
								$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->requires;
								foreach($table->attributes as $attribute)
								{
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->addChild('attribute');
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['name']= $attribute->attName;
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['id']= $attribute->attName;
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['pk']= ($table->primaryKeys[$attribute->attName])? 1: 0;
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['type']= $attribute->attType;
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['limit']= $attribute->size;
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['references']= ($table->foreignKeys[$attribute->attName])? $table->foreignKeys[$attribute->attName]->target : '';
									
									
									
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['default']= utf8_encode($attribute->attDefault);
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['description']= '';
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['notnull']= ($table->primaryKeys[$attribute->attName])? 1: 0;
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['comment']= utf8_encode($attribute->attComment);
									$this->xml->databases->database[$dbCount]->schemas->schema[$scCount]->tables->table[$tableCount]->attributes->attribute[$attCound]['mask']= '';
									$attCound++;
								}
								$attCound= 0;
								$tableCount++;
							}
							$tableCount= 0;
							$scCount++;
						}
						$scCount= 0;
						$dbCount++;
					}
					$dbCount= 0;
					//  XML
					file_put_contents($this->tmpXMLFile, $this->xml->asXML());
					
					/*foreach($this->dataBases as $db)
					{
					}*/
					
					//$this->generateQuery();
					//$this->generateXML();
				}
			}
			$projectConfigDirectory= $this->configDirectory;
			$jsonFile= '../'.$projectConfigDirectory.'json_data.php';
			//if(!file_exists($jsonFile))
			fopen($jsonFile, 'w+');
			//fclose($jsonFileObj);
			//chmod ($jsonFile, 0777);
			file_put_contents($jsonFile, json_encode($this));
			$this->xml= $this->xml->asXML();//(string)$this->xml;
		}
		public function applySqlStyle($sql, $el, $show= true)
		{
			if($show)
				$sql= str_replace('</'.$el.'>', '</span>', str_replace('<'.$el.'>', "<span class='".$el."SQLElement'>", $sql));
			else
				$sql= str_replace('</'.$el.'>', '', str_replace('<'.$el.'>', '', $sql));
			return $sql;
		}
		public function showQuery($db, $b= false)
		{
			$qrToShow= $this->dataBases[$db]->showQuery($b);
			//echo htmlentities($qrToShow);
			$xmlTmp= simplexml_load_string($this->xml);
			/*print_r($xmlTmp);
			echo '<hr>';*/
			//$xml= unserialize($this->xml);
			$dbCount= 0;
			foreach($this->dataBases as $db)
			{
				$schemaCount= 0;
				foreach($db->schemas as $schema)
				{
					$tableCount= 0;
					foreach($schema->tables as $table)
					{
						$xmlTable= $xmlTmp->databases->database[$dbCount]->schemas->schema[$schemaCount]->tables->table[$tableCount];
						//echo $table->getQuery().'<hr>';
						$qrToUse= $this->applySqlStyle($table->getQuery(), 'constructor', false);
						$qrToUse= $this->applySqlStyle($qrToUse, 'mindComment', false);
						$qrToUse= $this->applySqlStyle($qrToUse, 'obj', false);
						$qrToUse= $this->applySqlStyle($qrToUse, 'element', false);
						$xmlTable['sql']= utf8_encode($qrToUse);
						$attCount= 0;
						foreach($table->attributes as $attribute)
						{
							$qrToUse= $attribute->query;
							$qrToUse= $this->applySqlStyle($qrToUse, 'constructor', false);
							$qrToUse= $this->applySqlStyle($qrToUse, 'mindComment', false);
							$qrToUse= $this->applySqlStyle($qrToUse, 'obj', false);
							$qrToUse= $this->applySqlStyle($qrToUse, 'element', false);
							$xmlTable->attributes->attribute[$attCount]['query']= utf8_encode($qrToUse);
							//echo '<br>';
							$xmlTable;
							$attCount++;
						}
						$tableCount++;
					}
					$schemaCount++;
				}
				$dbCount++;
			}
			file_put_contents($this->tmpXMLFile, $xmlTmp->asXML());
			//echo $qrToShow;
			//echo ($b)? 1: 0;
			
			$this->query= $qrToShow;
			$qrToShow= $this->applySqlStyle($qrToShow, 'constructor', $b);
			$qrToShow= $this->applySqlStyle($qrToShow, 'mindComment', $b);
			$qrToShow= $this->applySqlStyle($qrToShow, 'obj', $b);
			$qrToShow= $this->applySqlStyle($qrToShow, 'element', $b);
			return utf8_encode($qrToShow);
		}
		public function showXML($db, $b=false)
		{
			if($b)
			{
				return "<iframe id='showXMLIframe' src='".$this->tmpXMLFile."' style='width: 100%; height: 100%; border: none; display: inline;' frameborder='0'></iframe>";
			}
			else
				return file_get_contents($this->tmpXMLFile);
		}
		public function addDataBase($dbName= "DataBase's Name", $schemaName, $address, $rootUser, $rootUserPwd, $user, $userPwd, $SGBD, $description= '')
		{
			GLOBAL $l;
			$dbName= trim($this->fixChars($dbName, $l['charsToFix'], $l['fixedChars']));
			$this->dataBases[$dbName]= new DataBase(((trim($SGBD) != '')? $SGBD: $this->defaultDataBase));
			$this->dataBases[$dbName]->name= $dbName;
			$this->dataBases[$dbName]->dbAddress= $address;
			$this->dataBases[$dbName]->rootUser= $rootUser;
			$this->dataBases[$dbName]->rootUserPwd= $rootUserPwd;
			$this->dataBases[$dbName]->user= $user;
			$this->dataBases[$dbName]->userPwd= $userPwd;
			$this->dataBases[$dbName]->schemas[$schemaName]->description= $description;
			$this->dataBases[$dbName]->addSchema($schemaName);
		}
		public function generateStructure($project)
		{
			if($project)
			{
				$ret= '';
				foreach($project->dataBases as $obj)
				{
					$ret.= "<ul class='database' type='none'>
							<li>
								<span style='cursor: pointer;'
									  onclick='var tmp= this.parentNode.getElementsByTagName(\"UL\");
											   for(i=0; i<tmp.length; i++)
											   {
													if(tmp[i].style.display == \"none\")
														tmp[i].style.display = \"\";
													else
														tmp[i].style.display= \"none\";
											   }'>".$obj->name.'</span>';
					foreach($obj->schemas as $schema)
					{
						$ret.= "<ul class='schema' type='none'><li>";
						$ret.= "<span style='cursor: pointer;'
									onclick='var tmp= this.parentNode.getElementsByTagName(\"UL\");
											for(i=0; i<tmp.length; i++)
											{
												if(tmp[i].style.display == \"none\")
													tmp[i].style.display = \"\";
												else
													tmp[i].style.display= \"none\";
											}'>".$schema->name."</span>";
						foreach($schema->tables as $table)
						{
							$ret.= "<ul class='table' type='none'>
									<li>";
									$ret.= "<span style='cursor: pointer;'
												onclick='   var tmp= this.parentNode.getElementsByTagName(\"UL\");
															for(i=0; i<tmp.length; i++)
															{
																if(tmp[i].style.display == \"none\")
																	tmp[i].style.display = \"\";
																else
																	tmp[i].style.display= \"none\";
															}'>".$table->tableName."</span>";
									foreach($table->attributes as $attribute)
									{
										if($table->primaryKeys[$attribute->attName])
											$className= 'primaryKey';
										else
											$className= 'attribute';
										$ret.= "<ul class='".$className."' style='display: none;' type='none'>";
										$ret.= '<li style="cursor: pointer;">'.$attribute->attName.'</li>';
										$ret.= "</ul>";
									}
							$ret.= '	</li>
								</ul>';
						}
						$ret.= '</li></ul>';
					}
					$ret.= '</li></ul>';
				}
			}else{
					$ret= 'No structure to show now';
				 }
			return $ret;
		}
		public function generateERDiagram()
		{
			$derContent= file_get_contents('der_tool.php');
			return $derContent;//"<iframe src='der.php' style='display: block; width: 100%; height: 100%;'></iframe>";
		}
		public function generateDataDictionary($project)
		{
			$ret= '';
			foreach($project->dataBases as $obj)
			{
				$header.= '<h1>'.$obj->name.'</h1>';
				$header.= '<p style="padding-left: 25px;">Schemas: '.count($obj->schemas).' ';
				$header.= '(';
				$countTmp=0;
				foreach($obj->schemas as $schema)
				{
					$header.= (($countTmp==0)? '': (($countTmp==count($obj->schemas)-1)? ' and ': '---')).$schema->name;
					$countTmp++;
				}
				$header.= ')';
				$header.= '</p>';
				
				foreach($obj->schemas as $schema)
				{
					$ret.= '<div style="padding-left: 25px;"><h2>'.$schema->name.'</h2>';
					foreach($schema->tables as $table)
					{
						$ret.= '<div style="padding-left: 25px;
									 margin-bottom: 15px;">
									<table style="width: 90%;"
										   cellpadding="0"
										   cellspacing="0">
										<tr>
											<td>
												<span style="padding-left: 7px;
															 padding-right: 7px;
															 margin-left: 7px;
															 font-weight: bold;
															 text-align:center;
															 background-color: #d0d0d0;
															 cursor: default;
															 border: solid 1px #000;">'.$table->tableName.'
											</td>
										</tr>
										<tr>
											<td style="background-color: #d0d0d0;
													   border: solid 1px #000;
													   cursor: default;">
												<table style="width: 100%;"
													   cellpadding="0"
													   cellspacing="0">
													<tr style="border-bottom: solid 1px #fff">
														<td style="border-right: solid 1px #fff">
															<br>
														</td>
														<td style="text-align: center; font-weight: bold; border-right: solid 1px #fff">
															Type
														</td>
														<td style="text-align: center; font-weight: bold; border-right: solid 1px #fff">
															Size
														</td>
														<td style="text-align: center; font-weight: bold; border-right: solid 1px #fff">
															Default
														</td>
														<td style="text-align: center; font-weight: bold; border-right: solid 1px #fff">
															Null
														</td>
														<td style="text-align: center; font-weight: bold; border-right: solid 1px #fff">
															References
														</td>
													</tr>
													';
						foreach($table->attributes as $attribute)
						{
							$ret.= "<tr>";
								if($table->foreignKeys[$attribute->attName])
									$fk= $table->foreignKeys[$attribute->attName]->target;
								else
									$fk= false;
								$attribute->size= strip_tags(nl2br($attribute->size));
								$ret.= "<td style='border-right: solid 1px #fff; height: 10px;'>".trim((($table->primaryKeys[$attribute->attName])? '*': (($fk)? '+': '&nbsp;&nbsp;')).$attribute->attName).'</td>';
								$ret.= "<td style='border-right: solid 1px #fff'>".trim($attribute->attType).'</td>';
								$ret.= "<td style='border-right: solid 1px #fff; text-align: center;'>".trim($attribute->size).'</td>';
								$ret.= "<td style='border-right: solid 1px #fff'>".trim((($attribute->attDefault)? (utf8_encode($attribute->attDefault)): 'NULL')).'</td>';
								$ret.= "<td style='border-right: solid 1px #fff; text-align: center;'>".trim((($attribute->nullAccepts)? 'NULL': 'NOT NULL')).'</td>';
								$ret.= "<td style='text-align: center;'>".(($fk)? $fk: '').'<br></td>';
							$ret.= "</tr>";
						}
						$ret.='					</table>
											</td>
										</tr>
									</table>';
						$ret.= '</div>';
					}
					$ret.= '</div>';
				}
				$ret.= '';
			}
			$header.= "";
			return $header.$ret;
		}
		public function __construct($n, $dir)
		{
			$this->projectName= $n;
			$this->configDirectory= $dir;
		}
	}
?>