<?php
// Thanks to http://www.eval.ca/articles/php-toPlural (MIT license)
//           http://dev.rubyonrails.org/browser/trunk/activesupport/lib/active_support/inflections.rb (MIT license)
//           http://www.fortunecity.com/bally/durrus/153/gramch13.html
//           http://www2.gsu.edu/~wwwesl/egw/crump.htm
//
// Changes (12/17/07)
//   Major changes
//   --
//   Fixed irregular noun algorithm to use regular expressions just like the original Ruby source.
//       (this allows for things like fireman -> firemen
//   Fixed the order of the singular array, which was backwards.
//
//   Minor changes
//   --
//   Removed incorrect pluralization rule for /([^aeiouy]|qu)ies$/ => $1y
//   Expanded on the list of exceptions for *o -> *oes, and removed rule for buffalo -> buffaloes
//   Removed dangerous singularization rule for /([^f])ves$/ => $1fe
//   Added more specific rules for singularizing lives, wives, knives, sheaves, loaves, and leaves and thieves
//   Added exception to /(us)es$/ => $1 rule for houses => house and blouses => blouse
//   Added excpetions for feet, geese and teeth
//   Added rule for deer -> deer
//
//	Changed by Felipe Nascimento de Moura <felipenmoura@gmail.com>
//	added is_singular static method
//	translation to Brazilian portugues pt-BR
//
// Changes:
//   Removed rule for virus -> viri
//   Added rule for potato -> potatoes
//   Added rule for *us -> *uses

class Inflect
{
    static $plural = array(
	    '/(.+[aeiou])ão$/'				=> "$1ões",
	    '/(.+[aeiou][a-z])ão$/'			=> "$1ães",
	    '/(.+[rs])$/'					=> "$1es",
	    '/(.+[aeou])l$/'				=> "$1is",
	    '/(.+[áéíóúâô][a-z]{1,2})il$/'	=> "$1eis",
	    '/(.+i)l$/'						=> "$1s",
	    '/(.+)z$/'						=> "$1ses",
    	'/(.+)m$/'						=> "$1ns",
    	'/([aeiou])$/'					=> "$1s",
    	'/(.+x)$/'						=> "$1"
    );

    static $singular = array(
        '/(.+[aeiou])ões$/'				=> "$1ão",
	    '/(.+[aeiou][a-z])ães$/'		=> "$1ão",
	    '/(.+r)es$/'					=> "$1",
	    '/(.+)ses$/'					=> "$1z",
	    '/(.+s)es$/'					=> "$1",
	    '/(.+[aeou])is$/'				=> "$1l",
	    '/(.+[áéíóúâô][a-z]{1,2})eis$/'	=> "$1il",
	    '/(.+i)s$/'						=> "$1l",
    	'/(.+)ns$/'						=> "$1m",
    	'/([aeiouãõéíóúâôê])s$/'					=> "$1",
    	'/(.+x)$/'						=> "$1"
    );

	static $female= Array(
		'/([aeiou][a-z]{1,2})ão$/'		=> "$1ona",
		'/([aeiou])ão$/'				=> "$1oa",
		'/o$/'							=> "a",
		'/e$/'							=> "a",
		'/or$/'							=> "ora",
		'/om$/'							=> "oa",
		'/m$/'							=> "m"
	);
	
	static $male= Array(
		'/([aeiou][a-z]{1,2})ona$/'		=> "$1ão",
		'/([aeiou])oa$/'				=> "$1ão",
		'/ora$/'						=> "or",
		'/([aeiou][a-z]{1,2})a$/'		=> "$1o",
		'/a$/'							=> "e"
	);

	static $genreSpecific= Array(
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
        'move'   => 'moves',
        'freguês'=> 'fregueses'
    );

    static $uncountable = array(
        'óculos',
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
     *  @method is_singular
     *  @return boolean
     *  @param String $string
     */
    public static function is_singular( $string )
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
    	foreach(self::$plural as $pattern => $result)
        {
            if(preg_match( $pattern, $string))
            {
            	return false;
            }
		}
		return true;
    }

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

    public static function toPlural_if($count, $string)
    {
        if ($count == 1)
            return "1 $string";
        else
            return $count . " " . self::toPlural($string);
    }
    
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
    
    public static function toMale($string)
    {
    	
    	// first, we gotta see if it is a word with specific genre change
        foreach ( self::$genreSpecific as $pattern => $result )
        {
            $pattern = '/' . $pattern . '$/i';
            if(preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach(self::$male as $pattern => $result)
        {
            if(preg_match( $pattern, $string))
                return preg_replace($pattern, $result, $string);
        }
        
        return $string;
    }
    
    public static function isFemale($string)
    {
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








