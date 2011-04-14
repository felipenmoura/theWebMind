<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Basic abstraction of the SQLite layer
 *
 * @author felipe
 */
class MindDB {
	private $db= null;
	public $lastInsertedId= 0;

	/**
	 * @method query
	 * @param String $qr
	 * @return Mixed
	 */
	public function query($qr)
	{
		$ret= $this->db->query($qr);
		$ar_ret= Array();
		while($tuple= $ret->fetchArray(SQLITE3_ASSOC))
		{
			$ar_ret[]= $tuple;
		}
		return $ar_ret;
	}

	/**
	 * Performs a command into the database
	 * @param String $command
	 * @return Int
	 */
	public function execute($command)
	{
		$ret= $this->db->exec($command);
		$this->lastInsertedId= $this->db->lastInsertRowId();
		return $this->lastInsertedId;
	}

    public function  __construct()
	{
		if(!$db = new SQLite3(_MINDSRC_.SQLITE))
		{
			Mind::message('Database', '[Fail]');
			return false;
		}
		$this->db= $db;
		return $this;
	}
}
?>
