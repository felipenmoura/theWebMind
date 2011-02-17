<?php
/**
 * This class is responsable to the analisys of the system
 * preparing the relations and apply the first rules
 *
 * foreach token
 * if isSubst & MindProp.isProperty
 * 	if !prop
 * 		if !evidencedEntity
 * 			setEvidencedEntity
 * 		else
 * 			MindEntity.addRelation
 * 	else
 * 		if !mindProp.isValid
 * 			echo ERROR
 * 		if !mindProp.isKnown
 * 			MindDarwin.addToDoubts
 * 			return
 * 		MindEntity.addProp
 * 		return
 *
 * @author felipe
 */
class Analyst {

	public static $entities= Array();
	public static $relations= Array();

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
		echo "Entities: ".sizeof(self::$entities)."\n";
		foreach(self::$entities as $entity)
		{
			if($detailed)
				echo "   ".$entity->name."\n";
			foreach($entity->properties as $prop)
			{
				$props++;
				echo "      ".$prop->name."\n";
			}
		}
		foreach(self::$relations as $k=>$rel)
		{
			echo "      ".
				 $k.': '.$rel->focus->name.' -> '.$rel->rel->name.
				 "\n";
		}
		echo "Properties: ".$props."\n";
		echo "Relations: ".sizeof(self::$relations)."\n";
	}

	public static function normalizeIt()
	{
		Normalizer::normalize();
	}

	/**
	 * Reset the properties of the analyst itself
	 */
	public static function reset()
	{
		self::$entities= Array();
		self::$relations= Array();
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
		$tmpProperties= Array();
		$i= 0;
		$focus= null;
		$linkVerb= null;
		$min= null;
		$max= 'n';
		$linkType= 'action';

		// foreach token
		foreach($structureKeys as $token)
		{
			$word= $expression[$i];
			$i++;
			// storing the current used verb
			if($token==Tokenizer::MT_VERB)
			{
				$linkVerb= $word;
				continue;
			}

			// setting quantifiers
			if(
				$min == null &&
				(
					$token == Tokenizer::MT_NONE ||
					$token == Tokenizer::MT_ONE
				)
			  )
			{
				$min= ($token == Tokenizer::MT_ONE)? 1: 0;
				continue;
			}
			if(
				$min != null &&
				(
					$token == Tokenizer::MT_MANY ||
					$token == Tokenizer::MT_ONE
				)
			  )
			{
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
					if(!$focus)
					{
						$focus= &self::$entities[$word];
					}else{
							/*
							 * here, if it is an entity and the focused
							 * entity has already been selected, it means
							 * it is the second entity on the instruction, so,
							 * it is a relation between entities(or should be)
							 */
							$rel= &self::$entities[$word];

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
		if(sizeof($tmpProperties)>0 && $focus)
		{
			foreach($tmpProperties as $prop)
				$focus->addProperty($prop);
		}
	}

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
		self::normalizeIt();
	}
}