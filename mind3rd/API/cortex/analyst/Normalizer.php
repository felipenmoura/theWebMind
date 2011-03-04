<?php
	/**
	 * Will normalize the data and entities structure applying
	 * rules and patterns. Thanks for Edgar F. Codd for all he
	 * created and wondered for the Relational Model
	 *
	 * @author felipe
	 */
	class Normalizer extends Normal{

		public static $tmpEntities	= Array();
		public static $tmpRelations	= Array();
		
		/**
		 * Redirects all the relations that point to, or are pointed by the
		 * $from entity, to the $to entity
		 * 
		 * @param MindEntity $from
		 * @param MindEntity $to 
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
			$rel= next(self::$oneByOne);
			do
			{
				$rel= &Analyst::$relations[$rel->name];
				
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
						self::mergeEntities(self::$focus, self::$predicate, $rel);
					else
						self::fixOneByOneRelation(self::$focus,
												  self::$predicate,
												  $rel);
				}else{
						// for 0:1 / 1:1 relations
						self::fixOneByOneRelation(self::$focus,
												  self::$predicate,
												  $rel);
					 }
			}while($rel= next(self::$oneByOne));
		}
		
		/**
		 * Fixes all the n:n relations
		 */
		public static function fixNByNRel()
		{
			foreach(self::$nByN as &$rel)
			{
				// first of all, we will need a link table
				$relName= $rel->focus->name.
						  PROPERTY_SEPARATOR.
						  $rel->rel->name;
				$relOtherName=  $rel->rel->name.
								PROPERTY_SEPARATOR.
								$rel->focus->name;
						
				if(isset(Analyst::$entities[$relName]))
					$linkTable= &Analyst::$entities[$relName];
				elseif(isset(Analyst::$entities[$relOtherName]))
						$linkTable= &Analyst::$entities[$relOtherName];
					else
					{
						$linkTable= new MindEntity($relName);
						Analyst::$entities[$linkTable->name]= $linkTable;
					}
				$linkEntity= &Analyst::$entities[$linkTable->name];
				
				$rel	 = &Analyst::$relations[$relName];
				$otherRel= &Analyst::$relations[$relName];
				
				// then, let's relate it to both the entities
				Analyst::addToFocus($rel->focus);
				$relation= Analyst::addRelationToFocused( $linkEntity,
														  $rel->linkType,
														  $rel->linkVerb,
														  0,
														  'n');
				$relation->uniqueRef= true;
				// and in the end, we remove the old n/n relation
				Analyst::unsetRelation($rel);
				Analyst::clearFocused();
			}
		}
		
		public static function setUpKeys()
		{
			GLOBAL $_MIND;
			
			foreach(Analyst::$entities as &$entity)
			{
				$pkPrefix= $_MIND->defaults['pk_prefix'];
				$fkPrefix= $_MIND->defaults['fk_prefix'];
				
				// checking for foreign keys, first
				foreach($entity->relations as &$rel)
				{
					if(!$rel) continue;
					if($rel->rel->name == $entity->name)
					{
						$propName= $fkPrefix.$rel->focus->name;
						if(!$entity->hasProperty($propName))
						{
							$fk= new MindProperty();
							$fk ->setName($propName)
								->setDefault(AUTOINCREMENT_DEFVAL)
								->setRequired(true)
								->setAsKey()
								->setType('int')
								->setRefTo($rel->focus);
							$entity->addProperty($fk);
						}else{
								$entity ->properties[$propName]
										->setRefTo($rel->focus);
							 }
					}
				}
				
				// now we'll see the primary keys
				if(sizeof($entity->properties) != sizeof($entity->pks))
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
						$entity->addProperty($pk, true);
					}else{
							$entity->properties[$propName]->setAsKey();
						 }
				}
			}
		}
		
		/**
		 * Normalizes the known structure
		 */
		public static function normalize()
		{
			self::separateByRelationQuantifiers(); // ok
			self::fixOneByOneRel(); // ok
			self::fixNByNRel(); // ok
			self::setUpKeys();
		}
	}