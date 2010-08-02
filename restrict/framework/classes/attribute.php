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
		public $pk;
		public $hidden=false;
		
		public function __construct($attName=false)
		{
			if($attName)
				$this->name= $attName;
			$this->pk=false;
		}
	}
?>