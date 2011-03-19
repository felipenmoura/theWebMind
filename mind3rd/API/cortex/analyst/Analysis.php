<?php
/**
 * This file is part of theWebMind 3rd generation.
 * 
 * Analyst components, within the Cortex/Analyst packages.<br/>
 * Notice that, these packages are being used only for documentation,
 * not to organize the classes.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 * @filesource
 */
/**
 * This is an abstract class to be used to analyze the project.
 *
 * @package Cortex
 * @subpackage Analyst
 * @author  Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
abstract class Analysis {

	public static $entities	= Array();
	public static $relations= Array();
	public static $focused	= Array();
	
	/**
	 * Returns an array with the names of all entities.
	 * 
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
	 * Clears the current focused entities.
	 */
	public static function clearFocused()
	{
		self::$focused  = false;
		self::$focused  = Array();
	}
	
	/**
	 * Adds an entity to the focused entities list.
	 * @param MindEntity &$entity The entity to be added to focus
	 */
	public static function addToFocus(MindEntity &$entity)
	{
		self::$focused[$entity->name]= $entity;
	}
	
	/**
	 * Adds a relation to the focused list.
	 * 
	 * Adds a relation between all the focused entities specified in the
	 * current expression and the passed $rel entity
	 * 
	 * @param MindEntity &$rel The entity to be added
	 * @param String $linkType The type of like(action, possibility or must)
	 * @param String $linkVerb The verb used to identify the relation
	 * @param Mixed $min The minimun quantifier
	 * @param Mixed $max The maximun quantifier
	 * @param boolean [$uniqueRef] If it should be an unique referation
	 * @return MindRelationCollection An array of all the created relations
	 */
	public static function &addRelationToFocused(MindEntity &$rel, $linkType,
									            $linkVerb, $min, $max,
												$uniqueRef=false)
	{
		// for each focused entity
		foreach(self::$focused as &$focus)
		{
			$curRelation= self::addRelationBetween($focus, $rel, $linkType, $linkVerb, $min, $max);
			$arRet[]= &$curRelation;
		}
		return $arRet;
	}
	
	public static function &addRelationBetween (MindEntity $focus,
												MindEntity &$rel, $linkType,
									            $linkVerb, $min, $max,
												$uniqueRef=false)
	{
		$arRet= Array();
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
		$curRelation->uniqueRef= $uniqueRef;

		// and let's use the relation name as index, as said before
		self::$relations[$relationName]= $curRelation;
		return $curRelation;
	}
	
	/**
	 * Adds the passed property to all the focused entities.
	 * 
	 * @param MindProperty &$prop The property to be added to the focused entities.
	 */
	public static function addPropertyToFocused(MindProperty &$prop)
	{
		// for each focused entity
		foreach(self::$focused as $focus)
			$focus->addProperty($prop);
	}

	/**
	 * Analyzes the project.
	 * 
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
		// I'm gonna try to put it in stepByStep style, to
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
			if($token==Tokenizer::MT_VERB || $token==Tokenizer::MT_QBE)
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

							if(in_array($word, array_keys(self::$focused)))
							{
								/*if($max != QUANTIFIER_MAX_MAX)
									continue;*/
								self::$entities[$word]->setSelfReferred($max);
								//continue;
							}
							/*
							 * here, if it is an entity and the focused
							 * entities have already been selected(post verb),
							 * it means it is the second entity on the
							 * instruction, so, it is a relation between entities
							 */
							self::addRelationToFocused(self::$entities[$word],
									                   $linkType,
									                   $linkVerb,
									                   $min,
									                   $max);
						 }
				}else{
					// ok, after that, this is just easy :)
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
}