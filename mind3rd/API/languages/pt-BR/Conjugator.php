<?php
/**
 * This class should identify verbs
 * NOTICE that its goas is about the present/future words...
 * past is not supported
 *
 * @author felipe
 */
class Conjugator {
	public static $verbs= false;

	static $possibilities= Array(
		'poder',
		'podem',
		'poderá',
		'poderão',
		'possivelmente',
		'provavelmente'
	);
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
		'/mos$/'							=> 'r'
	);

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

	public static function isAPosibility($string)
	{
		return in_array($string, self::$possibilities);
	}

	public static function isInfinitive($string)
	{
		foreach(self::$flections as $pattern=>$result)
		{
			if(preg_match('/'.$result.'$/i', $string))
				return true;
		}
		return false;
	}

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