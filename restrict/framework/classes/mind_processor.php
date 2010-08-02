<?php
	/**
	 *	CLass: MindProcessor 1.0
	 *	Class used to interpret and generate wml code
	 *  @package processor
	 *  @author Felipe Nascimento
	 *	
	 *	Observations:
	 *		* tables without relations (neither pointed by nor pointing to other tables) will not exist
	*/
	class MindProcessor
	{
		// defining properties
		public  $tables						= Array();
		public  $relations					= Array();
		public  $DDL;
		public  $processedWML;
		public  $currentOptions				= null;
		private $currentLineNumber;
		private $currentLine;
		public  $sentences;
		public  $messages					= Array();
		public  $warnings					= 0;
		public  $errors						= 0;
		public  $globalAttributeList		= Array();
		public  $status						= 1;
		public  $synonymous					= Array();
		public  $debug						= Array();
		public  $subTypes					= Array();
		// this table will be created on the memory, but will be discarted in the end of the process
		public  $theWebMindLanguageTempTable= 'theWebMindLanguageTempTable';
		// supported types
		public  $types						= Array('char',
													'varchar',
													'text',
													'password',
													'file',
													'smallint',
													'int',
													'bigint',
													'real',
													'time');
		// keywords. Mind will match then to force a pattern after dealing with the choosen language
		public  $verbId						= ' ||"||MIND_VERB||"|| ';
		public  $required					= ' ||"||MIND_REQUIRED||"|| ';
		public  $unique						= ' ||"||MIND_UNIQUE||"|| ';
		public  $belongsId					= ' ||"||EXTENDS||"|| ';
		public  $obligationId				= ' ||"||OBLIGATION||"|| ';
		public  $quantifierId				= ' ||"||MIND_QUANTIFIER_DIVISION||"|| ';
		public  $quantifiers				= Array('min'=> Array('min'=>' ||"||MIND_QUANTIFY_MIN_MIN||"|| ',
																  'max'=>' ||"||MIND_QUANTIFY_MIN_MAX||"|| '),
													'max'=> Array('min'=>' ||"||MIND_QUANTIFY_MAX_MIN||"|| ',
																  'max'=>' ||"||MIND_QUANTIFY_MAX_MAX||"|| '));
		/**
		* This method receives the text and parses it into an Array with all the expressions, separeted by "." or ";"
		* @author Felipe Nascimento
		* @name parse
		* @param String $str
		* @return numeric Array
		*/
		private function parse($str)
		{
			$expressions= Array(); // will have all the expressions to be interpreted
			$sep= Array('.', ';'); // expression separator
			$str= preg_split('//', trim($str)); // separing comments
			$str[]= $sep[0]; // this will fix well the expressiona had a comment without a . in the end
			$inside= false;  // indicates whether the iterator is between ""
			$comment= false;
			$allow= false;	 // this var indicates if the iterator is between {} or []
			$insideAtt= false; // inside ( ) ??
			$currentExpression= '';
			
			// running through the string, letter by letter
			for($i=0, $j= sizeof($str); $i<$j; $i++)
			{
				$letter= $str[$i];
				// first, we gotta ignore the comment blocks
				if($letter == '/')
				{
					if(!$comment)
					{
						if(isset($str[$i+1]) && $str[$i+1] == '*')
						{
							$comment= true;
						}
					}else{
							if($i>0 && $str[$i-1] == '*')
							{
								$comment= false;
								continue;
							}
						 }
				}
				// if it is inside a comment block, we skip to the next letter
				if($comment)
					continue;
				
				// checking if it is scaping the next charachter, if so, we must add this and skip
				if($i>0 && $str[$i-1] == '\\')
				{
					$currentExpression.= $str[$i];
					continue;
				}
				// checks whether it is opening a mask or option and if it is NOT inside the default value identification
				if(!$inside && $letter == '[' || $letter == '{')
				{
					$allow= true;
				}
				if(!$inside && $letter == ']' || $letter == '}')
				{
					$allow= false;
				}
				// checking if it is inside the attribute properties
				if(!$inside && !$allow && $letter == '(')
					$insideAtt= true;
				if(!$inside && !$allow && $letter == ')')
					$insideAtt= false;
					
				// if the current letter identifies an end of command and it is neither inside a string
				// not a mask/option identifier
				if(!$allow && !$inside && !$insideAtt && in_array($letter, $sep))
				{
					// add the current expression to the list of expressions
					$expressions[]= trim(stripslashes($currentExpression));
					$currentExpression= '';
				}else{
						if($letter == '"')
							$inside= !$inside;
						$currentExpression.= $letter;
					 }
			}
			return $expressions;
		}
		
		
		/**
		* This method simply prepares the output messages to log
		* @author Felipe Nascimento
		* @name log
		* @param Int $level
		* @param String $msg
		* @return void
		*/
		private function log($level, $msg)
		{
			$this->currentOptions;
			if($level=='2' && $this->currentOptions['reportDecisions'] !='on')
			{
				return false;
			}
			if($level=='3' && $this->currentOptions['reportDoubts'] !='on')
			{
				return false;
			}
			$this->messages[]= Array($level, $msg);
		}
		
		
		/**
		* Here, if the Mind Universe is enabled, it will update it with its new knowledge
		* @author Felipe Nascimento
		* @name updateUniverse
		* @return void
		*/
		private function updateUniverse()
		{
			GLOBAL $_MIND;
			// let's check if Mind Universe is enabled
			if(!isset($this->currentOptions['enableMindUniverse']) || $this->currentOptions['enableMindUniverse'] != 'on')
			{
				// if not, return here
				return;
			}
			// we'll run over all the entities to stabilish a knowledge base
			reset($this->tables);
			while($table= current($this->tables))
			{
				$wmkb= $_MIND['rootDir'].'mind-universe/'.$table->name.'.wmkb'; // WebMindKnowledgeBase
				
				if(!file_exists($wmkb))
				{
					// if this knowledge does not exist, then create it
					$f= fopen($wmkb, 'w+');
					fwrite($f, JSON_encode($table));
					fclose($f);
				}else{
						// otherwise, we oght to compare them
						$knowledge= file_get_contents($wmkb);
						if($knowledge != JSON_encode($table))
						{
							// if they are different, well... then we have to anaylise each attribute of this entity
							//similar_text
							//levenshtein
							
							//$this->log(4, "fiderentes!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
						}
					 }
				//$this->log(4, levenshtein("carrro", "caro"));
				next($this->tables);
			}
		}
		
		/**
		* This method has the responsability to understand (interpret) the code... this is the core
		* @author Felipe Nascimento
		* @name process
		* @param [Project Object]
		* @return MindProcessor Object
		*/
		private function process($project)
		{
			// again, we will use the global _MIND to have all the properties and methods as a singleton
			GLOBAL $_MIND;
			// lets log each step
			$this->log(4, "Starting...");
			
			// loading the language class, the interface and the selected idiom
			$langDir= $_MIND['rootDir'].$_MIND['languageDir'].'/';
			include($langDir.'language.php');
			$lang= new Language($project->lang);
			
			// two different classes we're gonna need here
			include('table.php');
			include('relation.php');
			
			$this->especialChars= $lang->especialChars;
			$this->fixedChars= $lang->fixedChars;
			$project->wml= preg_replace('/\r/', '', $project->wml);
			
			// identifying structures / subTypess
			// e.g.: $sex:char(1, {F=Female|M=Male}).
			$regExp= '\$\w[a-z0-9_]+:\w.+\)';
			$this->sentences= preg_match_all('/'.$regExp.'/i', $project->wml, $matches);
			
			// if there are subtypes, we will create a temp table to keep them
			// we have to log it, too, and then parse it, as it was a simple attribute
			if(sizeof($matches[0]) > 0)
			{
				$this->tables[$this->theWebMindLanguageTempTable]= new Table($this->theWebMindLanguageTempTable);
				
				$matches= $matches[0];
				$this->log(4, "Identifying subtypes...");
				
				// here, we will use all the identified subtypes, replacing with them in
				// the lines where they were being used 
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
			
			// here, we're about to identify the definitions, for synonymous
			// its rules are just like this: user=users, usuarios.
			$regExp= '\@\w+=[\w \,]+[\.|\;|$]';
			$x= preg_match_all('/'.$regExp.'/i', $project->wml, $matches);
			
			// if we got some matching pattern
			if(sizeof($matches[0]) > 0)
			{
				$this->log(4, "Treating synonymous...");
				$matches= $matches[0];
				for($i=0; $i<sizeof($matches); $i++)
				{
					$cur= explode('=', $matches[$i]);
					$cur[1]= explode(',', $cur[1]);
					$mainWord= $this->filter(substr($cur[0], 1));
					
					for($j=0, $k= sizeof($cur[1]); $j<$k; $j++)
					{
						// I know this is a confusing line. It cleans the name of any synonymous, to not fill our dictionary with trash.
						// Also, this is a faster way to use, afterwards. It's gonna be better for our performance.
						$this->synonymous[$this->filter(trim($cur[1][$j]))]= trim($mainWord);
					}
					// we have to avoid this expression, to not be interpreted afterwards
					$project->wml= str_replace($matches[$i], '', $project->wml);
				}
			}
			
			$this->log(4, "Analysing expressions...");
			// here, we gotta parse our prepared sentences
			// from now on, $this->sentences will be an array with each expression, already patternized
			$this->sentences= $this->parse($project->wml);
			// let's keep a copy of these sentences, before starting to change them
			$originalSentences= $this->sentences;
			
			// identify the "belong's
			$regExp= trim(implode(' | ', $lang->belongs));
			$this->sentences= preg_replace('/'.$regExp.'/i', $this->belongsId, $this->sentences, 1);
			// identify the "obligationId's
			$regExp= trim(implode(' | ', $lang->obligation));
			$this->sentences= preg_replace('/'.$regExp.'/i', $this->obligationId, $this->sentences, 1);
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
			$this->log(4, "Specifying attributes");
			while($cur= current($this->types))
			{
				if(isset($lang->types[$cur]))
					if(is_array($lang->types[$cur]))
					{
						// first of all, we have to prepare our regular expression
						$regExp= trim(implode('|', $lang->types[$cur]));
						$regExp= str_replace('|', '\(|:', $regExp);
						// and then, apply it to the sentences
						// the result will be a code following theWebMind's rules 
						$this->sentences= preg_replace('/:'.$regExp.'/i', ':'.$cur.'(', $this->sentences);
					}
				next($this->types);
			}
			
			// the same will be done here, to identify the quantifiers identificators (like "or", for example)
			$regExp= trim(implode('|', $lang->quantifiersId));
			$this->sentences= preg_replace('/'.$regExp.'/i', $this->quantifierId, $this->sentences, 0);
			$this->tmpSentences= $this->sentences;
			
			// now, we will use a copy of the sentences, and will clean the array used before
			$s= sizeof($this->tmpSentences);
			$this->sentences= false;
			$this->sentences= Array();

			// preparing quantifiers regular expressions 
			$regExpQmm= (trim(implode('|', $lang->quantifiers['min']['min'])));
			$regExpQmM= (trim(implode('|', $lang->quantifiers['min']['max'])));
			$regExpQMm= (trim(implode('|', $lang->quantifiers['max']['min'])));
			$regExpQMM= (trim(implode('|', $lang->quantifiers['max']['max'])));
			$regExpQ= trim(implode('|', $lang->quantifiersId));
			
			// let's iterate all the expressions
			// $s was used to "cache" the size during the loop
			$this->currentLine= 0;
			for($i=0; $i<$s; $i++)
			{
				$this->currentLineNumber++;
				$this->currentLine= $this->tmpSentences[$i];
				$continue= false;
				$extends= false;
				$abstract= false;
				
				// the verbs are the key... let's work with them, before continuing
				/*
				 * There are three main verb types
				 * the possessive, mandatory and relational 
				 * These possibilities are represented as VERB, BELONG and OBLIGATION and
				 * depending on them, Mind will decide how to deal with each relation
				 */
				if(trim($this->tmpSentences[$i]) != '' && preg_match('/'.preg_quote($this->verbId).'/', $this->tmpSentences[$i]))
				{
					$continue= true;
				}elseif(trim($this->tmpSentences[$i]) != '' && preg_match('/'.preg_quote($this->belongsId).'/', $this->tmpSentences[$i]))
					{
						$continue= true;
						$extends= true;
					}elseif(trim($this->tmpSentences[$i]) != '' && preg_match('/'.preg_quote($this->obligationId).'/', $this->tmpSentences[$i]))
						{
							$continue= true;
							$abstract= true;
						}else{
								if(trim($this->currentLine) != '')
								{
									$this->log(3, "Expression with no known verb:<img src='images/engine.gif' align='right' style='cursor:poniter;'".
												  "alt='Specify the verb manually' title='Specify the verb manually' onclick='Mind.Project.AddVerb(event, ".
												  "this);'/><br/><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"".
												  $this->currentLine."\"</i>");
									$this->warnings++;
								}
							 }
				// if a verb was found
				if($continue)
				{
					// we have to divide the expression by two using the verb
					$curExpression= explode($this->verbId, $this->tmpSentences[$i]);
					if(sizeOf($curExpression)==1)
						$curExpression= explode($this->belongsId, $this->tmpSentences[$i]);
					if(sizeOf($curExpression)==1)
						$curExpression= explode($this->obligationId, $this->tmpSentences[$i]);
					
					$curExpression[0]= trim($curExpression[0]);
					$delimiter= strrpos(trim($curExpression[0]), ' ');
					if(!$delimiter) $delimiter= 0;
					$curExpression[0]= trim(substr($curExpression[0], $delimiter, strlen($curExpression[0])-$delimiter)); // got the left entity
					
					/*
					 *  from now, the $curExpression has in the first half,
					 *  the left table, and in the second, the definition of
					 *  an attribute or another entity
					 */
					
					// if there is not a second half, it means the only matched verb was the last valid word
					// then, we will skip this expression
					if(!isset($curExpression[1]))
						continue;
					
					$regExp= implode('|', $this->types);
					$regExp= str_replace('|', '\(|', $regExp);	
					$ext= '';
					// is this and attribute or what?
					if(preg_match('/:'.$regExp.'/', $curExpression[1], $matches))
					{
						// this is an attribute
						$matches= $matches[0];
					}else{
							// no, this is "what"
							$ext= ($abstract)? $this->obligationId: (($extends)? $this->belongsId: '');
							$curExpression[1]= preg_replace('/ ('.$regExpQ.') /i', $this->quantifierId, $curExpression[1], 1);
							
							$curExpression[1]= preg_split('/'.preg_quote($this->quantifierId).'/', $curExpression[1]);
							
							if(sizeof($curExpression[1]) == 1)
							{
								// had not an OR statemant, then, the minimum quantifier is aplied
								$curExpression[1]= $this->quantifiers['min']['min'].$this->quantifierId.$curExpression[1][0];
							}else{
									$exp= '/^( +)?('.$regExpQmm.')/i';
									$curExpression[1][0]= preg_replace($exp, $this->quantifiers['min']['min'], $curExpression[1][0]);
									$exp= '/^( +)?('.$regExpQmM.')/i';
									$curExpression[1][0]= preg_replace($exp, $this->quantifiers['min']['max'], $curExpression[1][0], 1);
									
									$curExpression[1]= implode($this->quantifierId, $curExpression[1]);
								 }
							$exp= '/ ('.$regExpQMm.') /i';
							$curExpression[1]= preg_replace($exp, $this->quantifiers['max']['min'], $curExpression[1], 1);
							$exp= '/ ('.$regExpQMM.') /i';
							$curExpression[1]= preg_replace($exp, $this->quantifiers['max']['max'], $curExpression[1], 1);
						 }
					
					// let's use only the word next to the delimiter, as the right entity's name
					if(strpos($curExpression[1], $this->quantifierId)) // if it is not an attribute definition
					{
						if(strpos($curExpression[1], $this->quantifiers['max']['min']))
							$sharp= $this->quantifiers['max']['min'];
						elseif(strpos($curExpression[1], $this->quantifiers['max']['max']))
							{
								$sharp= $this->quantifiers['max']['max'];
							}else{
									$this->messages[]= Array(2, "No quantifier found in <i>\"".$originalSentences[$i]."\"</i><br/>I will assume a multiple relation(N) here");
									
									$curExpression[1]= str_replace($this->quantifierId, $this->quantifierId.$this->quantifiers['max']['max'], $curExpression[1]);
									$sharp= $this->quantifiers['max']['max'];
								 }
						$curExpression[1]	= explode($sharp, $curExpression[1]);
						$curExpression[1][1]= explode(' ', trim($curExpression[1][1]));
						$curExpression[1][1]= $curExpression[1][1][0];
						$curExpression[1]	= implode($sharp, $curExpression[1]);
					}
					
					$curExpression= implode($this->verbId.$ext, $curExpression);
					$this->sentences[]= $curExpression;
					$this->execExpression($curExpression);
				}
			}
			$this->log(4, "Treating cardinalities");
			reset($this->relations);
			
			// now, we gotta use only the valid tables and relations
			$rels= Array(); // this is going to be the final array of valid relations
			$tables= Array();
			
			while($rel= current($this->relations))
			{
				// here, we will check if already exists a relation between these entities, in opposite directions
				// if it does exist, AND the relation here is N, then this is a N/N relation between these entities
				if(isset($this->relations[$rel->rightTable.'|'.$rel->leftTable]) && $rel->max== 'n')
				{
						$tmpTable= $rel->leftTable.'_'.$rel->rightTable;
						$tmpRelation= $rel->leftTable.'|'.$tmpTable;
						$rels[$tmpRelation]= new Relation($tmpRelation);
						$rels[$tmpRelation]->min= 0;
						$rels[$tmpRelation]->max= 'n';
						$this->tables[$rel->leftTable]->refered[]= $tmpRelation;
						$this->tables[$rel->leftTable]->weight++;
						
						// we have to create a new table to link both entities
						// and also, two new relations
						
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
						else{
							}
							
						$this->log(2, "Relation n/n between \"".$rel->leftTable.
									  "\" and \"".$rel->rightTable.
									  "\". I have to create a new table, \"".$tmpTable.
									  "\", to link them.");
						
						$tables[$tmpTable]->addForeignKey($rel->leftTable);
						$tables[$tmpTable]->addForeignKey($rel->rightTable);
						
						// after that, we must remove the old relation between these two entities
						$tables[$rel->rightTable]->removeForeignKey($rel->leftTable);
						$tables[$rel->leftTable]->removeForeignKey($rel->rightTable);
						unset($this->relations[$rel->rightTable.'|'.$rel->leftTable]);
				}else{	// n/1 or 1/n or 1/1
						$rels[$rel->name]= $rel;
						// here, we have a commom relation between tables
						if(!isset($tables[$rel->leftTable]))
							$tables[$rel->leftTable]= $this->tables[$rel->leftTable];
						if(!isset($tables[$rel->rightTable]))
							$tables[$rel->rightTable]= $this->tables[$rel->rightTable];
						$tables[$rel->leftTable]->weight++;
						if($rel->leftTable != $rel->rightTable)
						{
							$tables[$rel->leftTable]->refered[]= $rel->name;
							$tables[$rel->rightTable]->addForeignKey($rel->leftTable);
						}
					 }
				next($this->relations);
			}
			
			if(sizeof($tables) == 0)
			{
				$this->log(0, "There are no entities in your project!");
				$this->log(2, "Is the project running with the correct idiom?");
				$this->log(2, "You can check if you haven't forgoten any \".\" or \";\" in the end of any line.");
				$this->errors++;
			}else{
					// here, any table that neither is pointed by nor points to another table will be removed
					// it only happens if, of course, the size of tables decreased after the analysis 
					if(sizeof($this->tables) > sizeof($tables))
					{
						reset($tables);
						while($table= current($this->tables))
						{
							if(!isset($tables[$table->name]) && $table->name != $this->theWebMindLanguageTempTable)
							{
								$this->log(3, '"'.$table->name.
											  '" has no relation with any other entity. I will ignore it and will not '.
											  'create any "floating" table for it, or any output.');
								$this->warnings++;
							}
							next($this->tables);
						}
					}
				 }
			
			// now, we update the real table list
			$this->tables= $tables;
			$this->relations= $rels;
			
			// we have to treat the 1/1 relations
			reset($this->relations);
			while($rel= current($this->relations))
			{
				if(isset($this->relations[$rel->rightTable.'|'.$rel->leftTable]))
				{
					// if there is a relation as specified with the last if and the current relation has the
					// maximum of 1, then the relation is obviously 1/1
					if($rel->max == '1')
					{
						// in this case, we will, for default, put one of the tables in evidence (the one which is more relevant to the application)
						$evidence= &$this->tables[(($this->tables[$rel->leftTable]->weight > $this->tables[$rel->rightTable]->weight)? $rel->leftTable: $rel->rightTable)];
						$other= &$this->tables[(($evidence->name == $rel->leftTable)? $rel->rightTable: $rel->leftTable)];

						// then, the less relevant will have its atrtributes moved to the evidenced one 
						if(!isset($this->relations[$rel->leftTable.'|'.$rel->leftTable]) && $rel->rightTable != $rel->leftTable)
						{
							foreach($other->attributes as $att)
							{
								$att->name= $other->name.'_'.$att->name;
								$evidence->attributes[$att->name]= $att;
							}
							
							// also, any relation this less relevant entity had must be moved to the other table
							// (both, those which point to it, and those which point from it)
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
							
							// fixing the tables pointed by the table that no longer exists
							foreach($other->foreignKeys as $fk)
							{
								$this->tables[$fk[1]]->refered= preg_replace('/'.$other->name.'/', $evidence->name, $this->tables[$fk[1]]->refered);
								$this->tables[$fk[1]]->foreignKeys= preg_replace('/'.$other->name.'/', $evidence->name, $this->tables[$fk[1]]->foreignKeys);
								$this->tables[$fk[1]]->removeReference($fk[1].'|'.$other->name);
							}
							
							// fixing the tables that were pointing to the table that no longer exists
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
							
							// removing the table 'other', and the relations 1/1
							$this->log(3, "Relation 1/1. The table <i>\"".
														$other->name.'"</i> has been removed. Its attributes were moved to "'.
														$rel->rightTable);
							$this->warnings++;
							unset($this->tables[$other->name]);
							unset($this->relations[$rel->leftTable.'|'.$rel->rightTable]);
							unset($this->relations[$rel->rightTable.'|'.$rel->leftTable]);
						}
					}
				}
				next($this->relations);
			}
			
			// here, we will add to each table a primary key, as set in the choosen DBMS configuration
			$this->log(4, "Dealing with Primary Keys");
			reset($this->tables);
			while($tab= current($this->tables))
			{
				$att= new Attribute($_MIND['primaryKeyPrefix'].$tab->name);
				$att->name= $_MIND['primaryKeyPrefix'].$tab->name;
				$att->type= 'integer';
				$att->pk= true;
				$att->size= '-1';
				$att->required= 1;
				$att->defaultValue= "'Default primary key value'";
				$att->references= '';
				$tmpAtt= $this->tables[$tab->name]->attributes;
				$att= $this->tables[$tab->name]->addAttribute($att);
				$this->tables[$tab->name]->attributes= array_merge(Array($att->name=>$att), $tmpAtt);
				next($this->tables);
			}
			
			$this->log(4, "Exporting format to theWebMind's knowledge format");
			/* formating in WebMindLanguage (wml format)
			 * this is not being used YET... the idea is to  build a "knowledge base center" with the main structure of many
			 * projects as people contribute to it. In the end, people will be able to start a project
			 * by its subject, loading automaticaly what most of the projects with that subject had
			 * I want to add some semanthic to it, and also the ability to learn with this base of knowledge
			 * Of course, any help is VERY welcome to reach this goal, too :)
			 */
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
			// this structure, presented above is unders construction and may suffer modification
			
			// here, we have to clear the mess and organize the house
			$project->wml= trim(implode('
', $this->sentences));
			$this->processedWML= '';
			$this->tmpSentences= '';
			$this->debug['status']= 1;
			
			// messaging the end of the action, with its status
			if($this->warnings == 0 && $this->errors == 0)
				$this->log(1, "Final Status: Finished with no errors");
			elseif($this->errors == 0)
				{
					$this->log(1, "Final Status: Finished with ".$this->warnings." warnings");
				}else{
						$this->log(0, "<b>Final Status: Failed with ".$this->warnings.
									  " warning(s) and ".$this->errors.' error(s)</b>');
					 }
			$this->saveCurrentDictionary();
			$this->updateUniverse();
			$this->debug['messages']= $this->messages;
			return $this;
		}
	
	
		/**
		* This function will saves the current synonymous dictionary to the local ORthe global one
		* @author Felipe Nascimento
		* @name saveCurrentDictionary
		* @return boolean
		*/
		private function saveCurrentDictionary()
		{
			if(!isset($this->currentOptions['addAutomatically']))
				return true;
			
			GLOBAL $_MIND;
			// yes, I do know it could be implemented in a different way, but, once it's gonna run only once each time
			// you run the project, I prefered to write just like this, to let it more "readable"
			if($this->currentOptions['addAutomatically'] == 'global')
			{
				$file= $_MIND['rootDir'].$_MIND['languageDir'].'/'.$this->lang.'/synonymous.json'; 
				$f= fopen($file, 'w+');
				if(file_put_contents($file, JSON_encode($this->synonymous)))
					return true;
				else
					return false;
			}elseif($this->currentOptions['addAutomatically'] == 'local')
				 {
				 	$file= $_MIND['rootDir'].$_MIND['publishDir'].'/'.$this->projectName.'/mind/synonymous.json';
				 	$f= fopen($file, 'w+');
					if(file_put_contents($file, JSON_encode($this->synonymous)))
						return true;
					else
						return false;
				 }
		}
		
		/**
		* This function will load the synonymous of this project, only. Depending on the set options;
		* @author Felipe Nascimento
		* @name loadLocalDictionary
		* @return void
		*/
		private function loadLocalDictionary()
		{
			GLOBAL $_MIND;
			$file= $_MIND['rootDir'].$_MIND['publishDir'].'/'.$this->projectName.'/mind/synonymous.json';
			if($this->currentOptions['useGlobalSynDic'] == 'on' && file_exists($file))
			{
				$syn= JSON_decode(file_get_contents($file), true);
				if(is_array($syn))
					$this->synonymous= array_merge($syn, $this->synonymous);
			}
		}
		
		/**
		* This function will load the global synonymous dictionary if Mind should do so, depending on the set options;
		* @author Felipe Nascimento
		* @name loadGlobalDictionary
		* @return void
		*/
		private function loadGlobalDictionary()
		{
			GLOBAL $_MIND;
			$file= $_MIND['rootDir'].$_MIND['languageDir'].'/'.$this->lang.'/synonymous.json';
			if($this->currentOptions['useGlobalSynDic'] == 'on' && file_exists($file))
			{
				$syn= JSON_decode(file_get_contents($file), true);
				if(is_array($syn))
					$this->synonymous= array_merge($syn, $this->synonymous);
			}
		}
		
		/**
		* Get method to return the list of currently supported types
		* @author Felipe Nascimento
		* @name getSupportedTypes
		* @return numeric Array
		*/
		public function getSupportedTypes(){
			return $this->types;
		}
		
		/**
		* This function is used only to show a list of the current entities
		* 	use it only for debug
		* @author Felipe Nascimento
		* @name showTables
		* @return void
		*/
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
		
		/**
		* This function is used to filter data, replacing special chars (specified in the Language Object)
		* @author Felipe Nascimento
		* @name filter
		* @param String $text
		* @return String
		*/
		public function filter($text)
		{
			$size= sizeof($this->especialChars);
			for($i=0; $i<$size; $i++)
			{
				$text= (str_replace($this->especialChars[$i], $this->fixedChars[$i], $text));
			}
			$text= preg_replace('/\.$|;$|,$/', '', $text);
			return strtolower($text);
		}
		
		/**
		* Interprets the expression and sets the its properties, applying the rules
		* @author Felipe Nascimento
		* @name execExpression
		* @param String $mindExp
		* @return void
		*/
		private function execExpression($mindExp)
		{
			// using the global _MIND to have access to its properties
			GLOBAL $_MIND;
			// keeping the original sentence, before changing it
			$original_sentence= $mindExp;
			
			// let's check if it is a table relation
			if(strpos($mindExp, $this->quantifierId))
			{
				// starting some variables
				$selfReference= false;
				$abstractLeftTable= false;
				$abstractRightTable= false;
				$oneByOneSelfRelation= false;
				
				// here, everything we have is the important/useful part of the instruction
				// in this case, we can do the following without fear
				$mindExp= explode(' ', trim($mindExp));
				
				// on a table relation, the first word is the left table
				$leftTable= $this->filter(trim($mindExp[0]));
				// and the left word is the secont entity
				$rightTable= $this->filter(trim($mindExp[sizeof($mindExp)-1]));
				
				// let's, first, see if any of these entities should be replaced by any synonymous
				if(isset($this->synonymous[$leftTable]))
					$leftTable= $this->synonymous[$leftTable];
				if(isset($this->synonymous[$rightTable]))
					$rightTable= $this->synonymous[$rightTable];

				/*
				 *  if the name of right table has a ":" it means it is probably an attribute
				 *  wrote with some mistake
				 *  in this case, we will alert the developer about it 
				 */
				if($pos = strpos($rightTable,':'))
				{
					$newTmpName= substr($rightTable, 0, $pos);
					$tmpType= substr($rightTable, $pos+1, strpos($rightTable, '(') - ($pos+1));
					$this->log(3, "Entity \"".$rightTable."\" renamed to \"".$newTmpName."\".".
								  "<img src='images/engine.gif' align='right' style='cursor:poniter;' alt='Add \"".$tmpType.
								  "\" as a type' title='Add \"".$tmpType."\" as a type'".
								  " onclick='Mind.Project.AddType(event, \"".$tmpType."\", ".JSON_encode($this->types).");'/>".
								  "<br/>I believe it should be a property, right? But I couldn't identify the type \"".$tmpType."\"!");
					// anyways... we will fix the name to a valid one
					$rightTable= $newTmpName;
					$this->warnings++;
				}
				// here, we find those entities which point to themselves
				if($leftTable == $rightTable)
				{
					// acording to its cardinality, it destroys the righttable, or create another table
					// to implement the relation
					if(in_array(trim($this->quantifiers['max']['min']), $mindExp))
					{
						$rightTable= false;
					}else{
							$rightTable= $rightTable.$_MIND['selfRelationalTable'];
						 }
					$selfReference= true;
				}
				
				// verify if the current entity was set manually to be abstract
				if(substr($leftTable, 0, 1) == '#')
				{
					$abstractLeftTable= true;
					$leftTable= preg_replace('/^#/', '', $leftTable);
				}
				if($rightTable)
				{
					if(substr($rightTable, 0, 1) == '#')
					{
						$abstractRightTable= true;
						$rightTable= preg_replace('/^#/', '', $rightTable);
					}
					if(!isset($this->tables[$rightTable]))
					{
						$this->tables[$rightTable]= new Table($rightTable);
					}
				}
				// verifies if the entity does not exist, yet
				if(!isset($this->tables[$leftTable]))
				{
					// then, creat it
					$this->tables[$leftTable]= new Table($leftTable);
				}
				
				if(!$rightTable)
				{
					if(!isset($this->relations[$leftTable.'|'.$leftTable]))
					{
						$rightTable= $leftTable;
						$oneByOneSelfRelation= true;
					}
				}
				
				// let's see if this entity should extend another one
				// it depends on the type of verb, used to identify the relation
				if($rightTable && preg_match('/'.preg_quote(trim($this->obligationId)).'/', implode(',', $mindExp)) > 0)
				{
					$this->tables[$leftTable]->extends= $rightTable;
					$this->tables[$rightTable]->abstract= true;
					$tmpToRemove= $this->tables[$leftTable];
				}
				if($rightTable && preg_match('/'.preg_quote(trim($this->belongsId)).'/', implode(',', $mindExp)) > 0)
				{
					$this->tables[$rightTable]->extends= $leftTable;
				}
				
				// are they abstract?
				if($abstractLeftTable)
					$this->tables[$leftTable]->abstract= true;
				if($abstractRightTable)
					$this->tables[$rightTable]->abstract= true;
				
				// treating cardinalities
				$min= (in_array(trim($this->quantifiers['min']['min']), $mindExp))? '0': '1';
				$max= (in_array(trim($this->quantifiers['max']['min']), $mindExp))? '1': 'n';
				
				// deciding for the name this relation will have
				if($rightTable)
					$relName= ($max=='n')? $leftTable.'|'.$rightTable: $rightTable.'|'.$leftTable;
				else
					$relName= false;
				
				// in case of self reference
				if($selfReference)
				{
					// if it is not 1/1 to the same table
					if(!$oneByOneSelfRelation)
					{
						if(!isset($this->relations[$relName]))
						{
							// we have to create a new table, and point it to the first one twice
							// one to the current ID, and another to the reference
							$att= new Attribute($_MIND['parentOnSelfRelation'].$leftTable);
							$att->required= 1;
							$att->unique= 0;
							$att->type= 'integer';
							$att->comment= 'References to the original '.$leftTable.' that points to itself';
							$att->mask= false;
							$att->defaultValue= false;
							$att->options= false;
							
							$this->tables[$rightTable]->addAttribute($att);
						}
					}else{
							// otherwise, Mind simply needs to have the first table referencing itself
							$this->tables[$leftTable]->addForeignKey($leftTable);
						 }
				}
				
				// creating the new relation
				if($relName)
				{
					$this->relations[$relName]= new Relation($relName);
					$this->relations[$relName]->min= $min;
					$this->relations[$relName]->max= $max;
				}
			}else{	// this is an attribute
					$mindExp= explode(trim($this->verbId), $mindExp);
					// firstly, we gotta create the left table, if it does not exist
					$leftTable= $this->filter(trim($mindExp[0]));
					
					// we here, will check if this entity may not be replaced by a synonymous
					if(isset($this->synonymous[$leftTable]))
						$leftTable= $this->synonymous[$leftTable];
					
					if(!isset($this->tables[$leftTable]))
					{
						$this->tables[$leftTable]= new Table($leftTable);
					}
					
					// here, we will divide the attribute name of the rest 
					$mindExp= explode(':', $mindExp[1], 2);
					$attName= explode(' ', trim($mindExp[0]));
					$attName= $attName[sizeof($attName)-1];
					
					// let's parse the atribute properties
					$mindExp= explode('(', $mindExp[1], 2);
					$att= new Attribute($this->filter($attName));
					
					/*
					 * Notice that, each property we set, must be removed from the
					 * expression, to not be processed twice 
					 */
					
					// attributes with # will set as hidden, to the module
					if(substr($att->name, 0,1) == '#')
					{
						$att->hidden= true;
						$att->name= preg_replace('/^#/', '', $att->name);
					}
					
					// Is this a required field?
					if(strpos($mindExp[1], trim($this->required)))
					{
						$mindExp[1]= str_replace(trim($this->required), '', $mindExp[1]);
						$att->required= 1;
					}else{
							$att->required= 0;
						 }
						 
					// is it unique?
					if(strpos($mindExp[1], trim($this->unique)))
					{
						$mindExp[1]= str_replace(trim($this->unique), '', $mindExp[1]);
						$att->unique= 1;
					}else{
							$att->unique= 0;
						 }
					
					// the type is the word which is before "("
					$att->type= trim($mindExp[0]);
					
					// Let's get the default value to the current attribute ( between "" )
					preg_match('/([^(\\\)])?"(.*)[^(\\\)]"/', preg_replace('/\)/', ') ', $mindExp[1]), $matches);
					if(sizeof($matches)>0)
					{
						// if it does have a default value, we have to prepare it
						$att->defaultValue= addslashes(preg_replace('/^("|.")|"$/', '', trim($matches[0])));
						
						// default values may be executable, the "Exec:" keyword is required, then
						if(substr($att->defaultValue, 0, 5) != "Exec:")
							$att->defaultValue= "'".trim($att->defaultValue)."'";
						else
							$att->defaultValue= preg_replace('/^Exec:/', '', trim($att->defaultValue));
						
						// removing the default value
						$mindExp[1]= str_replace($matches[0], '', $mindExp[1]);
					}
					
					// checking for masks ( between [ ] )
					preg_match('/[^(\\\)]\[(.*)[^(\\\)]\]/', $mindExp[1], $matches);
					if(sizeof($matches)>0)
					{
						$mindExp[1]= str_replace($matches[0], '', $mindExp[1]);
						$att->mask= preg_replace('/^(\[|.\[)|\]$/', '', trim($matches[0]));
					}
					
					// looking for comments the attribute have (identified by the use of // )
					preg_match('/\/\/.*$/', $mindExp[1], $matches);
					if(sizeof($matches)>0)
					{
						$matches[0]= preg_replace('/\/\//', '', trim($matches[0]));
						$att->comment= $matches[0];
					}
					
					// get the options for attribute ( between { } )
					preg_match('/[^(\\\)]\{(.*)[^(\\\)]\}/', $mindExp[1], $matches);
					if(sizeof($matches)>0)
					{
						/* if there are valid options, we have to parse them
						 * the pattern is just like this:
						 * code=label|anotherCode=AnotherLabel
						 * E.g.: {F=Female|M=Male}
						 */
						$matches[0]= preg_replace('/^(\{|.\{)|\}$/', '', trim($matches[0]));
						$mindExp[1]= str_replace($matches[0], '', $mindExp[1]);
					
						$matches= preg_split('/(\|)/', $matches[0]);
						$att->options= $matches;
						for($i=0; $i<sizeof($att->options);$i++)
						{
							$att->options[$i]= explode('=', $att->options[$i], 2);
						}
					}
					
					// te attribute size is the number that rested, parsed as real
					$att->size= (real)$mindExp[1];
					
					// finally, add the attribute to the global list of attributes and also to the current table
					// if the same attribute was already used, with the same characteristics, we tell the developer
					if(isset($this->globalAttributeList[$att->name]) && $this->globalAttributeList[$att->name] == $att)
						$this->log(2, "Attribute \"".$att->name."\" used more than one time, with the same carachteristics.<br/>Aren't they the same property? You could set both in the same table, or in a new table creating an agreggation.");
					$this->globalAttributeList[$att->name]= $att;
					$this->tables[$leftTable]->addAttribute($att);
				 }
		}
		/**
		* This is the constructor
		* 	if it receives a project object by parameter, it processes this project, otherwise, it simply returns itself
		* 	with its current properties
		* @author Felipe Nascimento
		* @name __construct
		* @param [Project Object]
		* @return MindProcessor Object
		*/
		public function __construct($project=false)
		{
			GLOBAL $_MIND;
			$this->currentOptions= $_MIND['fw']->loadOptions();
			
			// if no project has been received, we simply return the new empty Object
			if(!$project){
				return $this;
			}
			$this->lang= $project->lang;
			$this->projectName= $project->name;
			
			$this->loadGlobalDictionary();
			$this->loadLocalDictionary();
			
			// if a project Object has been sent to this method, then process it and return its result
			return $this->process($project);
		}
	}
