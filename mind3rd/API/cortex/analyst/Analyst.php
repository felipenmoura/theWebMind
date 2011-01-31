<?php
/**
 * This class is responsable to the analisys of the system
 * preparing the relations and apply the first rules
 *
 * @author felipe
 */
class Analyst {

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
    public static function analize($expression, $structure, $structureKeys){

		// let's simply print it as a message, by now
		echo implode(' ', $expression).'-'.$structure.'-'.implode('|', $structureKeys).'<br/>';
		
		return true;
	}
}