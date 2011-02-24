<?php

/**
 * The Token itself
 * @author felipe
 */
class Token
{
	// Tokens to be used
	// MT stands for MindTokenizer
	// MS stands for MindSyntaxer
	const MT_PERIOD  =  -2;
	const MS_PERIOD  = '.';
	const MT_COMA    =  -1;
	const MS_COMA    = ',';
	const MT_VOID    =   0;
	const MS_VOID    =  '';
	const MT_VERB    =   1;
	const MS_VERB    = 'V';
	const MT_SUBST   =   2;
	const MS_SUBST   = 'S';
	const MT_NONE    =   4;
	const MS_NONE    = 'N';
	const MT_ONE     =   8;
	const MS_ONE     = 'N';
	const MT_OR      =  16;
	const MS_OR      = 'O';
	const MT_MANY    =  32;
	const MS_MANY    = 'N';
	const MT_QMUST   =  64;
	const MS_QMUST   = 'Q';
	const MT_QMAY    = 128;
	const MS_QMAY    = 'Q';
	const MT_QNOTNULL= 254;
	const MT_QKEY    = 564;
	const MT_QOF     =1024;
	const MS_QOF     = 'C';
	const MT_QBE     =2048;
	const MT_ANY     =4096;
	const MS_ANY     = '*';

	public static $spine= Array();
	public static $words= Array();
	public static $string= '';

	public function add($word)
	{
		$ignoreForms= Mind::$currentProject['idiom'].'\IgnoreForms';
		$verbalizer= Mind::$currentProject['idiom'].'\Verbalizer';

		if(in_array($word, Tokenizer::$qualifiers['coma']))
		{
			$word= ',';
		}

		self::$words[]= $word;

		if($ignoreForms::shouldBeIgnored($word))
		{
			self::$spine[]= Token::MT_ANY;
			self::$string.= Token::MS_ANY;
			return;
		}
		if($word==',')
		{
			self::$spine[]= Token::MT_COMA;
			self::$string.= Token::MS_COMA;
			return;
		}
		if($word=='.')
		{
			self::$spine[]= Token::MT_PERIOD;
			self::$string.= Token::MS_PERIOD;
			return;
		}

		// let's check for quantifiers
		if(Tokenizer::isQuantifier('none', $word))
		{
			self::$spine[]= Token::MT_NONE;
			self::$string.= Token::MS_NONE;
			return;
		}
		if(Tokenizer::isQuantifier('one', $word))
		{
			self::$spine[]= Token::MT_ONE;
			self::$string.= Token::MS_ONE;
			return;
		}
		if(Tokenizer::isQuantifier('many', $word))
		{
			self::$spine[]= Token::MT_MANY;
			self::$string.= Token::MS_MANY;
			return;

		}
		if(Tokenizer::isQuantifier('or', $word))
		{
			self::$spine[]= Token::MT_OR;
			self::$string.= Token::MS_OR;
			return;
		}

		// and here, the qualifiers
		if(Tokenizer::isQualifier('must', $word))
		{
			self::$spine[]= Token::MT_QMUST;
			self::$string.= Token::MS_QMUST;
			return;
		}
		if(Tokenizer::isQualifier('may', $word))
		{
			self::$spine[]= Token::MT_QMAY;
			self::$string.= Token::MS_QMAY;
			return;
		}
		if(Tokenizer::isQualifier('notnull', $word))
		{
			self::$spine[]= Token::MT_QNOTNULL;
			return;
		}
		if(Tokenizer::isQualifier('of', $word))
		{
			self::$string.= Token::MS_QOF;
			self::$spine[]= Token::MT_QOF;
			return;
		}
		if(Tokenizer::isQualifier('be', $word))
		{
			self::$spine[]= Token::MT_QBE;
			return;
		}
		if(Tokenizer::isQualifier('key', $word))
		{
			self::$spine[]= Token::MT_QKEY;
			return;
		}
		// we know these words are already on its
		// canonic form, so, we can simply look for
		// it on the list
		if($verbalizer::isInVerbList($word))
		{
			self::$spine[]= Token::MT_VERB;
			self::$string.= Token::MS_VERB;
			return;
		}
		self::$spine[]= Token::MT_SUBST;
		self::$string.= Token::MS_SUBST;
		
		return $word;
	}
}