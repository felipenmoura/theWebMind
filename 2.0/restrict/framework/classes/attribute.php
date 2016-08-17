<?php
	class Attribute
	{
		public $name;
		public $type;
		public $defaultValue;
		public $mask;
		public $size;
		public $required;
		public $references;
		
		public function __construct($attName=false)
		{
			if($attName)
				$this->name= $attName;
		}
	}
?>