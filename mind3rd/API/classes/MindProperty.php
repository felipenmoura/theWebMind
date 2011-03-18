<?php
	/**
	 * Represents the property of an entity(MindEntity::properties)
	 *
	 * @author felipe
	 */
	class MindProperty {

		public  $definition= "";
		private $name      = false;
		private $type      = "text";
		private $size      = 0;
		private $options   = Array();
		private $default   = null;
		private $unique    = false;
		private $required  = false;
		public  $refTo     = false;
		public  $refBy     = Array();
		public  $key       = false;
		public  $comment   = false;

		/**
		 * Sets the entity and property the current property is referred to.
		 * @param MindEntity $entity
		 * @param MindProperty $prop 
		 * @return MindProperty
		 */
		public function setRefTo(MindEntity $entity, MindProperty $prop)
		{
			$this->refTo= Array($entity, $prop);
			return $this;
		}
		
		/**
		 * Return all the properties
		 * @param string $what
		 * @return mixed
		 */
		public function __get($what)
		{
			if(isset($this->$what))
				return $this->$what;
			return false;
		}
		
		/**
		 * Set name
		 * @param string $val
		 * @return MindProperty 
		 */
		public function setName($val)
		{
			$this->name= (string)$val;
			return $this;
		}
		
		/**
		 * Set size
		 * @param int/float $val
		 * @return MindProperty 
		 */
		public function setSize($val)
		{
			$this->size= $val;
			return $this;
		}
		
		/**
		 * Set Options
		 * @param Array $val
		 * @return MindProperty 
		 */
		public function setOptions($val)
		{
			$this->options= $val;
			return $this;
		}
		
		/**
		 * Set type
		 * @param string $val
		 * @return MindProperty 
		 */
		public function setType($val)
		{
			$this->type= (string)$val;
			$tmpType= self::isKnown($this->type);
			if(!$tmpType)
			{
				//TODO: Darwin::add($this->type);
				Darwin::addDoubt($this->type, 'dataType');
				return false;
			}
			return $this;
		}
		
		/**
		 * Set Default value
		 * @param string $val
		 * @return MindProperty 
		 */
		public function setDefault($val)
		{
			$this->default= (string)$val;
			return $this;
		}
		
		/**
		 * Set as unique or not
		 * @param boolean $val
		 * @return MindProperty 
		 */
		public function setUnique($val)
		{
			$this->unique= $val? true: false;
			return $this;
		}
		
		/**
		 * Set wheter the property is required(not null) or not
		 * @param boolean $val
		 * @return MindProperty 
		 */
		public function setRequired($val)
		{
			$this->required= $val? true: false;
			return $this;
		}
		
		/**
		 * Set the property as a key
		 * @return MindProperty 
		 */
		public function setAsKey()
		{
			$this->key= true;
			return $this;
		}
		
		/**
		 * Set the property as a weak key
		 * @return MindProperty 
		 */
		public function setAsWeakKey()
		{
			$this->key= 'weak';
			return $this;
		}
				
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
		 * Verifies on the passed string if it indicates that the current
		 * property definition should be unique or not
		 *
		 * @param String $expression
		 * @return boolean
		 */
		public static function isUnique($expression)
		{
			$rx= "/".implode('|', Tokenizer::$qualifiers['unique'])."/i";
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
			$this->setType($type);

			// identifying the name
			$this->setName(Mind::$lexer->fixWordChars(substr($str, 0, $typeStart-1)));

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
					
					$this->setDefault($default);
				}

				// identifying if it is required
				if(self::isRequired($details))
					$this->setRequired(true);

				// identifying if it is a forced key
				if(self::isKey($details))
					$this->setAsKey();

				// identifying its size
				if(preg_match(PROP_SIZE, $details, $size))
					$this->setSize($size[0]);

				// identifying the options
				if(preg_match(PROP_OPTIONS, $details, $options))
				{
					$options= explode('|',
								preg_replace(PROP_OPTIONS_CLEAR,
											'',
											$options[0])
							);
					foreach($options as &$opt)
					{
						$opt= explode('=', $opt, 2);
					}
					$this->setOptions($options);
				}
				
				// checking if it is unique
				if(self::isUnique($details))
				{
					$this->setUnique(true);
				}
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
		public function MindProperty($definition=false)
		{
			if($definition)
			{
				$this->definition= $definition;
				$this->parse();
			}
		}
	}