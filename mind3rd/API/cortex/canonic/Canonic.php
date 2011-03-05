<?php
/**
 * This class will take the content to be used, and take
 * it to its canonical form.
 * It extends the Inflect class, from the selected language
 *
 * @author felipe
 */
class Canonic{

	public static $substantives= Array();
	
	/**
	 * Takes a word to its canonic form(singular/male form)
	 * @param string$word
	 * @return string
	 */
	public static function canonize($word)
	{
		$inflec= Mind::$currentProject['idiom']."\Inflect";
		if(!$inflec::isSingular($word))
			$word= $inflec::toSingular($word);
		return $word;
	}

	/**
	 * Brings all the words in the Array to their canonical form
	 * @param array $content
	 * @return Array
	 */
	public function sweep()
	{
		self::$substantives= false;
		self::$substantives= Array();
		$content= Mind::$content;
		$newContent= Array();

		Mind::$tokenizer= new Tokenizer();
		$ignoreForms= Mind::$currentProject['idiom'].'\IgnoreForms';
		$verbalizer= Mind::$currentProject['idiom'].'\Verbalizer';
		$posVerb= false;
		
		foreach($content as $word)
		{
			if($ignoreForms::shouldBeIgnored($word))
				continue;
			if(!Tokenizer::isQualifier($word) && !Tokenizer::isQuantifier($word))
			{
				if( strlen($word) > 1
						&&
					!isset(self::$substantives[$word])
						&&
					($isVerb= $verbalizer::isVerb($word))
				  )
				{
					// is a verb
					$word= $verbalizer::toInfinitive($word);
				}
				else{
					// is a substantive
					$word= explode(':', $word);
					$word[0]= Canonic::canonize($word[0]);
					self::$substantives[$word[0]]= true;
					$word= implode(':', $word);
				}
			}
			$newContent[]= $word;
		}
		Mind::$content= $newContent;
		return $newContent;
	}
}