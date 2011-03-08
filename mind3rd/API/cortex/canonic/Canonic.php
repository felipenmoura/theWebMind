<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * Canonic, within the Cortex/Canonic packages.<br/>
 * Notice that, these packages are being used only for documentation,
 * not to organize the classes.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
/**
 * This class will take the content to be used, and take
 * it to its canonical form.
 * It extends the Inflect class, from the selected language
 *
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @package Cortex
 * @subpackage Canonic
 */
class Canonic{

	public static $substantives= Array();
	
	/**
	 * Takes a word to its canonic form(singular/male form).
	 * @param string $word
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
	 * Sweeps all the content and take all the substantives
	 * to their canonical form.
	 * 
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