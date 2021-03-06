<?php
/**
 * This file is part of TheWebMind 3rd generation.
 * 
 * Lexer, within the Cortex/Lexer packages.<br/>
 * Notice that, these packages are being used only for documentation,
 * not to organize the classes.
 * 
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 * @license licenses/mind3rd.license
 */
/**
 * This class sweeps the passed words and validates if
 * its content matches with the valid chars
 * 
 * @package Cortex
 * @subpackage Lexer
 * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
 */
class Lexer
{
	private $validChars;
	private $replacements;
	public  $lang= 'en';
	public  $originalContent= '';
	private $content= "";
	private $tokens= Array();
	private $glue= '';

	/**
	 * Splits a string using utf8 format.
	 * 
	 * Thanks to saeedco (no more information found about him!) I had to
	 * change only a few things on this method.
	 * Source: http://br2.php.net/manual/en/function.str-split.php#83331
	 * Idioms it is supposed to work properly
	 * English
	 * Chinese
	 * Japanese
	 * Arabic
	 * Turkish
	 * Urdu
	 * Russian
	 * Persian
	 * Portuguese
	 * 
	 * Of course, if you find any problem, report it to us :)
	 *
	 * @param String $str The string to be splitted.
	 * @return array
	 */
	private function str_split_utf8($str) {
		// place each character of the string into and array
		$split=1;
		$array = array();

		/*
		 * SOMEONE HERE, PLEASE! Explain that for me!
		 * ONLY the 'ó' char isnt working when treated...even 'Ó' is!
		 * ALL the other characteres are working just fine!
		 * Someone who could fix it in a better way, please
		 */
		$str= str_replace('ó', 'o', $str);

		for ( $i=0; $i < strlen( $str ); ){
			$value = ord($str[$i]);
			if($value > 127){
				if($value >= 192 && $value <= 223)
					$split=2;
				elseif($value >= 224 && $value <= 239)
					$split=3;
				elseif($value >= 240 && $value <= 247)
					$split=4;
			}else{
				$split=1;
			}
				$key = NULL;
			for ( $j = 0; $j < $split; $j++, $i++ ) {
				$key .= $str[$i];
			}
			array_push( $array, $key );
		}
		return $array;
	}

	/**
	 * Validates the sent char.
	 * 
	 * Verifies wether the passed char is a valid char for
	 * that idiom or not.
	 * 
	 * @param char $letter
	 * @return boolean
	 */
	private function isValidChar($letter)
	{
		$letter= ($letter);
		for($k=0; $k<strlen($this->validChars); $k++)
		{
			if($this->validChars[$k] == $letter
			   ||
			   ord($this->validChars[$k]) == ord($letter)
			  )
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Prepares names for their use.
	 * 
	 * Parses the string encoding and returns a valid name
	 * to be used on databases or restricted names
	 * 
	 * @param String $word
	 * @return String
	 */
	public function fixWordChars($word)
	{
		$word= preg_replace(FIX_PROP_NAME, '', strtolower($word));

		$word= $this->str_split_utf8($word);
		$str= "";
		for($i=0, $j=sizeof($word); $i<$j; $i++)
		{
			$str.= $this->translateChars($word[$i]);
		}
		return strtolower($str);
	}

	/**
	 * Fixes the passed string replacing special chars.
	 * 
	 * This method takes each special char and replaces it with
	 * the respective char specified in the choosen idiom.
	 * 
	 * @param string $str
	 * @return string
	 */
	public function translateChars($str)
	{
		$from = $this->replacements[0];
		$to   = $this->replacements[1];
		return strtr(utf8_decode($str), utf8_decode($from), $to);
	}

	/**
	 * Sweeps through the content and apply the lexer rules.
	 * 
	 * Performs a sweep over the sent content and replace any
	 * special char, also removing any invalid char.
	 * @param string $content
	 * @return string The cleaned content
	 */
    public function sweep($content)
	{
		Mind::$originalContent= $content;
		// let's treat the single line comments
		$content= preg_replace(SINGLE_COMMENT, '', $content);

		// now, it's time to start working with the data
		$this->content= trim(str_replace("\n", ' ', $content));
		while(strstr($this->content, '	')!==false) // ignoring tabs
			$this->content= str_replace('	', ' ', $this->content);
		while(strstr($this->content, '  ')!==false) // ignoring multiple spaces
			$this->content= str_replace('  ', ' ', $this->content);

		$this->originalContent= $this->content;
		$this->content= $this->str_split_utf8($this->content);

		// the fixed content;
		$fixed= "";

		// for each charactere on the content
		// let's remove the invalid ones
		$inside= 0;
		for($i=0, $j=sizeof($this->content); $i<$j; ++$i)
		{
			$letter= $this->content[$i];
			if($this->isValidChar($letter))
			{
				if($letter == '(')
					$inside++;
				if($letter == ')' && $inside>0)
					$inside--;
				if($inside > 0)
				{
					if($letter == ' ')
						$letter= $this->tmpSpace;
					if($letter == ',')
						$letter= $this->tmpComa;
					if($letter == '.')
						$letter= $this->tmpPeriod;
				}

				$fixed.= $letter;
			}
		}

		// preparing the tokens
		foreach($this->tokens as $char=>$token)
		{
			$fixed= str_replace($char, $token, $fixed);
		}

		// but content between parentheses should be left with
		// normal spaces, instead of the space token
		// restoring the initial format for attribute details
		$fixed= str_replace($this->tmpSpace, " ", $fixed);
		$fixed= str_replace($this->tmpComa, ",", $fixed);
		$fixed= str_replace($this->tmpPeriod, ".", $fixed);

		// let's deal with the \n and multiline comments
		$fixed= preg_replace(NEW_LINE, $this->tokens[' '], $fixed);
		$fixed= preg_replace(MULTILINE_COMMENT, '', $fixed);

		$exploded= explode($this->tokens[' '], $fixed);
		
		$fixed= array_filter($exploded);

		Mind::$content= $fixed;
		$this->content= "";

		return sizeof(Mind::$content)>0? Mind::$content: false;
	}

	/**
	 * The constructor
	 */
	public function __construct()
	{
		GLOBAL $_MIND;
		$this->glue		 = chr('176');
		$this->tmpSpace  = chr('177');
		$this->tmpComa	 = chr('178');
		$this->tmpPeriod = chr('179');
		$this->lang		 = Mind::$currentProject['idiom'];
		$xml			 = simplexml_load_file(Mind::$langPath.$this->lang.'/lexics.xml');

		$this->validChars = (string)$xml->validchars->lower;
		$this->validChars.= (string)$xml->validchars->upper;
		$this->validChars.= (string)$xml->validchars->special;
		$this->validChars.= (string)$xml->validchars->numbers;
		$this->validChars.= (string)$xml->validchars->symbols;
		$this->validChars.= " ";

		$this->replacements= Array();
		$this->replacements[0]= (string)$xml->replacements->from;
		$this->replacements[1]= (string)$xml->replacements->to;
		
		$this->tokens[' ']= $this->glue;
		$this->tokens['.']= $this->glue.'.'.$this->glue;
		$this->tokens[',']= $this->glue.','.$this->glue;

		// prepare it to the next step
		Mind::$canonic= new Canonic();
	}
}
?>
