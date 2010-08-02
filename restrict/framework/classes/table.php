<?php
	include('attribute.php');
	class Table
	{
		public $name;
		public $attributes= Array();
		public $refered= Array();
		public $weight= 0;
		public $foreignKeys= Array();
		public $abstract= false;
		public $extends= false;
		public $DDL= false;
		
		public function addAttribute($att)
		{
			if(is_string($att)) // the name of the attribute
			{
				$this->attributes[$att]= new Attribute($att);
				return $this->attributes[$att];
			}else{	// the attribute object itself, has been received
					$this->attributes[$att->name]= $att;
				 }
			return $this->attributes[$att->name];
		}
		public function removeReference($refName)
		{
			for($i=0; $i<sizeof($this->refered); $i++)
			{
				if(isset($this->refered[$i]))
					if($this->refered[$i]== $refName)
					{
						unset($this->refered[$i]);
						return true;
					}
			}
		}
		public function removeForeignKey($fk)
		{
			GLOBAL $_MIND;
			for($i=0; $i<sizeof($this->foreignKeys); $i++)
			{
				if($this->foreignKeys[$i][1]== $fk)
				{
					unset($this->foreignKeys[$i]);
					return true;
				}
			}
		}
		public function addForeignKey($table, $p= false)
		{
			GLOBAL $_MIND;
			$fkName= ($p)? $p: $_MIND['foreignKeyPrefix'];
			$fkName.= $table;
			//echo $fkName.'<hr/>';
			$this->foreignKeys[]= Array($fkName, $table);
		}
		public function __construct($tName=false)
		{
			if($tName)
				$this->name= $tName;
		}
	}
?>