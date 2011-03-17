<?php
	/**
	 * A facade to deal with attributes/properties and methods
	 * related to an entity
	 *
	 * @author felipe
	 */
	class MindEntity {

		public  $name;
		public  $pks= Array();
		public  $relevance= 0;
		public  $properties= Array();
		public  $relations= Array();
		public  $linkTable= false;
		private $refTo= Array();
		private $refBy= Array();

		public function &getRefTo()
		{
			return $this->refTo;
		}
		public function &getRefBy()
		{
			return $this->refBy;
		}
		
		/**
		 * Verifies if the definition describes an entity or not
		 *
		 * @param string $definition
		 * @return boolean
		 */
		public static function isEntity($definition)
		{
			return strpos($definition, ":")? false: true;
		}

		/**
		 * Adds a property to the current entity
		 *
		 * @global $_MIND
		 * @param MindProperty $property
		 * @return MindEntity
		 */
		public function addProperty(MindProperty $property)
		{
			GLOBAL $_MIND;
			//if($property->key && $_MIND->defaults['add_pk_prefix_to_all_keys'])
			//	$property->setName($_MIND->defaults['pk_prefix'].$property->name);
			$this->properties[$property->name]= $property;
			if($property->key)
				$this->pks[$property->name]= $property;
			return $this;
		}

		/**
		 * Verifies if the current entity has a specified property
		 * 
		 * @param string $propName
		 * @return boolean
		 */
		public function hasProperty($propName)
		{
			return isset($this->properties[$propName]);
		}
		
		/**
		 * Removes a property from the current entity
		 * 
		 * @param string $propName
		 * @return boolean
		 */
		public function removeProperty($propName)
		{
			if(isset($this->properties[$propName]))
				unset($this->properties[$propName]);
		}
		
		/**
		 * Defines another entity pointed/refered by this entity
		 * @param MindEntity $ref
		 * @return MindEntity
		 */
		public function addRefTo(MindEntity &$ref)
		{
			$this->refTo[$ref->name]= &$ref;
			return $this;
		}
		
		/**
		 * Removes a reference TO
		 * @param type $refName
		 * @return MindEntity 
		 */
		public function removeRefTo($refName)
		{
			unset($this->refTo[$refName]);
			return $this;
		}

		/**
		 * Specifies that another entity is pointing to this one
		 * @param MindEntity $ref
		 * @return MindEntity
		 */
		public function addRefBy(MindEntity &$ref)
		{
			$this->refBy[$ref->name]= &$ref;
			return $this;
		}
		
		/**
		 * Removes a reference BY
		 * @param type $refName 
		 * @return MindEntity
		 */
		public function removeRefBy($refName)
		{
			unset($this->refBy[$refName]);
			return $this;
		}

		/**
		 * Adds a reference to the current entity
		 *
		 * @param MindRelation $rel
		 * @return MindEntity
		 */
		public function addRef(MindRelation &$rel)
		{
			$this->relations[$rel->name]= &$rel;
			if($rel->focus->name == $this->name)
			{
				if($rel->max == QUANTIFIER_MAX_MAX)
				{
					$this->relevance++;
					$this->addRefBy($rel->rel);
				}else
					$this->addRefTo($rel->rel);
			}else{
					if($rel->max == QUANTIFIER_MAX_MIN)
					{
						$this->relevance++;
						$this->addRefBy($rel->focus);
					}else
						$this->addRefTo($rel->focus);
				 }
			return $this;
		}

		/**
		 * Constructor. It receives the name of the new entity
		 * @param string $word
		 */
		public function MindEntity($word)
		{
			$this->name= (string)$word;
		}
	}