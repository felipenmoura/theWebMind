<?php
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

/**
 * This class should inflect words for different idioms
 * changing its genre and number
 * IN THIS CASE: pt-BR
 * @name Inflect
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @package cortex.analyst
 */
class Inflect implements inflection
{
    static $plural = array(
		'/^a$/i'						=> "a",
		'/^ao$/i'						=> "aos",
		'/^o$/i'						=> "os",
	    '/(.+[aeiouãõéíóúâôênh])ão$/i'	=> "$1ões",
	    '/(.+[aeiou][a-z])ão$/i'		=> "$1ães",
	    '/([cp])ão$/i'					=> "$1ães",
	    '/(.+[rs])$/i'					=> "$1es",
	    '/(.+[aeou])l$/i'				=> "$1is",
	    '/(.+[áéíóúâô][a-z]{1,2})il$/i'	=> "$1eis",
	    '/(.+i)l$/i'					=> "$1s",
	    '/(.+)z$/i'						=> "$1ses",
    	'/(.+)m$/i'						=> "$1ns",
    	'/([aeiou])$/i'					=> "$1s",
    	'/(.+x)$/i'						=> "$1"
    );

    static $singular = array(
		'/^aos$/i'							=> "ao",
		'/^os$/i'							=> "o",
        '/(.+)ões$/i'						=> "$1ão",
        '/([aeiouãõéíóúâôêpnh])ães$/i'		=> "$1ão",
        '/([cph])ães$/i'					=> "$1ão",
        '/(.+[a-z])ães$/i'					=> "$1ão",
	    '/(.+[aeiouãõéíóúâôê][a-z])ães$/i'	=> "$1ão",
	    '/(.+r)es$/i'						=> "$1",
	    '/(.+)ses$/i'						=> "$1z",
	    '/(.+s)es$/i'						=> "$1",
	    '/(.+[aeou])is$/i'					=> "$1l",
	    '/(.+[áéíóúâô][a-z]{1,2})eis$/i'	=> "$1il",
	    '/(.+i)s$/i'						=> "$1l",
    	'/(.+)ns$/i'						=> "$1m",
    	'/([aeiouãõéíóúâôê])s$/i'			=> "$1",
    	'/(.+x)$/i'							=> "$1"
    );

	static $female= Array(
		'/([aeiouãõéíóúâôê][a-z]{1,2})ão$/i'		=> "$1ona",
		'/([aeiouãõéíóúâôê])ão$/i'					=> "$1oa",
		'/o$/i'										=> "a",
		'/e$/i'										=> "a",
		'/or$/i'									=> "ora",
		'/om$/i'									=> "oa",
		'/m$/i'										=> "m",
		'/l$/i'										=> "l"
	);

	static $male= Array(
		'/([aeiouãõéíóúâôê][a-z]{1,2})ona$/i'		=> "$1ão",
		'/([aeiouãõéíóúâôê])oa$/i'					=> "$1ão",
		'/ora$/i'									=> "or",
		'/([aeiouãõéíóúâôê][a-z]{1,2})a$/i'			=> "$1o",
		'/a$/i'										=> "e"
	);

	static $genreSpecific= Array(
		'ele'							=> 'ela',
		'cada'							=> 'cada',
		'eles'							=> 'elas',
		'cônsul' 						=> 'consulesa',
		'visconde' 						=> 'viscondessa',
		'homem'							=> 'mulher',
		'poeta' 						=> 'poetisa',
		'bode' 							=> 'cabra',
		'boi' 							=> 'vaca',
		'burro' 						=> 'besta',
		'cão' 							=> 'cadela',
		'carneiro' 						=> 'ovelha',
		'cavaleiro' 					=> 'amazona',
		'frade' 						=> 'freira',
		'veado' 						=> 'cerva',
		'zangão' 						=> 'abelha',
		'ateu' 							=> 'atéia',
		'ator' 							=> 'atriz',
		'avô' 							=> 'avó',
		'embaixador' 					=> 'embaixatriz',
		'judeu' 						=> 'judia',
		'maestro' 						=> 'maestrina',
		'marajá' 						=> 'marani',
		'réu' 							=> 'ré',
		'sultão' 						=> 'sultana'
	);

    static $irregular = array(
        'freguês'=> 'fregueses'
    );

    static $uncountable = array(
        'óculos',
		'para',
		'do',
		'que',
		'de',
		'da',
		'vários',
		'várias',
        'oculos',
        'átlas',
        'atlas',
        'binoculos',
        'calças',
        'lápis',
        'lapis',
        'vírus',
        'virus'
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
    	if(in_array($string, Inflect::$uncountable))
    		return true;
    	// let's check for irregular forms
    	if(in_array($string, array_keys(Inflect::$irregular)))
    		return true;
    	// now, let's see if the word isn't the plural from a irregular form
    	// still faster than running all the plural forms, I bet
    	elseif(in_array($string, Inflect::$irregular))
    			return false;
    	// ok, if the word reached here, it diserves some care
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
    public static function toPlural( $string )
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
    public static function toSingular( $string )
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
	 * Chenges the genre of a word to female
	 *
	 * @method toFemale
	 * @param String $string
	 * @return String
	 */
    public static function toFemale($string)
    {
    	// first, we gotta see if it is a word with specific genre change
    	if(array_key_exists($string, self::$genreSpecific))
    		return self::$genreSpecific[$string];

        // check for matches using regular expressions
        foreach(self::$female as $pattern => $result)
        {
            if(preg_match( $pattern, $string))
                return preg_replace($pattern, $result, $string);
        }

        return $string;
    }
	/**
	 * Changes the genre of a word to male
	 *
	 * @method toMale
	 * @param String $string
	 * @return String
	 */
    public static function toMale($string)
    {
    	if($k= array_search($string, self::$genreSpecific))
    		return $k;

        // check for matches using regular expressions
        foreach(self::$male as $pattern => $result)
        {
            if(preg_match( $pattern, $string))
                return preg_replace($pattern, $result, $string);
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
		// let's avoid any preposition
    	if(in_array($string, self::$uncountable))
    		return false;
    	// it is a female from the specific list
    	if(in_array($string, self::$genreSpecific))
    		return true;
    	// if it is a male kay, in the specific list
    	if(array_key_exists($string, self::$genreSpecific))
    		return false;
    	// ok, now let's see if it is a female by touching it!
    	foreach(self::$female as $pattern => $result)
        {
            if(preg_match($pattern, $string))
            {
            	return false;
            }
		}
    	return true;
    }
}