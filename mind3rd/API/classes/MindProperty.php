<?php
	/**
	 * Description of MindProperty
	 *
	 * @author felipe
	 */
	class MindProperty {

		public $definition= "";
		public $name= false;
		public $type= "text";
		public $size= 0;
		public $default= "";
		public $required= false;
		public $refTo= false;
		public $refBy= Array();
		public $key= false;

		public static function isProperty($definition)
		{
			return strpos($definition, ":");
		}

		public static function isKnown($type)
		{
			foreach(Tokenizer::$dataTypes as $k=>$validTypes)
				if(in_array($type, $validTypes))
					return $k;
			return false;
		}

		public static function isRequired($expression)
		{
			$rx= "/".implode('|', Tokenizer::$qualifiers['notnull'])."/i";
			return (preg_match($rx, $expression))? true: false;
		}

		public static function isKey($expression)
		{
			$rx= "/".implode('|', Tokenizer::$qualifiers['key'])."/i";
			return (preg_match($rx, $expression))? true: false;
		}

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
			if(!self::isKnown($this->type))
			{
				//TODO: Darwin::add($this->type);
				echo "UNKNOWN TYPE...for a while";
				return false;
			}

			// identifying the name
			$this->name= substr($str, 0, $typeStart);

			// identifying details
			if(preg_match("/\(.*/", $str, $details))
			{
				$details= $details[0];

				// identifying the default value
				if(preg_match("/\".*\"/", $details, $default))
				{
					$default= $default[0];
					$details= str_replace($default, "", $str, $one);

					// checking if the default value isn't a value or a function call
					if($default[1]=='=' || strtolower(substr($default, 1, 5)) == 'exec:')
					{
						$default= preg_replace("/(^(\"=)|(\"exec\:))|(\"$)/i", "", $default);
					}
					//$default= preg_replace("/(^\")|(\"$)/", "", $default);
					$this->default= $default;
				}

				// identifying if it is required
				if(self::isRequired($details))
					$this->required= true;

				// identifying if it is a forced key
				if(self::isKey($details))
					$this->key= true;
			}
		}
		
		public function MindProperty($definition)
		{
			$this->definition= $definition;
			$this->parse();
			echo json_encode($this)."\n";
		}
	}