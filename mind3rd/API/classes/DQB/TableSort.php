<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DQB;
/**
 * Description of TableSort
 *
 * @author felipe
 */
abstract class TableSort {
	
	public static $original= false;
	public static $ordered= false;
	public static $unordered= false;
	public static $aux= false;
	
	private static function tableRefsTo($table)
	{
		foreach($table['properties'] as $prop)
		{
			if($prop['ref_to_property'])
			{
				return true;
			}
		}
		return false;
	}
	
	private static function doSort()
	{
		foreach(self::$unordered as $k=>$query)
		{
			if(!self::tableRefsTo($query->table))
			{
				self::$ordered[]= $query;
				unset(self::$unordered[$k]);
			}// TODO: order the other tables
		}
		return self::$ordered;
	}


	public static function sort(Array &$queries)
	{
		self::$unordered= self::$original= $queries;
		$ordered= Array();
		$queries= self::doSort();
	}
}