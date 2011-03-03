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
				echo $rel->name." - from: ".$from->name." - focus: ".$rel->focus->name.' - rel: '.$rel->rel->name."\n";
				if($rel->focus->name == $from->name)
				{
					echo "foco igual ao from";
					$rel->setFocus($to);
					$rel->rename($rel->focus->name.PROPERTY_SEPARATOR.$rel->rel->name);
				}else{
						echo "rel igual ao from";
						$rel->setRel($to);
						$rel->rename($rel->rel->name.PROPERTY_SEPARATOR.$rel->focus->name);
					 }
			}
		}
		
		public static function fixOneByOneRel()
		{
			if(sizeof(self::$oneByOne) == 0)
				return;
			reset(self::$oneByOne);
			$rel= next(self::$oneByOne);
			do
			{
				$rel= &Analyst::$relations[$rel->name];
				//next(self::$oneByOne);
				
				// defining the focus
				self::setByRelevance($rel->focus, $rel->rel);
				//$predicate= &Analyst::$relations[$focus[1]->name]; // less relevant
				//$focus	  = &Analyst::$relations[$focus[0]->name]; // more relevant
				
				// let's check the minimun quantifiers
				//echo $focus->name.'---'.$predicate->name;
				if($rel->min== 1 && $rel->opposite->min == 1)
				{ // for 1:1 / 1:1 relations
					self::mergeEntities(self::$focus, self::$predicate, $rel);
					//Analyst::unsetRelation($rel->opposite);
				}elseif($rel->min== 0 && $rel->opposite->min == 0)
				{ // for 0:1 / 0:1 relations
					
				}else{
						
					 }
			}while($rel= next(self::$oneByOne));
		}
		
		public static function fixNByNRel()
		{
			
		}
		
		public static function normalize()
		{
			self::separateByRelationQuantifiers(); // ok
			self::fixOneByOneRel();
			self::fixNByNRel();
		}
	}