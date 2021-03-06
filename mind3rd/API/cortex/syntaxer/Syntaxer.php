<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * Syntaxer, within the Cortex/Syntaxer packages.<br/>
 * Notice that, these packages are being used only for documentation,
 * not to organize the classes.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
/**
 * Identifies and organizes the valid syntaxes on the content.
 * 
 * This class will work with the tokenized strucure and
 * the original code, due to identify patterns and apply
 * any needed rules.
 * <pre>
 * Legend:
 *  Q=May have, Must have
 *  S=Substantive
 *  V=Verb
 *  0,N=Quantifiers
 *  O=Or
 *  C=Complement/Composite(like "of", or "de")
 *  A=Addition(like "," or "and"
 *</pre>
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @package Cortex
 * @subpackage Syntaxer
 */
class Syntaxer {

	public static $sintatics= Array();

	/**
	 * Loads the file which has the list of avaliable sintaxes.
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
	 * Fetches the identified composed substantives.
	 * 
	 * Fixes the composed substantives(defined by the use of
	 * the "of" tokens defined into the qualifiers.xml of the current idiom)
	 */
	public function fetchComposedSubstantives()
	{
		while(preg_match(COMPOSED_SUBST,
						 Token::$string,
						 $matches,
						 PREG_OFFSET_CAPTURE))
		{
			$matches= $matches[0];
			array_splice(Token::$spine, $matches[1], 3, Token::MT_SUBST);
			array_splice(Token::$words, $matches[1], 3,
						 Token::$words[$matches[1]].
						 '_'.
						 Token::$words[$matches[1]+2]);
			Token::$string= preg_replace(COMPOSED_SUBST, 'S', Token::$string, 1);
		}
	}
	
	/**
	 * Sweeps the content to apply the rules and identify patterns.
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
		return $matches;
	}
}