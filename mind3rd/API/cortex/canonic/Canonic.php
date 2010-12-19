<?php
/**
 * This class will take the content to be used, and take
 * it to its canonical form
 * @package cortex.analyst
 * @author felipe
 */
class Canonic extends Inflect{

	/**
	 * Takes a word to its canonic form
	 * @param string$word
	 * @return string
	 */
	public static function canonize($word)
	{
		if(!self::isSingular($word))
			$word= self::toSingular($word);
		if(self::isFemale($word))
			$word= self::toMale($word);
		return $word;
	}

	/**
	 * Brings all the words in the Array to their canonical form
	 * @param array $content
	 * @return Array
	 */
	public function sweep(Array $content)
	{
		$v= new Verbalizer;
		$newContent= Array();
		foreach($content as $word)
		{
			if(!Verbalizer::isVerb($word) && strlen($word) > 1)
			{
				//echo 'the canonic form of '.$word.' is '.Canonic::canonize($word);
				$word= Canonic::canonize($word);
			}
			$newContent[]= $word;
		}
		/*array_map(function ($word)
				  {
					print_r($v);
					//echo Verbalizer::toInfinitive($word);
					//if(Verbalizer::isVerb($word))
					//return Verbalizer::toInfinitive($word);
					Canonic::canonize($word);
				  },
				  $content);
		*/
		print_r($newContent);
		return $newContent;
	}
	
    public function __construct()
	{

	}
}
?>
