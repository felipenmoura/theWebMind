<?php
	/**
	 * This file is part of TheWebMind 3rd generation.
	 * 
	 * Normalizer components, within the Cortex/Analyst packages.<br/>
	 * Notice that, these packages are being used only for documentation,
	 * not to organize the classes.
	 * 
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 * @license licenses/mind3rd.license
	 */

	/**
	 * Normalizes the data structure.
	 * 
	 * Will normalize the data and entities structure
	 * applying rules and patterns. Thanks for
	 * Edgar F. Codd for all he created and wondered
	 * for the Relational Model
	 *
	 * @package Cortex
	 * @subpackage Analyst
	 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 * @filesource
	 */
	class Normalizer extends Normal{

		public static $tmpEntities	= Array();
		public static $tmpRelations	= Array();
		
		/**
		 * Redirects all the relations between entities.
		 * 
		 * Redirects all the relations that point to, or are pointed by the
		 * $from entity, to the $to entity.
		 * 
		 * @param MindEntity $from Entity which will lose its references
		 * @param MindEntity $to Entity which will be reffered instead
		 */
		public static function redirectRelations(MindEntity &$from, MindEntity &$to)
		{
			foreach($from->relations as &$rel)
			{
				if(!$rel)
					continue;
				if($rel->focus->name == $from->name)
				{
					$rel->setFocus($to);
					$rel->rename($rel->rel->name.
								 PROPERTY_SEPARATOR.
								 $rel->focus->name);
				}else{
						$rel->setRel($to);
						$rel->rename($rel->focus->name.
									 PROPERTY_SEPARATOR.
									 $rel->rel->name);
					 }
			}
		}
		
		/**
		 * Fixes the 1:1 relations, normalizing them
		 */
		public static function fixOneByOneRel()
		{
			if(sizeof(self::$oneByOne) == 0)
				return true;
			reset(self::$oneByOne);
			$rel= current(self::$oneByOne);
			
			foreach(self::$oneByOne as &$rel)
			{
				$rel= &Analyst::$relations[$rel->name];
				
				if(is_null($rel) || is_null($rel->focus) || is_null($rel->rel))
					continue;
				// defining the focus
				$entities= self::setByRelevance($rel->focus, $rel->rel);
				self::$focus= $entities[0];
				self::$predicate= $entities[1];
						
				// let's check the minimun quantifiers
				if($rel->min== 1 && $rel->opposite->min == 1)
				{
					// for 1:1 / 1:1 relations
					self::mergeEntities(self::$focus, self::$predicate, $rel);
				}elseif($rel->min== 0 && $rel->opposite->min == 0)
					{
						// for 0:1 / 0:1 relations
						if(Analyst::isItWorthMerging(self::$predicate))
						{
							self::mergeEntities(self::$focus, self::$predicate, $rel);
						}else{
								self::fixOneByOneRelation(self::$focus,
														  self::$predicate,
														  $rel);
							 }
					}else{
							// for 0:1 / 1:1 relations
							self::fixOneByOneRelation(self::$focus,
													  self::$predicate,
													  $rel);
						 }
			}
		}
		
		/**
		 * Creates an extra entity to link multiple relations.
		 * 
		 * Creates the entity to be used as a link between the
		 * other two entities due to an N:N relation
		 * 
		 * @param MindRelation $rel
		 * @return MindEntity
		 */
		public static function &createNByNEntity(MindRelation &$rel)
		{
			$linkTable= false;
			$relName= $rel->focus->name.
					  PROPERTY_SEPARATOR.
					  $rel->rel->name;
			$relOtherName=  $rel->rel->name.
							PROPERTY_SEPARATOR.
							$rel->focus->name;
			if(isset(Analyst::$entities[$relName]))
				$linkTable= &Analyst::$entities[$relName];
			if(isset(Analyst::$entities[$relOtherName]))
				$linkTable= &Analyst::$entities[$relOtherName];
			if(!$linkTable)
			{
				$linkTable= new MindEntity($relName);
				Analyst::$entities[$linkTable->name]= $linkTable;
			}
			Analyst::$entities[$linkTable->name]->linkTable= Array($rel->focus->name,
																   $rel->rel->name);
			return Analyst::$entities[$linkTable->name];
		}
		
		/**
		 * Create all the relations needed to fix n:n relations.
		 * 
		 * It takes the $left and $right entities and set the
		 * $center to reffer to it. The $rel parameter is used
		 * to identifies characteristics from an original
		 * relation, like linkVerb or linkType.
		 * 
		 * @param MindEntity $left
		 * @param MindEntity $center
		 * @param MindEntity $right
		 * @param MindRelation $rel 
		 */
		public static function createNbyNRelations( MindEntity &$left,
													MindEntity &$center,
													MindEntity &$right,
													MindRelation &$rel)
		{
			Analyst::addToFocus($left);
			Analyst::addToFocus($right);
			$relations= Analyst::addRelationToFocused( $center,
													  $rel->linkType,
													  $rel->linkVerb,
													  0,
													  'n',
													  true);
		}
		
		/**
		 * Fixes all the n:n relations
		 */
		public static function fixNByNRel()
		{
			foreach(self::$nByN as &$rel)
			{
				if(!$rel->treated)
				{
					$entity= self::createNByNEntity($rel);
					$rel->opposite->treated= true;
					self::createNbyNRelations($rel->focus,
											  $entity,
											  $rel->rel,
											  $rel);
				}
				Analyst::unsetRelation($rel);
				Analyst::clearFocused();
			}
		}
		
		/**
		 * Adds all the foreign keys to all the entities that
		 * may need it.
		 */
		public static function addFks()
		{
			GLOBAL $_MIND;
			$fkPrefix= $_MIND->defaults['fk_prefix'];
			$pkPrefix= $_MIND->defaults['pk_prefix'];
			foreach(Analyst::$relations as &$relation)
			{
				$pointed= $relation->focus;
				
				foreach($pointed->pks as &$pk)
				{
					$propName= $fkPrefix.preg_replace("/^".$pkPrefix."/",
													  '',
													  $pk->name);
					$entity= &$relation->rel;
					
					if($entity->linkTable)
					{
						$pkToRemove= $pkPrefix.$entity->name;
						if($entity->hasProperty($pkToRemove))
							$entity->removeProperty($pkToRemove);
						$refTo= $entity->getRefTo();
						
						if(
							(
								!in_array($pointed->name, $entity->linkTable)
								&&
								$relation->max == QUANTIFIER_MAX_MAX
							)
						  )
						{
							$p= new MindProperty();
							$p ->setName($_MIND->defaults['counter_col'])
								->setRequired(true)
								->setType('int')
								->comment= Mind::$l10n->getMessage('additionalCounterCol');
							$entity->addProperty($p);
						}
					}elseif($pointed->linkTable &&
							$relation->max == QUANTIFIER_MAX_MIN)
					{
						echo $pointed->name." - ".$entity->name."\n";
					}
					/*if($pointed->linkTable)
					{
						if($relation->max == QUANTIFIER_MAX_MIN)
						{
							echo $pointed->name." <<< ".$entity->name."\n";
							
							$fkToRemove= $fkPrefix.$pointed->name;
							echo $fkToRemove."\n";
							if($entity->hasProperty($fkToRemove))
								$entity->removeProperty($fkToRemove);
						}
						$refTo= $pointed->getRefTo();
						
						if(!in_array($pointed->name, $pointed->linkTable) &&
							$relation->max == QUANTIFIER_MAX_MAX)
						{
							
							$p= new MindProperty();
							$p ->setName($_MIND->defaults['counter_col'])
								->setRequired(true)
								->setType('int')
								->comment= Mind::$l10n->getMessage('additionalCounterCol');
							$pointed->addProperty($p);
						}
						
					}*/
					
					if(!$entity->hasProperty($propName))
					{
						$fk= new MindProperty();
						$fk ->setName($propName)
							->setRequired(true)
							//->setAsKey()
							->setType('int')
							->setRefTo($relation->focus, $pk);
						if($relation->uniqueRef)
							$fk->setAsKey();
						$entity->addProperty($fk);
					}else{
							$entity ->properties[$propName]
									->setRefTo($relation->focus, $pk);
							if($relation->uniqueRef)
								$entity ->properties[$propName]->setAsKey();
						 }
				}
			}
		}
		
		/**
		 * Adds all the primary keys the entities will need.
		 */
		public static function addPks()
		{
			GLOBAL $_MIND;
			$pkPrefix= $_MIND->defaults['pk_prefix'];
			foreach(Analyst::$entities as &$entity)
			{
				if( sizeof($entity->pks) == 0
						||
					sizeof($entity->properties) != sizeof($entity->pks))
				{
					$propName= $pkPrefix.$entity->name;
					if(!$entity->hasProperty($propName))
					{
						$pk= new MindProperty();
						$pk ->setAsKey()
							->setName($propName)
							->setDefault(AUTOINCREMENT_DEFVAL)
							->setRequired(true)
							->setType('int');
						$entity->addProperty($pk);
					}else{
							$entity->properties[$propName]->setAsKey();
						 }
				}
			}
		}
		
		/**
		 * Will set the primary and foreign keys to entities.
		 */
		public static function setUpKeys()
		{
			GLOBAL $_MIND;
			Analyst::$entities= array_filter(Analyst::$entities);
			Analyst::$relations= array_filter(Analyst::$relations);
			
			self::addPks();
			self::addFks();
		}
		
		/**
		 * Clears static properties setting them to their default value.
		 * Quite useful when used through a command line single session.
		 */
		public static function reset()
		{
			self::$oneByOne = false;
			self::$oneByOne = Array();
			self::$nByN     = false;
			self::$nByN     = Array();
			self::$oneByN   = false;
			self::$oneByN   = Array();
			self::$focus    = false;
			self::$focus    = Array();
			self::$predicate= false;
			self::$predicate= Array();
		}
		
		/**
		 * Normalizes the known structure.
		 * 
		 * It will apply the n:n and 1:1 rules, plus setting the
		 * foreign and primary keys to all the entities.
		 * Please, notice that there are rules to be followed here,
		 * such as:<pre>
		 *    1:1 to 1:1 relations will always merge entities
		 *    0:1 to 0:1 relations will always try to identify the less
		 *               important and set it to point to the other entity
		 *               setting its foreign key as a primary key
		 *    0:1 to 1:1 will decide if it should wether merge or fix the
		 *               relation. It will decide it using the following
		 *               parameters of decision:
		 *               - number of properties: as many, less mergeable
		 *               - number of relations:  as less reffered,
		 *                                       more mergeable
		 *               - big properties:       as many big properties,
		 *                                       less mergeable
		 *    n:n        relations will generate an extra entity, altough,
		 *               if the entity with that name already exists, it 
		 *               takes the existing one.
		 *               If the antity ONLY has keys, no pk will be added.
		 * </pre>
		 */
		public static function normalize()
		{
			self::reset();
			self::separateByRelationQuantifiers();
			self::fixOneByOneRel();
			self::fixNByNRel();
			self::setUpKeys();
		}
	}