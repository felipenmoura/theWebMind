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
	 * Sweeps the content to apply the rules and identify patterns
	 * @name sweep
	 */
	public function sweep()
	{
		self::loadSintaticList();

		// mounting the regular expression
		$pattern= implode('|', self::$sintatics);

		// let's find all the patterns that match
		// that means that we'll find only expressions with valid syntax
		$pattern= str_replace('S', 'S((( )?\,( )?S)?)+', $pattern);

		preg_match_all('/'.$pattern.'/',
					   Token::$string,
					   $matches,
					   PREG_OFFSET_CAPTURE);
		// as we know it's only one block, we can use it straight
		$matches= $matches[0];

		foreach($matches as $found)
		{
			$len= strlen($found[0]);
			$expression= array_slice(Token::$words, $found[1], $len);
			$tokens= array_slice(Token::$spine, $found[1], $len);
			$struct= $found[0];
			
			// let's analize it, now
			Analyst::analize($expression, $struct, $tokens);
		}
		print_r(Analyst::getUniverse());
		return $this;
	}
}