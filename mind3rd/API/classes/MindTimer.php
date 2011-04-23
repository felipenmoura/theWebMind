<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
/**
 * This is a time controller, to deal with processes time.
 *
 * @author felipe
 * 
 */
abstract class MindTimer {
	
	public static $startingTime= 0;
	public static $endingTime  = 0;
	
	public static function getElapsedTime()
	{
		return number_format(((float)self::$endingTime) - ((float)self::$startingTime), 4);
	}
	
	public static function init()
	{
		$startingTime= microtime();
		$startingTime= explode(' ', $startingTime);
		$startingTime= $startingTime[1] + $startingTime[0];
		self::$startingTime= $startingTime;
	}
	public static function end()
	{
		$endingTime= microtime();
		$endingTime= explode(' ', $endingTime);
		$endingTime= $endingTime[1] + $endingTime[0];
		self::$endingTime= $endingTime;
	}
}