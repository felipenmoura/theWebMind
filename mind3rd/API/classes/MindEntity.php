<?php
	/**
	 * A facade to deal with attributes/properties and methods
	 * related to an entity
	 *
	 * @author felipe
	 */
	class MindEntity {

		public static function isEntity($definition)
		{
			return strpos($definition, ":")? false: true;
		}

		public function MindEntity()
		{
		}
	}