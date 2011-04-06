<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MindDBAL
 *
 * @author felipe
 */
class MindDBAL {
	
	private     $name        = "";
	private     $dsn         = false;
	private     $user        = "";
	private     $pwd         = "";
	private     $driver      = "";
	private     $dbName      = "";
	private     $host        = "";
	private     $port        = false;
	protected   $conn        = "";
	protected   $DBNamespace = "";
	public      $drivers     = "";

	public function __set($what, $value)
	{
		if(isset($this->$what))
		{
			$this->$what= $value;
			return $this;
		}
		//throw new MindDBALException("Failed to set a value. Inexistent property ".$what);
	}
	
	private function mountDSN()
	{
		$dsn = $this->driver.":dbname=".$this->dbName.";";
		$dsn.= "host=".$this->host;
		if($this->port)
			$dsn.";port=".$this->port;
		$this->dsn= $dsn;
	}
	
	public function init()
	{
		if(!$this->dsn)
		{
			$this->mountDSN();
		}
		$this->conn= new \PDO($this->dsn, $this->user, $this->pwd);
		
		return $this;
	}

	public function begin()
	{
		return $this->conn->beginTransaction();
	}
	
	public function rollBack()
	{
		return $this->conn->rollback();
	}
	
	public function commit()
	{
		return $this->conn->commit();
	}
	
	public function execute($qr)
	{
		return $this->conn->exec($qr);
	}
	
	public function __construct($dsn=false)
	{
		if($dsn)
		{
			if(is_array($dsn))
			{
				foreach($dsn as $key=>$data)
				{
					if(isset($this->$key))
						$this->$key= $data;
				}
				$this->init();
			}else{
					$this->dsn= $dsn;
					$this->init();
				 }
		}
	}
}