<?php
/**
 * Geneic methods to be used by the Normalizer
 *
 * @author felipe
 */
abstract class Normal {
	
	public static $oneByOne		= Array();
	public static $nByN			= Array();
	public static $oneByN		= Array();
	public static $focus		= Array();
	public static $predicate	= Array();
	
	/**
	 * Merges all the poperties from the $rel into the $focus
	 * @global Mind $_MIND
	 * @param MindEntity $focus
	 * @param MindEntity $rel 
	 */
	public static function mergeProperties(MindEntity &$focus, MindEntity &$rel)
	{
		GLOBAL $_MIND;
		if($_MIND->conf['use_prefix_on_merged_entities'])
		{
			foreach($rel->properties as $prop)
			{
				$prop->setName($rel->name.PROPERTY_SEPARATOR.$prop->name);
			}
		}
		$focus->properties= array_merge($focus->properties, $rel->properties);
	}
	
	/**
	 * Merges the $rel entity into the $focus entity, mergin
	 * the properties and relations
	 * 
	 * @param MindEntity $focus
	 * @param MindEntity $rel
	 * @return MindEntity 
	 */
	public static function mergeEntities(MindEntity &$focus,
										 MindEntity &$rel,
										 MindRelation &$relation)
	{
		self::mergeProperties($focus, $rel);
		Analyst::unsetRelation($relation->opposite);
		Analyst::unsetRelation($relation);
		Normalizer::redirectRelations($rel, $focus);
		Analyst::removeEntity($rel->name);
		return $focus;
	}
	
	public static function fixOneByOneRelation(MindEntity &$focus,
											   MindEntity &$rel,
											   MindRelation &$relation)
	{
		/*
		 * excluir a relação entre a mais forte e a mais fraca
		 * marcar a fk como pk
		 */
		Analyst::unsetRelation(Analyst::$relations[$rel->name.PROPERTY_SEPARATOR.$focus->name]);
		Analyst::$relations[$focus->name.PROPERTY_SEPARATOR.$rel->name]->uniqueRef= true;
	}
	
	/**
	 * Gets the pontuation amount for the relevance an entity may have.
	 * It takes many directrizes to define how relevant an entity is.
	 * New parameters for such decision can be added here.
	 * 
	 * @global Mind $_MIND
	 * @param MindEntity $entity
	 * @return int 
	 */
	public static function relevanceAmount(MindEntity $entity)
	{
		GLOBAL $_MIND;
		$pts= 0;
		// if the entity has many properties, it might be important
		if(sizeof($entity->properties) > $_MIND->conf['big_entities_length'])
			$pts++;
		// if the entity has many big fields
		if(self::hasBigProperties($entity) > $_MIND->conf['big_fields_in_entity'])
			$pts++;
		if(sizeof($entity->relations) >= $_MIND->conf['relations_length'])
			$pts++;
		return $pts;
	}
	
	/**
	 * Returns an array with the most relevant entity in the first position
	 * and the other entity in the second position
	 * 
	 * @param MindEntity $en1
	 * @param MindEntity $en2
	 * @return Array
	 */
	public static function setByRelevance(MindEntity &$en1, MindEntity &$en2)
	{
		$en1Rlv= $en1->relevance;
		$en2Rlv= $en2->relevance;
		$en1Rlv+= self::relevanceAmount($en1);
		$en2Rlv+= self::relevanceAmount($en2);
		if($en1Rlv == $en2Rlv)
			if(strlen($en1->name) > strlen($en2->name))
				$en1Rlv++;
			else
				$en2Rlv++;
		if($en1Rlv > $en2Rlv)
			return Array($en1, $en2);
		else
			return Array($en2, $en1);
	}
	
	/**
	 * Verifies if the entity has properties classified as big
	 * If yes, returns the number of big properties found, or
	 * false(0) if nog big property has been found
	 * 
	 * @param MindEntity $entity
	 * @return mixed
	 */
	public static function hasBigProperties(MindEntity $entity)
	{
		GLOBAL $_MIND;
		$bigFields= 0;
		foreach($entity->properties as $prop)
			if( $prop->size > $_MIND->conf['big_fields_size'] ||
				$prop->type=='text')
				$bigFields++;
		return $bigFields;
	}
	
	/**
	 * Organizes the relations between entities to each group,
	 * separating by n:n, 1:1 or 1:n
	 */
	public static function separateByRelationQuantifiers()
	{
		reset(Analyst::$relations);
		while($rel= current(Analyst::$relations))
		{
			next(Analyst::$relations);
			
			$relName	 =  $rel->name;
			$relOtherName=  $rel->rel->name.
							PROPERTY_SEPARATOR.
							$rel->focus->name;
			
			if(isset(Analyst::$relations[$relOtherName]))
			{
				$otherRel= &Analyst::$relations[$relOtherName];

				Analyst::$relations[$rel->name]->opposite= 
						&Analyst::$relations[$otherRel->name];
				Analyst::$relations[$otherRel->name]->opposite=
						&Analyst::$relations[$rel->name];
				
				// let's look for n/n relations
				if($otherRel->max=='n' && $rel->max=='n')
				{
					self::$nByN[]= &Analyst::$relations[$rel->name];
				}elseif($otherRel->max==1 && $rel->max==1)
				{ // 1:1 relation
					self::$oneByOne[]= &Analyst::$relations[$rel->name];
				}else{
					// useless re-definition
					// if the relation was already specified as 1:n it doesn't
					// need to be re-specified as n:1
					Analyst::unsetRelation($otherRel);
				}
			}else{
					self::$oneByN[]= &Analyst::$relations[$rel->name];
				 }
		}
	}
}