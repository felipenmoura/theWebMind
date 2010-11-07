<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Canonic
 *
 * @author felipe
 */
class Canonic extends Inflect{

	public static function canonize($word)
	{
		if(!self::is_singular($word))
			$word= self::toSingular($word);
		if(self::isFemale($word))
			$word= self::toMale($word);
		echo $word."<br>";
	}

	public function sweep(Array $content)
	{
		array_map(function ($word)
				  {
					Canonic::canonize($word);
				  },
				  $content);
		print_r($content);
	}
	
    public function __construct()
	{

	}
}
?>
