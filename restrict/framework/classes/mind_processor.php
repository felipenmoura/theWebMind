<?php
/*
	CLass: MindProcessor 1.0
	Definition: class used to interpret and generate wml code
	Author: Felipe Nascimento
	
	Observations:
		* tables without relations (neither pointed by nor pointing to other tables) will not exist
*/
	class MindProcessor
	{
		public $tables= Array();
		public $relations= Array();
		public $DDL;
		public $processedWML;
		public $sentences;
		public $subTypes= Array();
		public $theWebMindLanguageTempTable= 'theWebMindLanguageTempTable';
		public $types= Array('char', 'varchar', 'text', 'password', 'file', 'smallint', 'int', 'bigint', 'real', 'time'); // supported types
		public $verbId= ' ||"||MIND_VERB||"|| ';
		public $required= ' ||"||MIND_REQUIRED||"|| ';
		public $unique= ' ||"||MIND_UNIQUE||"|| ';
		public $quantifierId= ' ||"||MIND_QUANTIFIER_DIVISION||"|| ';
		public $quantifiers= Array('min'=> Array('min'=>' ||"||MIND_QUANTIFY_MIN_MIN||"|| ',
												  'max'=>' ||"||MIND_QUANTIFY_MIN_MAX||"|| '),
									'max'=> Array('min'=>' ||"||MIND_QUANTIFY_MAX_MIN||"|| ',
												  'max'=>' ||"||MIND_QUANTIFY_MAX_MAX||"|| '));
		
		public function __construct($project) // obj of project
		{
			GLOBAL $_MIND;
			$langDir= $_MIND['rootDir'].$_MIND['languageDir'].'/';
			
			include($langDir.'language.php');
			
			$lang= new Language($project->lang);
			
			include('table.php');
			include('relation.php');
			
			$this->especialChars= $lang->especialChars;
			$this->fixedChars= $lang->fixedChars;
			
			// identifying structures / subTypess
			$regExp= '\$\w[a-z0-9_]+:\w.+\)';
			$this->sentences= preg_match_all('/'.$regExp.'/i', $project->wml, $matches);
			if(sizeof($matches[0]) > 0)
			{
				$this->tables[$this->theWebMindLanguageTempTable]= new Table($this->theWebMindLanguageTempTable);
				
				$matches= $matches[0];
				for($i=0; $i<sizeof($matches); $i++)
				{
					$subTypesName= trim(substr($matches[$i], 1, strpos($matches[$i], ':')-1));
					$subTypeDef= trim(substr($matches[$i], strpos($matches[$i], ':')+1));
					$this->subTypes[$subTypesName]= $subTypeDef;
					$project->wml= preg_replace('/'.$regExp.'/', '', $project->wml);
					$regExp= ':'.$subTypesName.'\(\)';
					$project->wml= preg_replace('/'.$regExp.'/', ':'.$subTypeDef, $project->wml);
				}
			}
			
			//	parsing by enters
			$this->processedWML= preg_replace('/\n/', '_TMP_NEW_LINE_MIND_REGEXP_MATCH_', $project->wml);
			$this->processedWML= preg_replace('/\/\*.+?\*\//', '', $this->processedWML);
			$this->processedWML= preg_replace('/_TMP_NEW_LINE_MIND_REGEXP_MATCH_/', '
', $this->processedWML);
			$this->sentences= preg_split('/
/', $this->processedWML);
			
			// identify the verbs
			$regExp= trim(implode(' | ', $lang->verbs));
			$this->sentences= preg_replace('/'.$regExp.'/i', $this->verbId, $this->sentences, 1);
			// identify the required fields
			$regExp= trim(implode('|', $lang->required));
			$this->sentences= preg_replace('/'.$regExp.'/i', $this->required, $this->sentences);
			// identify the unique fields
			$regExp= trim(implode('|', $lang->unique));
			$this->sentences= preg_replace('/'.$regExp.'/i', $this->unique, $this->sentences);
			// applying fieldtypes
			reset($this->types);
			while($cur= current($this->types))
			{
				if(is_array($lang->types[$cur]))
				{
					$regExp= trim(implode('|', $lang->types[$cur]));
					$regExp= str_replace('|', '\(|:', $regExp);
					$this->sentences= preg_replace('/:'.$regExp.'/i', ':'.$cur.'(', $this->sentences);
				}
				next($this->types);
			}
			//reset($this->subTypes);
			/*while($cur= current($this->subTypes))
			{
				echo $cur->name;
				/*
				if(isset($this->subTypes[]))
				{
					$regExp= trim(implode('|', $this->subTypes[$cur]));
					$regExp= str_replace('|', '\(|:', $regExp);
					$this->sentences= preg_replace('/:'.$regExp.'/i', ':'.$cur.'(', $this->sentences);
				}
				* /
				next($this->subTypes);
			}
			*/
			// identify the divisors of quantities fields
			$regExp= trim(implode('|', $lang->quantifiersId));
			$this->sentences= preg_replace('/'.$regExp.'/i', $this->quantifierId, $this->sentences, 0);
			
			$this->tmpSentences= $this->sentences;
			$s= sizeof($this->tmpSentences);
			$this->sentences= false;
			$this->sentences= Array();
			$regExpQmm= (trim(implode(' |', $lang->quantifiers['min']['min'])));
			$regExpQmM= (trim(implode(' |', $lang->quantifiers['min']['max'])));
			$regExpQMm= (trim(implode(' |', $lang->quantifiers['max']['min'])));
			$regExpQMM= (trim(implode(' |', $lang->quantifiers['max']['max'])));
			$regExpQ= trim(implode(' | ', $lang->quantifiersId));
			
			for($i=0; $i<$s; $i++)
			{
				if(trim($this->tmpSentences[$i]) != '' && preg_match('/'.$this->verbId.'/', $this->tmpSentences[$i]))
				{
					$curExpression= explode($this->verbId, $this->tmpSentences[$i]);
					$curExpression[0]= trim($curExpression[0]);
					$delimiter= strrpos(trim($curExpression[0]), ' ');
					if(!$delimiter) $delimiter= 0;
					$curExpression[0]= trim(substr($curExpression[0], $delimiter, strlen($curExpression[0])-$delimiter)); // got the left entity
					// defining if it has an attribute or another entity
					$regExp= implode('|', $this->types);
					$regExp= str_replace('|', '\(|', $regExp);
					if(!isset($curExpression[1]))
						continue;
					if(preg_match('/:'.$regExp.'/', $curExpression[1], $matches))
					{	// it is an attribute
						$matches= $matches[0];
					}else{
							$curExpression[1]= preg_replace('/'.$regExpQ.'/i', $this->quantifierId, $curExpression[1], 1);
							if(strstr($curExpression[1], $this->quantifierId))
							{
								$curExpression[1]= preg_replace('/'.$regExpQmm.'/i', $this->quantifiers['min']['min'], $curExpression[1], 1);
								$curExpression[1]= preg_replace('/'.$regExpQmM.'/i', $this->quantifiers['min']['max'], $curExpression[1], 1);
							}else{
									$curExpression[1]= $this->quantifiers['min']['min'].$this->quantifierId.$curExpression[1];
								 }
							$curExpression[1]= preg_replace('/'.$regExpQMm.'/i', $this->quantifiers['max']['min'], $curExpression[1], 1);
							$curExpression[1]= preg_replace('/'.$regExpQMM.'/i', $this->quantifiers['max']['max'], $curExpression[1], 1);
							$rightEntity= explode($this->quantifiers['max']['min'], $curExpression[1]);
							if(sizeof($rightEntity)!=2)
							{
								$glue= $this->quantifiers['max']['max'];
								if(!strpos($curExpression[1], $glue))
								{
									$curExpression[1]= str_replace(trim($this->quantifierId), $this->quantifierId.$glue, $curExpression[1]);
								}
								$rightEntity= explode($glue, $curExpression[1]);
							}else
								$glue= $this->quantifiers['max']['min'];
							$end= ($end= strpos(trim($rightEntity[1]), ' '))? $end: trim(strlen($rightEntity[1]));
							$rightEntity[1]= preg_replace('/\.$/', '', trim(substr($rightEntity[1], 0, $end)));
							$curExpression[1]= implode($glue, $rightEntity);
						 }
					$curExpression= implode($this->verbId, $curExpression);
					$this->sentences[]= $curExpression;
					
					$this->execExpression($curExpression);
				}
			}
			
			reset($this->relations);
			
			$rels= Array(); // final array of valid relations
			$tables= Array();
			while($rel= current($this->relations))
			{
				if(isset($this->relations[$rel->rightTable.'|'.$rel->leftTable]) && $rel->max== 'n')
				{
						$tmpTable= $rel->leftTable.'_'.$rel->rightTable;
						$tmpRelation= $rel->leftTable.'|'.$tmpTable;
						$rels[$tmpRelation]= new Relation($tmpRelation);
						$rels[$tmpRelation]->min= 0;
						$rels[$tmpRelation]->max= 'n';
						$this->tables[$rel->leftTable]->refered[]= $tmpRelation;
						$this->tables[$rel->leftTable]->weight++;
						
						$tmpRelation= $rel->rightTable.'|'.$tmpTable;
						$rels[$tmpRelation]= new Relation($tmpRelation);
						$rels[$tmpRelation]->min= 0;
						$rels[$tmpRelation]->max= 'n';
						
						$this->tables[$rel->rightTable]->refered[]= $tmpRelation;
						$this->tables[$rel->rightTable]->weight++;
						
						if(!isset($tables[$rel->leftTable]))
							$tables[$rel->leftTable]= $this->tables[$rel->leftTable];
						if(!isset($tables[$rel->rightTable]))
							$tables[$rel->rightTable]= $this->tables[$rel->rightTable];
						if(!isset($tables[$tmpTable]))
							$tables[$tmpTable]= new Table($tmpTable);
						
						$tables[$tmpTable]->addForeignKey($rel->leftTable);
						$tables[$tmpTable]->addForeignKey($rel->rightTable);
						
						$tables[$rel->rightTable]->removeForeignKey($rel->leftTable);
						$tables[$rel->leftTable]->removeForeignKey($rel->rightTable);
						
						unset($this->relations[$rel->rightTable.'|'.$rel->leftTable]);
						//unset($this->relations[$rel->rightTable.'|'.$rel->rightTable]);
				}else{	// n/1 or 1/n or 1/1 => OK
						$rels[$rel->name]= $rel;
						
						if(!isset($tables[$rel->leftTable]))
							$tables[$rel->leftTable]= $this->tables[$rel->leftTable];
						if(!isset($tables[$rel->rightTable]))
							$tables[$rel->rightTable]= $this->tables[$rel->rightTable];
						$tables[$rel->leftTable]->weight++;
						$tables[$rel->leftTable]->refered[]= $rel->name;
						$tables[$rel->rightTable]->addForeignKey($rel->leftTable);
					 }
				//$tables[$rel->rightTable]->addForeignKey($rel->leftTable);
				next($this->relations);
			}
			
			$this->tables= $tables;
			$this->relations= $rels;
			
			
			reset($this->relations);
			while($rel= current($this->relations))
			{
				if(isset($this->relations[$rel->rightTable.'|'.$rel->leftTable]))
				{
					if($rel->max == '1') // 1/1
					{
						$evidence= &$this->tables[(($this->tables[$rel->leftTable]->weight > $this->tables[$rel->rightTable]->weight)? $rel->leftTable: $rel->rightTable)];
						$other= &$this->tables[(($evidence->name == $rel->leftTable)? $rel->rightTable: $rel->leftTable)];
						
						foreach($other->attributes as $att)
						{
							$att->name= $other->name.'_'.$att->name;
							$evidence->attributes[$att->name]= $att;
						}
						// remove the relations between the evidenced table and the table that will be removed
						$other->refered= preg_replace('/'.$other->name.'/', $evidence->name, $other->refered);
						$evidence->refered= array_merge($evidence->refered, $other->refered);
						
						$evidence->removeReference($rel->name);
						$tmpRel= explode('|', $rel->name);
						$tmpRel= $tmpRel[1].'|'.$tmpRel[1];
						$evidence->removeReference($tmpRel);
						$evidence->weight= sizeof($evidence->refered);
						
						$evidence->foreignKeys= array_merge($evidence->foreignKeys, $other->foreignKeys);
						$evidence->removeForeignKey($evidence->name);
						$evidence->removeForeignKey($other->name);
						$other->removeReference($evidence->name.'|'.$evidence->name);
						
						// fixing the tables pointed by the table that no longer exist
						foreach($other->foreignKeys as $fk)
						{
							$this->tables[$fk[1]]->refered= preg_replace('/'.$other->name.'/', $evidence->name, $this->tables[$fk[1]]->refered);
							$this->tables[$fk[1]]->foreignKeys= preg_replace('/'.$other->name.'/', $evidence->name, $this->tables[$fk[1]]->foreignKeys);
							$this->tables[$fk[1]]->removeReference($fk[1].'|'.$other->name);
						}
						
						// fixing the tables that were pointing to the table that no longer exist
						foreach($other->refered as $ref)
						{
							$ref= explode('|', $ref);
							$ref= $ref[1];
							
							if(is_array($this->tables[$ref]->foreignKeys))
							{
								$this->tables[$ref]->foreignKeys[0]= preg_replace('/'.$other->name.'/', $evidence->name, $this->tables[$ref]->foreignKeys[0]);
							}else{
									$this->tables[$ref]->foreignKeys= preg_replace('/'.$other->name.'/', $evidence->name, $this->tables[$ref]->foreignKeys);
								 }
							
						}
						
						
						// removing the table other, and the relations 1/1
						unset($this->tables[$other->name]);
						unset($this->relations[$rel->leftTable.'|'.$rel->rightTable]);
						unset($this->relations[$rel->rightTable.'|'.$rel->leftTable]);
					}else{
							//$tables[$rel->rightTable]->addForeignKey($rel->leftTable);
						 }
				}
				next($this->relations);
			}
			
			
			reset($this->tables);
			while($tab= current($this->tables))
			{
				$att= new Attribute($_MIND['primaryKeyPrefix'].$tab->name);
				$att->name= $_MIND['primaryKeyPrefix'].$tab->name;
				$att->type= 'integer';
				$att->size= '0';
				$att->required= 1;
				$att->references= '';
				$tmpAtt= $this->tables[$tab->name]->attributes;
				$att= $this->tables[$tab->name]->addAttribute($att);
				$this->tables[$tab->name]->attributes= array_merge(Array($att->name=>$att), $tmpAtt);
				
				next($this->tables);
			}
			// formating in WebMindLanguage (wml format)
			$this->verbId				= trim(str_replace('|', '\|', $this->verbId));
			$this->required				= trim(str_replace('|', '\|', $this->required));
			$this->quantifierId			= trim(str_replace('|', '\|', $this->quantifierId));
			$this->quantifiers['min']	= preg_replace('/\|/', '\|', $this->quantifiers['min']);
			$this->quantifiers['max']	= preg_replace('/\|/', '\|', $this->quantifiers['max']);
			
			$this->sentences= preg_replace('/'.$this->verbId.' \|/', '-> |', $this->sentences);
			$this->sentences= preg_replace('/'.$this->verbId.'/', '<- ', $this->sentences);
			$this->sentences= preg_replace('/'.$this->quantifierId.'/', ',', $this->sentences);
			$this->sentences= preg_replace('/'.trim($this->quantifiers['min']['min']).'/', '{0', $this->sentences);
			$this->sentences= preg_replace('/'.trim($this->quantifiers['min']['max']).'/', '{1', $this->sentences);
			$this->sentences= preg_replace('/'.trim($this->quantifiers['max']['min']).'/', '1}', $this->sentences);
			$this->sentences= preg_replace('/'.trim($this->quantifiers['max']['max']).'/', '*}', $this->sentences);
			$this->sentences= preg_replace('/'.trim($this->required).'/', 'not null', $this->sentences);
			$this->sentences= preg_replace('/,( *)/', ',', $this->sentences);
			
			$project->wml= trim(implode('
', $this->sentences));
			$this->processedWML= '';
			$this->tmpSentences= '';
		}
		
		public function showTables()
		{
			echo '<pre>';
			foreach($this->tables as $table)
			{
				echo $table->name.'
';
				if(is_array($table->attributes))
					foreach($table->attributes as $att)
					{
						echo '   '.$att->name.'
';
					}
				// else return error on the code;
			}
			echo '</pre>';
		}
		
		public function filter($text)
		{
			$size= sizeof($this->especialChars);
			for($i=0; $i<$size; $i++)
			{
				$text= (str_replace($this->especialChars[$i], $this->fixedChars[$i], $text));
			}
			return strtolower($text);
		}
		private function proccessExpression($mindExp)
		{
		}
		private function execExpression($mindExp)
		{
			GLOBAL $_MIND;
			if(strpos($mindExp, $this->quantifierId)) // it means it is a table relation
			{
				$mindExp= explode(' ', trim($mindExp));
				$leftTable= $this->filter(trim($mindExp[0]));
				$rightTable= $this->filter(trim($mindExp[sizeof($mindExp)-1]));
				if(!isset($this->tables[$leftTable]))
				{
					$this->tables[$leftTable]= new Table($leftTable);
				}
				if(!isset($this->tables[$rightTable]))
				{
					$this->tables[$rightTable]= new Table($rightTable);
				}
				$min= (in_array(trim($this->quantifiers['min']['min']), $mindExp))? '0': '1';
				$max= (in_array(trim($this->quantifiers['max']['min']), $mindExp))? '1': 'n';
				$relName= ($max=='n')? $leftTable.'|'.$rightTable: $rightTable.'|'.$leftTable;
				$this->relations[$relName]= new Relation($relName);
				$this->relations[$relName]->min= $min;
				$this->relations[$relName]->max= $max;
			}else{	// attributes
					$mindExp= explode(trim($this->verbId), $mindExp);
					$leftTable= $this->filter(trim($mindExp[0]));
					if(!isset($this->tables[$leftTable]))
					{
						$this->tables[$leftTable]= new Table($leftTable);
					}
					
					$mindExp= explode(':', $mindExp[1], 2);
					$attName= explode(' ', trim($mindExp[0]));
					$attName= $attName[sizeof($attName)-1];
					$mindExp= explode('(', $mindExp[1], 2);
					$att= new Attribute($this->filter($attName));
					
					if(strpos($mindExp[1], trim($this->required)))
					{
						$mindExp[1]= str_replace(trim($this->required), '', $mindExp[1]);
						$att->required= 1;
					}else{
							$att->required= 0;
						 }
					if(strpos($mindExp[1], trim($this->unique)))
					{
						$mindExp[1]= str_replace(trim($this->unique), '', $mindExp[1]);
						$att->unique= 1;
					}else{
							$att->unique= 0;
						 }
						 
					$att->type= trim($mindExp[0]);
					
					preg_match('/([^(\\\)])?"(.*)[^(\\\)]"/', $mindExp[1], $matches); // get the default value for attribute ( between "" )
					if(sizeof($matches)>0)
					{
						$att->defaultValue= preg_replace('/^("|.")|"$/', '', trim($matches[0]));
						$mindExp[1]= str_replace($matches[0], '', $mindExp[1]);
					}
					preg_match('/[^(\\\)]\[(.*)[^(\\\)]\]/', $mindExp[1], $matches); // get the mask for attribute ( betwee [] )
					if(sizeof($matches)>0)
					{
						$mindExp[1]= str_replace($matches[0], '', $mindExp[1]);
						$att->mask= preg_replace('/^(\[|.\[)|\]$/', '', trim($matches[0]));
					}
					preg_match('/\/\/.*$/', $mindExp[1], $matches);
					if(sizeof($matches)>0)
					{
						$matches[0]= preg_replace('/\/\//', '', trim($matches[0]));
						$att->comment= $matches[0];
					}
					
					preg_match('/[^(\\\)]\{(.*)[^(\\\)]\}/', $mindExp[1], $matches); // get the options for attribute ( between {} )
					if(sizeof($matches)>0)
					{
						$matches[0]= preg_replace('/^(\{|.\{)|\}$/', '', trim($matches[0]));
						$mindExp[1]= str_replace($matches[0], '', $mindExp[1]);
					
						$matches= preg_split('/(\|)/', $matches[0]); // parses the options, separated by |, using = to especify any value/label
						$att->options= $matches;
						for($i=0; $i<sizeof($att->options);$i++)
						{
							$att->options[$i]= explode('=', $att->options[$i], 2);
						}
					}
					$att->size= (real)$mindExp[1];
					$this->tables[$leftTable]->addAttribute($att);
				 }
		}
	}
?>