<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */

namespace DQB;
/**
 * Description of TableSort
 *
 * @author felipe
 */
abstract class TableSort {
	
	public static $original= false;
	public static $ordered= Array();
	public static $unordered= Array();
	public static $aux= false;
	
	private static function tableRefsTo(&$table)
	{
		$ar= Array();
		if(!isset($table['ref_to']))
		{
			foreach($table['properties'] as $prop)
			{
				if($prop['ref_to_property'])
				{
					$ref= explode('.', $prop['ref_to_property']);
					$ar[]= $ref[0];
				}
			}
			if(sizeof($ar) > 0)
			{
				$table['ref_to']= $ar;
				return $ar;
			}
		}else
			return $table['ref_to'];
		return false;
	}
	
	private static function addToOrderedList($qrKey)
	{
		$qr= self::$unordered[$qrKey];
		self::$ordered[$qr->table['name']]= $qr;
		unset(self::$unordered[$qrKey]);
	}
	
	private static function isOrdered($qrKey)
	{
		$qr= self::$unordered[$qrKey];
		foreach($qr->table['ref_to'] as $ref)
		{
			if(!isset(self::$ordered[$ref]))
				return false;
		}
		return true;
	}
	
	private static function doSort()
	{
		foreach(self::$unordered as $k=>$query)
		{
			// if the table does not referres to anyone, it can
			// be added to the ordered list
			if(!self::tableRefsTo($query->table))
			{
				self::addToOrderedList($k);
			}else{
					if(self::isOrdered($k))
					{
						self::addToOrderedList($k);
					}
				 }
		}
		if(sizeof(self::$unordered)>0)
			self::doSort();
		return self::$ordered;
	}


	public static function sort(Array &$queries)
	{
		self::$unordered= self::$original= $queries;
		$ordered= Array();
		$queries= self::doSort();
	}
}