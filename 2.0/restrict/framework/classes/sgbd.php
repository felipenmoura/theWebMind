<?php
	interface sgbdInterface
	{
		/*public function newPK(); // must return the value to be inserted on Primary Keys
		public function insert(); // 
		public function update(); // 
		public function select(); // 
		public function delete(); // 
		public function create(); // */
	}
	class SGBD// implements sgbdInterface
	{
		public $name= '';
		public $sqlBase= '';
		public $execute= '';
		
		public function __construct($SGBD)
		{
			$this->name= $SGBD;
			include('sgbds/'.$SGBD.'.php');
			$this->execute= new $SGBD();
		}
		public function getSGBD()
		{
			return $this->execute;
		}
	}
?>