<?php
	//require_once('table.php');
	//require_once('dataBase.php');
	class Schema extends dataBase
	{
		public $tables= Array();
		public $name= 'public';
		public $description= '';
		private $query= '';
		
		public function generateSchemaQuery($b=false, $sgbd) // se $b==true escreve conteudo HTML
		{
			$written= Array();
			$this->query= '';
			if($this->name != 'public')
			{
				/*$this->query.= "CREATE SCHEMA ".$this->name.";
";*/
				$this->query.= str_replace('<schemaname>', $this->name, $sgbd->execute->createSchema());
			}
			foreach($this->getTables() as $table)
			{
				if(!in_array($table->tableName, $written))
				{
					$this->query.= $table->generateTableQuery($this->name, $sgbd, $b, $color);
					$written[]= $table->getTableName();
				}
			}
			$written= null;
			$written= Array();
			if($b)
				$this->query.= '</pre>';
			return $this->query;
		}
		public function addTable($name, $b= false, $SGBD= false)
		{
			$this->tables[$name]= new Table($this->name, $name, $b, $SGBD);
			return $this->tables[$name];
		}
		public function getTables()
		{
			return $this->tables;
		}
		public function __construct($name)
		{
			$this->name= $name;
		}
	}
?>