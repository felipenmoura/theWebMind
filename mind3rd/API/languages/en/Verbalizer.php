<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
namespace en;
/**
 * This class should identify verbs
 * NOTICE that its goals is about the present/future words...
 * past is not supported
 * @package cortex.analyst
 * @author felipe
 */
class Verbalizer {
	/**
	 * @var AssocArray $verbs - The associative list of verbs
	 * @static $verbs
	 */
	public static $verbs= false;

	/**
	 * @var AssocArray An array of verbs that may indicate a possibility
	 */
	static $possibilities= Array(
		'can',
		'may',
		'maybe',
		'possibly',
		'probably'
	);

	/**
	 * @static
	 * @var AssocArray the possible flections a verb may suffer
	 */
    static $flections = array(
		'/ies$/'							=> 'y',
		'/es$/'								=> 's',
		'/es$/'								=> '',
		'/s$/'								=> ''
	);

	/**
	 * Words that should NOT be changed
	 * @static
	 * @var AssocArray A list of fixed exceptions
	 */
	static $exceptions= Array(
		'has'								=> 'have',
		'have'								=> 'has',
		'be'								=> 'be'
	);

	/**
	 * Returns if the passed word means the verb is a possibility
	 * @param string $word
	 * @static
	 * @return boolean
	 */
	public static function isAPosibility($string)
	{
		return in_array($string, self::$possibilities);
	}

	/**
	 * Returns if the passed word is in the infinitive form
	 * @param string $word
	 * @static
	 * @return boolean
	 */
	public static function isInfinitive($string)
	{
		$isInVerBlist= self::isInVerbList($string);
		if($isInVerBlist)
			return true;
		/*
		foreach(self::$flections as $pattern=>$result)
		{
			if(preg_match('/'.$result.'$/i', $string) && $isInVerBlist)
				return true;
		}*/
		return false;
	}

	/**
	 * Tries to bring any word to its infinitive form
	 * Returns false if it is not in the verbs.list
	 * @param string $word
	 * @return string
	 * @static
	 */
	public static function toInfinitive($string)
    {
		$string= strtolower($string);
		if(self::isInfinitive($string))
			return $string;
		
        if(isset(self::$exceptions[$string]))
		{
			return self::$exceptions[$string];
		}
		if(self::isAPosibility($string))
		{
			return $string;
		}

        foreach(self::$flections as $pattern=>$result)
        {
            $pattern= $pattern.'i';
            if(preg_match($pattern, $string))
			{
				$tmpWord= preg_replace($pattern, $result, $string);
				if(self::isInVerbList($tmpWord))
					return $tmpWord;
			}
        }
		return false;
    }

	/**
	 * Verifies if the passed word is in the verbs.list
	 * @param string $word
	 * @return boolean
	 * @static
	 */
	public static function isInVerbList($string)
	{
		if(!self::$verbs)
			self::loadVerbs();
		return isset(self::$verbs[$string]);
	}

	/**
	 * Returns if the received word is a verb or not
	 * @param string $word
	 * @static
	 */
	public static function isVerb($word)
	{
		$word= self::toInfinitive($word);
		if($word)
			return true;
		return false;
	}

	/**
	 * This method reads the verbs.list file and
	 * parses it to an indexed array
	 * @name loadVerbs
	 * @static
	 */
	public static function loadVerbs()
	{
		if(!file_exists('verbs.list'))
			$fR= fopen(\Mind::$langPath.\Mind::$curLang.'/verbs.list', 'rb');
		else
			$fR= fopen('verbs.list', 'rb');
		self::$verbs= Array();
		while (!feof($fR)){
			$verb= preg_replace('/\s/', '', fgets($fR, 4096));
			self::$verbs[$verb]= true;
		}
	}
}