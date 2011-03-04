<?php
/**
 * This class is responsable to the analisys of the system
 * preparing the relations and apply the first rules
 *
 * @author felipe
 */
class Analyst {

	public static $entities	= Array();
	public static $relations= Array();
	public static $focused	= Array();

	/**
	 * Returns true if the entity is worth mergin with another, or not.
	 * It takes the relevance as parameter to determine wheter the entity
	 * should be merged(stop existing) or not.
	 * 
	 * @global Mind $_MIND
	 * @param MindEntity $en
	 * @return boolean
	 */
	public static function isItWorthMerging(MindEntity $en)
	{
		GLOBAL $_MIND;
		if(Normalizer::relevanceAmount($en) < $_MIND->conf['merging_amount_pts'])
			return true;
		return false;
	}
	
	/**
	 * Removes an entity from the entities list
	 * @param type $entity 
	 */
	public static function removeEntity($entity)
	{
		unset(self::$entities[$entity]);
		//self::$entities[$entity]= false;
		//self::$entities= array_filter(self::$entities);
		unset($entity);
	}
	
	/**
	 * Removes a relation between two entities, but it will NOT remove
	 * the refBy or refTo properties of each entity.
	 * It is most often, useful to be used for relations that may have
	 * been represented twice, as the same idea.
	 * It also decreases the relevance of the focused entity.
	 * 
	 * @param MindRelation $rel 
	 */
	public static function unsetRelation(MindRelation &$rel)
	{
		if($rel->focus)
		{
			$rel->focus->relations[$rel->name]= false;
			$rel->focus->relevance--;
		}
		if($rel->rel)
			$rel->rel->relations[$rel->name]= false;
		unset(self::$relations[$rel->name]);
	}
	
	/**
	 * Gets the whole interpreted context, with all the
	 * analysed information
	 * @return Array
	 */
	public static function getUniverse()
	{
		return Array(
						'entities'=>self::$entities,
						'relations'=>self::$relations
					);
	}

	/**
	 * Prints out the analysed content
	 * @param boolean $detailed
	 */
	public static function printWhatYouGet($detailed=true)
	{
		$props= 0;
		echo "ENTITIES: ".sizeof(self::$entities)."\n";
		foreach(self::$entities as $k=>$entity)
		{
			if($detailed)
				echo "   (".$entity->relevance.")".$entity->name."\n";
			foreach($entity->properties as $prop)
			{
				$props++;
				echo "        ".$prop->name."\n";
			}
		}
		echo "RELATIONS:\n";
		foreach(self::$relations as $k=>$rel)
		{
			if(!$rel)
				continue;
			echo "      ".
				 $k.': '.
				 $rel->rel->name.' -> '.
				 $rel->focus->name.
				 "\n";
		}
		echo "Properties: ".$props."\n";
		echo "Relations: ".sizeof(self::$relations)."\n";
	}

	/**
	 * Applies normalization rules to the currently analized structure
	 */
	public static function normalizeIt()
	{
		Normalizer::normalize();
		echo "passei por aqui!\n";
		self::$relations= array_filter(self::$relations);
		self::$entities= array_filter(self::$entities);
	}

	/**
	 * Reset the properties of the analyst itself
	 */
	public static function reset()
	{
		self::$entities = false;
		self::$relations= false;
		self::$entities = Array();
		self::$relations= Array();
		self::clearFocused();
	}

	public static function clearFocused()
	{
		self::$focused  = false;
		self::$focused  = Array();
	}
	
	/**
	 * Adds an entity to the focused entities list
	 * @param MindEntity $entity 
	 */
	public static function addToFocus(MindEntity &$entity)
	{
		self::$focused[]= $entity;
	}
	
	/**
	 * Adds a relation between all the focused entities specified in the
	 * current expression and the passed $rel entity
	 * 
	 * @param MindEntity $rel
	 * @param String $linkType
	 * @param String $linkVerb
	 * @param Mixed $min
	 * @param Mixed $max
	 * @return MindRelation
	 */
	public static function addRelationToFocused(MindEntity &$rel, $linkType,
									            $linkVerb, $min, $max)
	{
		// for each focused entity
		foreach(self::$focused as $focus)
		{
			/*
			 * we will use this relationName as index on an
			 * indexed array to speed up the search for
			 * relations in the future
			 */
			$relationName= $focus->name."_".$rel->name;

			// let's create the relation itself
			$curRelation= new MindRelation($relationName);

			$curRelation->setLinkType($linkType)
						->setMin($min)
						->setMax($max)
						->setUsedVerb($linkVerb)
						->setEntities(
								self::$entities[$focus->name],
								self::$entities[$rel->name]);
			// now, both entities will POINT to the same relation
			$focus->addRef($curRelation);
			$rel->addRef($curRelation);

			// and let's use the relation name as index, as said before
			self::$relations[$relationName]= $curRelation;
		}
		return $curRelation;
	}
	
