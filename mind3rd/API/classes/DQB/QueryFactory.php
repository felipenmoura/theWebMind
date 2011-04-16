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
	
	public static function addQuery($command, Array $table, $template, $raw= false)
	{
		if(!isset(self::$queries[$command]))
			self::$queries[$command]= Array();
		self::$queries[$command][$table['name']]= new Query($command, $table, $template);
        
        $ret= self::$queries[$command][$table['name']]->query;
        if($raw)
        {
            self::$queries[$command][$table['name']]->query= strip_tags($ret);
            $ret= strip_tags($ret);
        }
		return $ret;
	}
	
	public static function getQueryString($command, $raw= false)
	{
		$ret= self::$dbms->getModel($command);
        if($raw)
            $ret= strip_tags($ret);
        return $ret;
	}
	
	public static function build($command, Array $table, $raw= false)
	{
		$template= self::$dbms->getModel($command);
		return self::addQuery($command, $table, $template, $raw);
	}
	
	public static function setUp($dbDriver)
	{
		self::$queries= false;
		self::$queries= Array();
		self::$dbms= @new $dbDriver();
		if(!self::$dbms)
		{
            \Mind::write('dbDriverNotFound');
		}
		self::$mustSort= self::getQueryString('mustSort');
	}
	
	public static function sortTables()
	{
		self::sort(self::$queries['createTable']);
	}
	
    public static function buildRawQuery($table='*',
									     $queryCommand='createTable')
    {
        return self::buildQuery($table='*', $queryCommand='createTable', true);
    }
    
	public static function buildQuery($table='*',
									  $queryCommand='createTable',
                                      $raw=false)
	{
		$p= new \DAO\ProjectFactory(\Mind::$currentProject);
		$param= ($table=='*')? false: $table;
		$entities= $p->getEntity($param);
		foreach($entities as $entity)
		{
			$entity['properties']= $p->getProperties($entity);
			\DQB\QueryFactory::build($queryCommand, $entity, $raw);
		}
		if(self::$mustSort)
		{
			self::sortTables();
		}
		return self::$queries;
	}
    
    public static function getAllTables()
    {
        return self::$dbms->getModel('getAllTables');
    }
	
	public function __construct($dbDriver)
	{
		self::setUp($dbDriver);
	}
}