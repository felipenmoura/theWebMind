<?php
/**
 * This class sweeps the passed words and validates if
 * its content matches with the valid chars
 * @package cortex.analyst
 * @author felipe
 */
class Lexer
{
	private $validChars;
	private $replacements;
	public  $lang= 'en';
	private $content= "";
	private $tokens= Array();
	private $glue= '';

	/**
	 * Thanks to saeedco (no more information found about him!)
	 * Source: http://br2.php.net/manual/en/function.str-split.php#83331
	 * Idioms it is supposed to work properly
	 * Chinese
	 * Japanese
	 * Arabic
	 * Turkish
	 * Urdu
	 * Russian
	 * Persian
	 * Portuguese
	 *
	 * @param String $str
	 * @return array
	 */
	private function str_split_utf8($str) {
		// place each character of the string into and array
		$split=1;
		$array = array();
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
	 * Validates the sent char
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
	 * Performs a sweep over the sent content and replace any
	 * special char, also removing any invalid char.
	 * @param string $content
	 * @return string The cleaned content
	 */
    public function sweep($content)
	{
		$this->content= trim(str_replace("\n", ' ', $content));
		$this->originalContent= $this->content;
		$this->content= $this->str_split_utf8($this->content);
		// untill here, it's all fine

		// the fixed content;
		$fixed= "";
		// for each charactere on the content
		// let's remove th invalid ones
		for($i=0, $j=sizeof($this->content); $i<$j; $i++)
		{
			$letter= $this->content[$i];
			if($this->isValidChar($letter))
				$fixed.= $letter;
		}

		// preparing the tokens
		foreach($this->tokens as $char=>$token)
		{
			$fixed= str_replace($char, $token, $fixed);
		}
		$fixed= preg_replace("/\n/", $this->tokens[' '], $fixed);
		$fixed= array_filter(explode($this->tokens[' '], $fixed));
		Mind::$content= $fixed;
		return sizeof(Mind::$content)>0? Mind::$content: false;
	}

	/**
	 * Returns the fixed char to the sent char
	 * @param string $char
	 * @return string
	 */
	public function translateChars($str)
	{
		$from = $this->replacements[0];
		$to   = $this->replacements[1];
		return strtr($str, $from, $to);
	}
	
	public function __construct()
	{
		GLOBAL $_MIND;
		$this->glue= chr('176');
		$this->lang= Mind::$l10n->name;
		$xml= simplexml_load_file(Mind::$langPath.$this->lang.'/lexics.xml');
		include(Mind::$langPath.$this->lang.'/Inflect.php');
		$this->validChars= (string)$xml->validchars->lower;
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
