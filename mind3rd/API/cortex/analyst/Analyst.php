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

		// foreach token
		$i= 0;
		foreach($structureKeys as $token)
		{
			//echo $expression[$i]."<hr>";
			$word= $expression[$i];

			// if it is a substantive
			if($token==Tokenizer::MT_SUBST)
			{
				// if it is an entity
				if(MindEntity::isEntity($word))
				{
					//echo "it is an entity!\n";
				}else{ // otherwise, it must be a property
					$tmpProperty= new MindProperty($word);
					//echo " is a property\n";
				}
			}

			$i++;
		}
		return true;
	}
}