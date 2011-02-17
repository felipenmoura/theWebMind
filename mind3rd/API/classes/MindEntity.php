<?php
	/**
	 * A facade to deal with attributes/properties and methods
	 * related to an entity
	 *
	 * @author felipe
	 */
	class MindEntity {

		public $name;
		public $relevance;
		public $properties= Array();
		public $relations= Array();

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
		 * Adds a reference to the current entity
		 *
		 * @param MindRelation $rel
		 * @return MindEntity
		 */
		public function addRef(MindRelation &$rel)
		{
			$this->relations[]= &$rel;
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