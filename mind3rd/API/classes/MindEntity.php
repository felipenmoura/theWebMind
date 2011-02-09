<?php
	/**
	 * A facade to deal with attributes/properties and methods
	 * related to an entity
	 *
	 * @author felipe
	 */
	class MindEntity {

		public $name;
		public $properties= Array();
		public $relations= Array();

		public static function isEntity($definition)
		{
			return strpos($definition, ":")? false: true;
		}

		public function addProperty(MindProperty $property)
		{
			$this->properties[$property->name]= $property;
			return $this;
		}

		public function addRef(MindRelation &$rel)
		{
			$this->relations[]= &$rel;
			return $this;
		}

		public function MindEntity($word)
		{
			$this->name= (string)$word;
		}
	}