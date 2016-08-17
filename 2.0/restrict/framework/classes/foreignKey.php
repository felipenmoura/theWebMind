<?php
	//require_once('attribute.php');
	class ForeignKey extends Attribute
	{
		public  $target= null;
		public  $name= '';
		public  $query= '';
		public  $schemaName= '';
		public function generateFKQuery($b=false, $sgbd)
		{
			$this->query= str_replace('<foreignkey>', $this->name, $sgbd->execute->createFK());
			$this->query= str_replace('<schemaname>', $this->schemaName, $this->query);
			$this->query= str_replace('<references>', $this->target, $this->query);
			$this->query= str_replace('<primarykeys>', 'pk_'.$this->target, $this->query);
			return $this->query;
		}
		public function __construct($att, $target, $schemaName)
		{
			$this->target= $target;
			$this->schemaName= $schemaName;
			$this->name= $att->attName;
			//echo $this->target;
			//echo ' > '.$att->attName.' < ';
		}
	}
?>