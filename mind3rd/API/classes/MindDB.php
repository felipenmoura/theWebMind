<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
/**
 * Basic abstraction of the SQLite layer
 *
 * @author felipe
 */
class MindDB {
    
	public static $db= null;
	public static $inTransaction= false;
	public $lastInsertedId= 0;

    public function __get($what)
    {
        if($what=='db')
            return self::$db;
        if(isset($this->$what))
            return $this->$what;
        return false;
    }
    
	/**
	 * @method query
	 * @param String $qr
	 * @return Mixed
	 */
	public function query($qr)
	{
		$ret= self::$db->query($qr);
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
        if(strtoupper($command) == 'BEGIN')
        {
            if(self::$inTransaction)
               return true;
            self::$inTransaction= true;
        }elseif(strtoupper($command) == 'COMMIT' || strtoupper($command) == 'ROLLBACK')
            {
                self::$inTransaction= false;
            }
		$ret= self::$db->exec($command);
		$this->lastInsertedId= self::$db->lastInsertRowId();
		return $this->lastInsertedId;
	}

    public function  __construct()
	{
        if(!self::$db)  
            if(!self::$db = new SQLite3(_MINDSRC_.SQLITE))
            {
                Mind::message('Database', '[Fail]');
                return false;
            }
        
		return $this;
	}
}