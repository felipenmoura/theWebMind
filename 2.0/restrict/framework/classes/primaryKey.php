<?php
	//require_once('attribute.php');
	class PrimaryKey extends Attribute
	{
		private $target= null;
		public  $name= '';
		public  $query= '';
		public  $schemaName= '';
		public function generatePKQuery($b=false, $sgbd)
		{
			$this->query= $this->name;
			return $this->query;
		}
		public function __construct($att, $schemaName)
		{
			$this->target= $target;
			$this->schemaName= $schemaName;
			$this->name= $att->attName;
		}
	}
?>