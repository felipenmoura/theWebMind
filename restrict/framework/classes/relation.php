<?php
	class Relation
	{
		public $name;
		public $leftTable;
		public $rightTable;
		
		public function __construct($rName= false)
		{
			if($rName)
				$this->name= $rName;
			if(strpos($rName, '|'))
			{
				$this->leftTable= explode('|', $rName);
				$this->rightTable= $this->leftTable[1];
				$this->leftTable= $this->leftTable[0];
			}
		}
	}
?>