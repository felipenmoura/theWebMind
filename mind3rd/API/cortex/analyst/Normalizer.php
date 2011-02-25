<?php
	/**
	 * Will normalize the data and entities structure applying
	 * rules and patterns. Thanks for Edgar F. Codd for all he
	 * created and wondered for the Relational Model
	 *
	 * @author felipe
	 */
	class Normalizer {

		public static $tmpEntities= Array();
		public static $tmpRelations= Array();

		public static function fixOneByOneRel()
		{
			
		}
		
		public static function fixNByNRel()
		{
			
		}
		
		public static function normalize()
		{
			self::fixOneByOneRel();
			self::fixNByNRel();
		}
	}