	/**
	 * Adds the passed property to all the focused entities.
	 * 
	 * @param MindProperty $prop 
	 */
	public static function addPropertyToFocused(MindProperty &$prop)
	{
		// for each focused entity
		foreach(self::$focused as $focus)
			$focus->addProperty($prop);
	}
	
	/**
	 * Gets an Array of the names of all the focused entities on
	 * the current expression.
	 * @return Array
	 */
	public static function getFocusedNames()
	{
		$focused= Array();
		foreach(self::$focused as $focus)
			$focused[]= $focus->name;
		return $focused;
	}
	
	/**
	 * This method receives each expression and analizes
	 * the best way to act and to store the understood
	 * structure
	 * 
	 * @param String $expression
	 * @param String $structure
	 * @param Array $structureKeys
	 * @return Boolean True if everything went ok, false when any error occurred
	 */
    public static function analize($expression, $structure, Array $structureKeys){
		// I'm gonna try to put it in stepByStep style to
		// get it easier for me and for you to understand
		// and follow the thoughts

		// setting up
		self::clearFocused();
		$tmpProperties= Array();
		$i            = 0;
		$linkVerb     = null;
		$min          = null;
		$max          = null;
		$linkType     = 'action';
		$relation     = false;
		$posVerb      = false;

		// foreach token
		foreach($structureKeys as $token)
		{
			$word= $expression[$i];
			$i++;
			// storing the current used verb
			if($token==Tokenizer::MT_VERB)
			{
				$linkVerb= $word;
				$posVerb= true;
				continue;
			}

			// setting quantifiers
			if($token == Tokenizer::MT_NONE)
			{
				$min= 0;
				continue;
			}
			if($token == Tokenizer::MT_ONE || $token == Tokenizer::MT_MANY)
			{
				if(!is_null($max))
					$min= $max;
				$max= ($token == Tokenizer::MT_ONE)? 1: 'n';
			}

			// verifying the way the entities will be linked
			if($token==Tokenizer::MT_QMAY)
			{
				$linkType= 'possibility';
				continue;
			}
			if($token==Tokenizer::MT_QMUST)
			{
				$linkType= 'must';
				continue;
			}

			// if it is a substantive
			if($token==Tokenizer::MT_SUBST)
			{
				// if it is an entity
				if(MindEntity::isEntity($word))
				{
					// fixing any special char
					$word= Mind::$lexer->fixWordChars($word);

					// yeah, I know it looks crazy, but try to follow my thoughts
					// let's instantiate a new Entity in case it has not been
					// instantiated before
					if(!isset(self::$entities[$word]))
						self::$entities[$word]= new MindEntity($word);

					// each instruction *should* have one focused entity
					// we will use the first entity on each expression as focus
					if(!$posVerb)
					{
						self::addToFocus(self::$entities[$word]);
					}else{
							$relation= true;
							// if min or max quantifier have not been set
							if(is_null($min))
								$min= ($linkType == 'must')? 1: 0;
							if(is_null($max))
								$max= 'n';

							/*
							 * here, if it is an entity and the focused
							 * entity has already been selected, it means
							 * it is the second entity on the instruction, so,
							 * it is a relation between entities
							 */
							
							self::addRelationToFocused(self::$entities[$word],
									                   $linkType,
									                   $linkVerb,
									                   $min,
									                   $max);
						 }
				}else{
					// ok, after that, this is just easy, now :)
					// let's store all the properties to a temporary array
					$tmpProperties[]= new MindProperty($word);
				}
			}
		}
		
		// adding the properties to the focused entity
		// we're doing it now, because according to the selected idiom
		// the sequence of focused entity and properties may vary
		if(sizeof($tmpProperties)>0 && $posVerb)
		{
			foreach($tmpProperties as $prop)
				self::addPropertyToFocused($prop);
		}
		
		// if there was a relation, we will return some details about it
		if($relation && isset(self::$entities[$word]))
			return Array('min'		=>$min,
						 'max'		=>$max,
						 'linkVerb'	=>$linkVerb,
						 'linkType'	=>$linkType,
						 'focus'	=>implode(', ', self::getFocusedNames()),
						 'rel'		=>self::$entities[$word]->name);
		// otherwise, we return the focused entities
		return self::$focused;
	}

	/**
	 * Sweeps through all the matched expressions
	 * @param Array $matches 
	 */
	public static function sweep($matches)
	{
		// let's clear the Analyst memory as it uses static properties
		self::reset();

		// now we gotta analyse each valid expression
		foreach($matches as $found)
		{
			$len= strlen($found[0]);
			$expression= array_slice(Token::$words, $found[1], $len);
			$tokens= array_slice(Token::$spine, $found[1], $len);
			$struct= $found[0];

			// let's analize it, now
			// Analyst will store it on its own static structure
			Analyst::analize($expression, $struct, $tokens);
		}
		self::clearFocused();
		self::normalizeIt();
	}
}