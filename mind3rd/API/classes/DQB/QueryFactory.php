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
class QueryFactory extends TableSort{
	
	public static $queries   = Array();
	public static $dbms      = Array();
	public static $showHeader= true;
	public static $mustSort  = false;
	
	public static function getCompleteQuery($decorated=true, $raw= false, $format='string')
	{
		$closingQueries= Array();
		$outpt= ($format=='string')? "": Array();
		if(self::$showHeader)
		{
			$qr= self::getQueryString('getHeader');
			if(!$decorated)
				$qr= strip_tags($qr);
			if($raw)
				$qr= htmlentities($qr);
			if($format=='string')
				$outpt.= $qr;
			else
				$outpt[]= $qr;
		}
		
		foreach(self::$queries as $qrs)
		{
			foreach($qrs as $qr)
			{
				if($format=='string')
					$outpt.= ($decorated)? $qr->query: strip_tags($qr->query);
				else
					$outpt[]= ($decorated)? $qr->query: strip_tags($qr->query);
				
				$cq= $qr->closingQuery;
				if(!$decorated)
				{
					$cq= array_map(function($arPos){
						return strip_tags($arPos);
					}, $cq);
				}
				if($raw)
				{
					$cq= array_map(function($arPos){
						return htmlentities($arPos);
					}, $cq);
				}
				$closingQueries= array_merge($cq, $closingQueries);
			}
		}
		
		if($format=='string')
		{
			$outpt.= implode("\n    ", $closingQueries);
		}else
			$outpt= array_merge($outpt, $closingQueries);
		if($raw && $format=='string')
			return htmlentities($outpt);
		else
			return $outpt;
	}
	
	public static function showQueries($decorated=true, $raw= false)
	{
		$qr= self::getCompleteQuery($decorated, $raw);
		echo $qr;
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
		self::$mustSort= self::getQueryString('mustSort');
	}
	
	public static function sortTables()
	{
		self::sort(self::$queries['createTable']);
	}
	
	public static function buildQuery($table='*',
									  $queryCommand='createTable')
	{
		$p= new \DAO\ProjectFactory(\Mind::$currentProject);
		$param= ($table=='*')? false: $table;
		$entities= $p->getEntity($param);
		foreach($entities as $entity)
		{
			$entity['properties']= $p->getProperties($entity);
			\DQB\QueryFactory::build($queryCommand, $entity);
		}
		if(self::$mustSort)
		{
			self::sortTables();
		}
		return self::$queries;
	}
	
	public function __construct($dbDriver)
	{
		self::setUp($dbDriver);
	}
}