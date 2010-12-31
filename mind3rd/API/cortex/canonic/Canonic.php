<?php
/**
 * This class will take the content to be used, and take
 * it to its canonical form.
 * It extends the Inflect class, from the selected language
 *
 * @package cortex.analyst
 * @author felipe
 */
class Canonic extends Inflect{

	/**
	 * Takes a word to its canonic form(singular, male form)
	 * @param string$word
	 * @return string
	 */
	public static function canonize($word)
	{
		if(!self::isSingular($word))
			$word= self::toSingular($word);
		/*if(self::isFemale($word))			// demands more tests
		// apparently, female substantives are brought to a wrong male form
			$word= self::toMale($word);*/
		return $word;
	}

	/**
	 * Brings all the words in the Array to their canonical form
	 * @param array $content
	 * @return Array
	 */
	public function sweep()
	{
		$content= Mind::$content;
		$newContent= Array();

		Mind::$tokenizer= new Tokenizer();

		foreach($content as $word)
		{
			if(IgnoreForms::shouldBeIgnored($word))
				continue;
			if(!Tokenizer::isQualifier($word) && !Tokenizer::isQuantifier($word))
			{
				if(strlen($word) > 1 && ($isVerb= Verbalizer::isVerb($word)))
					$word= Verbalizer::toInfinitive($word);
				else
					$word= Canonic::canonize($word);
			}
			$newContent[]= $word;
		}
		Mind::$content= $newContent;
		return $newContent;
	}
}
?>
