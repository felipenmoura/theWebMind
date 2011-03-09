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
 * This class is responsible for the analysis of the system
 * preparing the relations and apply the first rules
 *
 * @package Cortex
 * @subpackage Analyst
 * @author  Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
class Analyst extends Analysis {

	/**
	 * Returns true if the entity is worth mergin with another, or not.
	 * It takes the relevance as parameter to determine wheter the entity
	 * should be merged(stop existing) or not.
	 * 
	 * @param MindEntity $en The entity to be analyzed
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
	 * Removes an entity from the entities list.
	 * @param type $entity The entity to be removed
	 */
	public static function removeEntity($entity)
	{
		unset(self::$entities[$entity]);
		unset($entity);
	}
	
	/**
	 * Removes a relation between two entities.
	 * But it will NOT remove the refBy or refTo properties of each entity.
	 * It is most often, useful to be used for relations that may have
	 * been represented twice, as the same idea.
	 * It also decreases the relevance of the focused entity.
	 * 
	 * @param MindRelation &$rel The relation  which will be droped
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
	 * analyzed information
	 * @return Array An array with both entities and relations
	 */
	public static function getUniverse()
	{
		return Array(
						'entities'=>self::$entities,
						'relations'=>self::$relations
					);
	}

	/**
	 * Prints out the analyzed content
	 * @param boolean $detailed Pass true if it should show detailed
	 * information about the entities and properties.
	 */
	public static function printWhatYouGet($detailed=true)
	{
		$props= 0;
		echo "ENTITIES: ".sizeof(self::$entities)."\n";
		foreach(self::$entities as $k=>$entity)
		{
			if($detailed)
			{
				echo "  ".$entity->name.
					 "\n";
				//echo "[".$k."]\n";
			}
			foreach($entity->properties as $prop)
			{
				$details= false;
				$details= Array();
				if($prop->size)
					$details[]= $prop->size;
				if($prop->uinque)
					$details[]= "unique";
				if($prop->key)
					$details[]= "key";
				if($prop->required)
					$details[]= "not null";
				if(!is_null($prop->default) && trim($prop->default) != '')
					$details[]= ($prop->default!= AUTOINCREMENT_DEFVAL)?
									'"'.$prop->default.'"':
									"AUTO_INCREMENT";
				if(sizeof($prop->options) > 0)
				{
					$details[]= "{".implode("|", array_keys($prop->default))."}";
				}	
				
				$props++;
				echo "    ".$prop->name.
					 ":".$prop->type.
					 "(".
						implode(", ", $details).
					 ")".
					 ($prop->refTo? " => ".$prop->refTo->name: "").
					 "\n";
			}
		}
		echo "RELATIONS:".sizeof(self::$relations)."\n";
		foreach(self::$relations as $k=>$rel)
		{
			if(!$rel)
				continue;
			echo "  ".
				 $k.': '.
				 $rel->rel->name.' -> '.
				 $rel->focus->name.
				 ($rel->uniqueRef? "[pk]": "").
				 "\n";
		}
		echo "Properties: ".$props."\n";
		echo "Relations: ".sizeof(self::$relations)."\n";
	}

	/**
	 * Applies normalization rules to the currently analized structure.
	 * This method uses the Normalizer methods and properties to apply
	 * the required rules.
	 */
	public static function normalizeIt()
	{
		Normalizer::normalize();
		self::$relations= array_filter(self::$relations);
		self::$entities= array_filter(self::$entities);
	}

	/**
	 * Reset the properties of the analyst itself.
	 */
	public static function reset()
	{
		self::$entities = false;
		self::$relations= false;
		self::$entities = Array();
		self::$relations= Array();
		self::clearFocused();
	}

	/**
	 * Sweeps through all the matched expressions.
	 * @param Array $matches 
	 */
	public static function sweep($matches)
	{
		// let's clear the Analyst memory as it uses static properties
		self::reset();

		// now we gotta analyze each valid expression
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
