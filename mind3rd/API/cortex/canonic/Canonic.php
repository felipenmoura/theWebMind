<?php
/**
 * This class will take the content to be used, and take
 * it to its canonical form.
 * It extends the Inflect class, from the selected language
 *
 * @author felipe
 */
class Canonic{

	/**
	 * Takes a word to its canonic form(singular/male form)
	 * @param string$word
	 * @return string
	 */
	public static function canonize($word)
	{
		$inflec= Mind::$currentProject['idiom']."\Inflect";
		if(!$inflec::isSingular($word)) /* TODO: fix it*/
			$word= $inflec::toSingular($word);
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

		// PAREI AQUI
			//print_r(Mind::$content);
			//echo "\ncanonic--------------------------\n";

		Mind::$tokenizer= new Tokenizer();
		$ignoreForms= Mind::$currentProject['idiom'].'\IgnoreForms';
		$verbalizer= Mind::$currentProject['idiom'].'\Verbalizer';

		foreach($content as $word)
		{
			if($ignoreForms::shouldBeIgnored($word))
				continue;
			if(!Tokenizer::isQualifier($word) && !Tokenizer::isQuantifier($word))
			{
				if(strlen($word) > 1 && ($isVerb= $verbalizer::isVerb($word)))
					$word= $verbalizer::toInfinitive($word);
				else{
					$word= explode(':', $word);
					$word[0]= Canonic::canonize($word[0]);
					$word= implode(':', $word);
				}
			}
			$newContent[]= $word;
		}
		Mind::$content= $newContent;
		return $newContent;
	}
}