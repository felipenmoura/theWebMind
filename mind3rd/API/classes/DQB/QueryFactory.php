<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * Notice that, these packages are being used only for documentation,
 * not to organize the classes.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */

namespace DQB;
/**
 * Description of QueryBuilder
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @package DQB
 */
class QueryFactory {
	
	public static $queries   = Array();
	public static $dbms      = Array();
	public static $showHeader= true;
	
	public static function showQueries($decorated=true, $raw= false)
	{
		$closingQueries= Array();
		$outpt= "";
		if(self::$showHeader)
			$outpt= self::getQueryString('getHeader');
		foreach(self::$queries as $qrs)
		{
			foreach($qrs as $qr)
			{
				$outpt.= $qr->query;
				$closingQueries= array_merge($qr->closingQuery, $closingQueries);
			}
		}
		$outpt.= implode("\n    ", $closingQueries);
		if(!$decorated)
			$outpt= strip_tags($outpt);
		if($raw)
			echo htmlentities($outpt);
		else
			echo $outpt;
	}
	
	public static function addQuery($command, Array $table, $template)
	{
		if(!isset(self::$queries[$command]))
			self::$queries[$command]= Array();
		self::$queries[$command][$table['name']]= new Query($command, $table, $template);
		return self::$queries[$command][$table['name']]->query;
	}
	
	public static function getQueryString($command)
	{
		return self::$dbms->getModel($command);
	}
	
	public static function build($command, Array $table)
	{
		$template= self::$dbms->getModel($command);
		return self::addQuery($command, $table, $template);
	}
	
	public static function setUp($dbDriver)
	{
		self::$queries= false;
		self::$queries= Array();
		self::$dbms= @new $dbDriver();
		if(!self::$dbms)
		{
			// TODO: put it into the L10N
			echo "Database Driver not found.";
		}
	}
	
	public function __construct($dbDriver)
	{
		self::setUp($dbDriver);
	}
}