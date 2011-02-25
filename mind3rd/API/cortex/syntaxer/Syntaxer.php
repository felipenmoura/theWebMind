<?php
/**
 * This class will work with the tokenized strucure and
 * the original code, due to identify patterns and apply
 * any needed rules
 * Legend:
 *  Q=May have, Must have
 *  S=Substantive
 *  V=Verb
 *  0,N=Quantifiers
 *  O=Or
 *  C=Complement(like "of", or "de")
 *  A=Addition(like "," or "and"
 *
 * @author felipe
 */
class Syntaxer {

	public static $sintatics= Array();

	/**
	 * Loads the file which has the list of avaliable sintaxes
	 * @static loadSintaticList
	 */
	public static function loadSintaticList()
	{
		if(!file_exists('sintatics.list'))
			$fR= fopen(Mind::$langPath.Mind::$curLang.'/sintatics.list', 'rb');
		else
			$fR= fopen('sintatics.list', 'rb');

		while (!feof($fR)){
			$sint= preg_replace('/\s/', '', fgets($fR, 4096));
			self::$sintatics[]= $sint;
		}
	}

	/**
	 * Fixes the composed substantives(defined by the use of
	 * the "of" tokens)
	 */
	public function fetchComposedSubstantives()
	{
		echo Token::$string."\n";
		while(preg_match('/SCS/', Token::$string, $matches, PREG_OFFSET_CAPTURE))
		{
			$matches= $matches[0];
			array_splice(Token::$spine, $matches[1], 3, Token::MT_SUBST);
			array_splice(Token::$words, $matches[1], 3,
						 Token::$words[$matches[1]].
						 '_'.
						 Token::$words[$matches[1]+2]);
			Token::$string= preg_replace('/SCS/', 'S', Token::$string, 1);
		}
		return true;
		$words = Array();
		$spine = Array();
		$string= '';
		// for each word
		for($i=0, $j=sizeof(Token::$spine); $i<$j; $i++)
		{
			// if it is a substantive and there is another substantive
			// two positions after, with an OF token between them
			if( Token::$spine[$i] == Token::MT_SUBST
				&& isset(Token::$spine[$i+1])
				&& isset(Token::$spine[$i+2])
				&& Token::$spine[$i+1] == Token::MT_QOF
				&& Token::$spine[$i+2] == Token::MT_SUBST)
			{
				// rewrite the substantive
				$words[]= Token::$words[$i].
						  PROPERTY_SEPARATOR.
						  Token::$words[$i+2];
				$spine[]= Token::$spine[$i];
				$string.= Token::$string[$i];
				
				//$i= $i+2;
			}else{
					$words[]= Token::$words[$i];
					$spine[]= Token::$spine[$i];
					$string.= Token::$string[$i];
				 }
		}
		Token::$words = $words;
		Token::$string= $string;
		Token::$spine = $spine;
	}
	
	/**
	 * Sweeps the content to apply the rules and identify patterns
	 * @name sweep
	 */
	public function sweep()
	{
		// loading stintatic list: all the avaliable, allowed syntaxes
		// formats, specified by the selected idiom
		self::loadSintaticList();

		// mounting the regular expression
		$pattern= implode('|', self::$sintatics);

		// let's find all the patterns that match
		// that means that we'll find only expressions with valid syntax
		// reminding that the pattern was dynamicaly created before
		$pattern= str_replace('S', VALID_SUBST_SYNTAX, $pattern);

		$this->fetchComposedSubstantives();
		
		// finding the valid syntaxes
		preg_match_all('/'.$pattern.'/',
					   Token::$string,
					   $matches,
					   PREG_OFFSET_CAPTURE);

		// as we know it's only one block, we can use it straightly
		$matches= $matches[0];

		Analyst::sweep($matches);

		return $this;
	}
}