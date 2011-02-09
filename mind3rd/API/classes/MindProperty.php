<?php
	/**
	 * Represents the property of an entity(MindEntity::properties)
	 *
	 * @author felipe
	 */
	class MindProperty {

		public $definition= "";
		public $name= false;
		public $type= "text";
		public $size= 0;
		public $default= null;
		public $required= false;
		public $refTo= false;
		public $refBy= Array();
		public $key= false;

		/**
		 * Checks if the string sent indicates that it is about a property
		 * @param String $definition
		 * @return boolean
		 */
		public static function isProperty($definition)
		{
			return strpos($definition, ":")? true: false;
		}

		/**
		 * Returns if the passed property type is known by the system.
		 * If it is not known, then it stores it as a doubt
		 *
		 * @param String $type
		 * @return boolean
		 */
		public static function isKnown($type)
		{
			foreach(Tokenizer::$dataTypes as $k=>$validTypes)
				if(in_array($type, $validTypes))
					return $k;
			return false;
		}

		/**
		 * Verifies on the passed string, if it indicates that the current
		 * property definition should be required or not
		 *
		 * @param String $expression
		 * @return boolean
		 */
		public static function isRequired($expression)
		{
			$rx= "/".implode('|', Tokenizer::$qualifiers['notnull'])."/i";
			return (preg_match($rx, $expression))? true: false;
		}

		/**
		 * Returns true in case the passed expression represent a property
		 * definition, which has terms saying it should(forcedly) treated as
		 * a key
		 *
		 * @param String $expression
		 * @return boolean
		 */
		public static function isKey($expression)
		{
			$rx= "/".implode('|', Tokenizer::$qualifiers['key'])."/i";
			return (preg_match($rx, $expression))? true: false;
		}

		/**
		 * This method parses the current property definition due to
		 * identify its specification, like size, type or if it is
		 * not null
		 *
		 * @return boolean
		 */
		private function parse()
		{
			$str= $this->definition;
			$one= 1;

			// identifying the data type
			$typeStart= strpos($str, ':')+1;
			$typeEnd= strpos($str, '(');
			$typeEnd= $typeEnd? $typeEnd- $typeStart: strLen($str);
			$type= substr($str, $typeStart, $typeEnd);
			$this->type= $type;
			$tmpType= self::isKnown($this->type);
			if(!$tmpType)
			{
				//TODO: Darwin::add($this->type);
				echo "UNKNOWN TYPE...for a while";
				return false;
			}else
				$type= $tmpType;

			// identifying the name
			$this->name= substr($str, 0, $typeStart);

			// identifying details
			if(preg_match(PROP_DETAILS, $str, $details))
			{
				$details= $details[0];

				// identifying the default value
				if(preg_match(PROP_DEFAULT, $details, $default))
				{
					$default= $default[0];
					$details= str_replace($default, "", $str, $one);

					// checking if the default value isn't a value or a function call
					if($default[1]=='=' || strtolower(substr($default, 1, 5)) == EXEC_STRING)
					{
						$default= preg_replace(PROP_DEFEXEC, "", $default);
					}
					
					$this->default= $default;
				}

				// identifying if it is required
				if(self::isRequired($details))
					$this->required= true;

				// identifying if it is a forced key
				if(self::isKey($details))
					$this->key= true;

				// identifying its size
				if(preg_match(PROP_SIZE, $details, $size))
					$this->size= $size[0];
			}
			return true;
		}

		/**
		 * The constructor itself
		 * IT receives by paramether, the definition string, to parse the
		 * property's characteristics and store it on itself
		 *
		 * @param String $definition
		 */
		public function MindProperty($definition)
		{
			$this->definition= $definition;
			$this->parse();
			//echo json_encode($this)."\n";
		}
	}