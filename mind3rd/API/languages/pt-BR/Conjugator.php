<?php
/**
 * This class should identify verbs
 * NOTICE that its goas is about the present/future words...
 * past is not supported
 *
 * @author felipe
 */
class Conjugator {
	/**
	 * @var AssocArray $verbs - The associative list of verbs
	 * @static $verbs
	 */
	public static $verbs= false;

	/**
	 *
	 * @var AssocArray An array of verbs that may indicate a possibility
	 */
	static $possibilities= Array(
		'poder',
		'podem',
		'poderá',
		'poderão',
		'possivelmente',
		'provavelmente'
	);

	/**
	 *
	 * @var AssocArray the possible flections a verb may suffer
	 */
    static $flections = array(
		'/õe$/'								=> 'ôr',
		'/rão$/'							=> 'r',
		'/a$/'								=> 'ar',
		'/e$/'								=> 'er',
		'/vo$/'								=> 'ver',
		'/i$/'								=> 'er',
		'/am$/'								=> 'ar',
		'/o$/'								=> 'ar',
		'/(.+)o$/'							=> '$1er',
		'/(.+)a$/'							=> '$1er',
		'/em$/'								=> 'er',
		'/remos$/'							=> 'r',
		'/emos$/'							=> 'er',
		'/mos$/'							=> 'r',
		'/ei$/'								=> '',
		'/.ei$/'							=> 'ar',
		'/(.)ei$/'							=> '$1er',
		'/ou$/'								=> 'ar',
		'/eu$/'								=> 'er',
		'/ás$/'								=> '',
		'/á$/'								=> '',
	);

	/**
	 *
	 * @var AssocArray A list of fixed exceptions
	 */
	static $exceptions= Array(
		'sei'								=> 'saber',
		'dei'								=> 'dar',
		'dou'								=> 'dar',
		'deu'								=> 'dar',
		'dão'								=> 'dar',
		'deram'								=> 'dar',
		'dará'								=> 'dar',
		'darão'								=> 'dar',
		'teem'								=> 'ter',
		'terem'								=> 'ter',
		'terão'								=> 'ter',
		'tiveram'							=> 'ter',

	);

	/**
	 * Returns if the passed word means the verb is a possibility
	 * @param string $word
	 * @return boolean
	 */
	public static function isAPosibility($string)
	{
		return in_array($string, self::$possibilities);
	}

	/**
	 * Returns if the passed word is in the infinitive form
	 * @param string $word
	 * @return boolean
	 */
	public static function isInfinitive($string)
	{
		foreach(self::$flections as $pattern=>$result)
		{
			if(preg_match('/'.$result.'$/i', $string) && self::isInVerbList($string))
				return true;
		}
		return false;
	}

	/**
	 * Tries to bring any word to its infinitive form
	 * Returns false if it is not in the verbs.list
	 * @param string $word
	 * @return string
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
            $pattern= ''.$pattern.'i';
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
	 */
	public static function isVerb($word)
	{
		$word= self::toInfinitive($word);
		if($word)
			return true;
		return false;
	}

	/**
	 * @name loadVerbs
	 */
	public static function loadVerbs()
	{
		$fR= fopen('verbs.list', 'rb');
		Conjugator::$verbs= Array();
		while (!feof($fR)){
			$verb= preg_replace('/\s/', '', fgets($fR, 4096));
			Conjugator::$verbs[$verb]= true;
		}
	}
}