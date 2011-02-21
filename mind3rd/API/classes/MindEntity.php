<?php
	/**
	 * A facade to deal with attributes/properties and methods
	 * related to an entity
	 *
	 * @author felipe
	 */
	class MindEntity {

		public $name;
		public $relevance= 0;
		public $properties= Array();
		public $relations= Array();
		private $refTo= Array();
		private $refBy= Array();

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
		 * @param MindProperty $property
		 * @return MindEntity
		 */
		public function addProperty(MindProperty $property)
		{
			$this->properties[$property->name]= $property;
			return $this;
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
		 * Adds a reference to the current entity
		 *
		 * @param MindRelation $rel
		 * @return MindEntity
		 */
		public function addRef(MindRelation &$rel)
		{
			$this->relations[]= &$rel;
			if($rel->focus->name == $this->name)
			{
				if($rel->max == QUANTIFIER_MAX_MAX)
				{
					//echo $rel->max;
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