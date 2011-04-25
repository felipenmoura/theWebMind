<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */
	/*
		Thanks to http://www.eval.ca/articles/php-toPlural (MIT license)
				   http://dev.rubyonrails.org/browser/trunk/activesupport/lib/active_support/inflections.rb (MIT license)
				   http://www.fortunecity.com/bally/durrus/153/gramch13.html
				   http://www2.gsu.edu/~wwwesl/egw/crump.htm
		This classe has been partly inspired on the above cite codes.
		The other methods and all the regular expression except the ones
		refered to plural and singular on english were created by:
		Felipe Nascimento de Moura <felipenmoura@gmail.com>
	 *  You can contribute changing this file and telling me, or maybe
	 *  adding tests to it, and in case you find anything weird, please
	 *  let me know :)
	 */

namespace en;

/**
 * This class should inflect words for different idioms
 * changing its genre and number
 * IN THIS CASE: en
 * @name Inflect
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @package cortex.analyst
 */
class Inflect implements \inflection
{
	static $plural = array(
		'/z$/'							=> 'zes',
		'/^(ox)$/i'						=> "$1en",
		'/([m|l])ouse$/i'				=> "$1ice",
        '/(matr|vert|ind)ix|ex$/i'		=> "$1ices",
        '/y$/i'							=> "ies",
        '/f$/i'							=> "ves",
        '/(tomat|potat|ech|her|vet)o$/i'=> "$1oes",
		'/s$/'							=> "ses"
	);
	static $singular = array(
		'/rs$/'				=> 'r',
		'/ies$/'			=> 'y',
		'/sses$/'		    => 'ss', // for classes, for example
		'/ses$/'		    => 'se', // for cases, for example
		'/shes$/'			=> 'sh',
		'/ss$/'				=> 'ss',
		'/s$/'				=> ''
	);
	/*
    static $plural = array(
        '/(quiz)$/i'					=> "$1zes",
        '/^(ox)$/i'						=> "$1en",
        '/([m|l])ouse$/i'				=> "$1ice",
        '/(matr|vert|ind)ix|ex$/i'		=> "$1ices",
        '/(x|ch|ss|sh)$/i'				=> "$1es",
        '/([^aeiouy]|qu)y$/i'			=> "$1ies",
        '/(hive)$/i'					=> "$1s",
        '/(?:([^f])fe|([lr])f)$/i'		=> "$1$2ves",
        '/(shea|lea|loa|thie)f$/i'		=> "$1ves",
        '/sis$/i'						=> "ses",
        '/([ti])um$/i'					=> "$1a",
        '/(tomat|potat|ech|her|vet)o$/i'=> "$1oes",
        '/(bu)s$/i'						=> "$1ses",
        '/(alias)$/i'					=> "$1es",
        '/(octop)us$/i'					=> "$1i",
        '/(ax|test)is$/i'				=> "$1es",
        '/(us)$/i'						=> "$1es",
        '/y$/i'							=> "ies",
        '/s$/i'							=> "s"
    );

    static $singular = array(
        '/(quiz)zes$/i'             => "$1",
        '/(matr)ices$/i'            => "$1ix",
        '/(vert|ind)ices$/i'        => "$1ex",
        '/^(ox)en$/i'               => "$1",
        '/(alias)es$/i'             => "$1",
        '/(octop|vir)i$/i'          => "$1us",
        '/(cris|ax|test)es$/i'      => "$1is",
        '/(shoe)s$/i'               => "$1",
        '/(o)es$/i'                 => "$1",
        '/(bus)es$/i'               => "$1",
        '/([m|l])ice$/i'            => "$1ouse",
        '/(x|ch|ss|sh)es$/i'        => "$1",
        '/(m)ovies$/i'              => "$1ovie",
        '/(s)eries$/i'              => "$1eries",
        '/([^aeiouy]|qu)ies$/i'     => "$1y",
        '/ves$/i'		            => "ve",
        '/(tive)s$/i'               => "$1",
        '/(hive)s$/i'               => "$1",
        '/(li|wi|kni)ves$/i'        => "$1fe",
        '/(shea|loa|lea|thie)ves$/i'=> "$1f",
        '/(^analy)ses$/i'           => "$1sis",
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",
        '/([ti])a$/i'               => "$1um",
        '/(n)ews$/i'                => "$1ews",
        '/(h|bl)ouses$/i'           => "$1ouse",
        '/(corpse)s$/i'             => "$1",
        '/(us)es$/i'                => "$1",
        '/ies$/i'					=> "y",
        '/s$/i'                     => ""
    );
	*/
	static $female= Array();
	static $male= Array();
	static $genreSpecific= Array();

    static $irregular = array(
        'move'   => 'moves',
        'foot'   => 'feet',
        'goose'  => 'geese',
        'sex'    => 'sexes',
        'child'  => 'children',
        'man'    => 'men',
        'tooth'  => 'teeth',
        'person' => 'people',
		'mouse'  => 'mice'
    );

    static $uncountable = array(
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment'
    );

    /**
     *	@author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     *  @method isSingular
     *  @return boolean
     *  @param String $string
     */
    public static function isSingular( $string )
    {
    	// if it is uncountable, then, it may be treated as a singular
    	if(in_array($string, self::$uncountable))
    		return true;
    	// let's check for irregular forms
    	if(in_array($string, array_keys(self::$irregular)))
    		return true;
    	// now, let's see if the word isn't the plural from a irregular form
    	// still faster than running all the plural forms, I bet
    	elseif(in_array($string, self::$irregular))
    			return false;
    	// ok, if the word reached here, it diserves some attemtion
    	// let's finally check if it matches with any plural rule
    	foreach(self::$singular as $pattern => $result)
        {
            if(preg_match( $pattern, $string))
            {
            	return false;
            }
		}
		return true;
    }

	/**
	 * Set a word to plural
	 * @method toPlural
	 * @param String $string
	 * @return String
	 */
    public static function toPlural($string)
    {
        // save some time in the case that singular and plural are the same
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

        // check for irregular singular forms
        foreach ( self::$irregular as $pattern => $result )
        {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach ( self::$plural as $pattern => $result )
        {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }
        return preg_replace('/$/', '$1s', $string);
        return $string;
    }

	/**
	 * Sets a word to singular form
	 *
	 * @method toSingular
	 * @param String $string
	 * @return String
	 */
    public static function toSingular($string)
    {
        // save some time in the case that singular and plural are the same
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

        // check for irregular plural forms
        foreach ( self::$irregular as $result => $pattern )
        {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach ( self::$singular as $pattern => $result )
        {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }

	/**
	 * Verifies whether the given word is female or not
	 *
	 * @method isFemale
	 * @param String $string
	 * @return String
	 */
    public static function isFemale($string)
	{
		return false;
	}

	public static function toFemale($word)
	{
		return $word;
	}
	public static function toMale($word)
	{
		return $word;
	}
}
