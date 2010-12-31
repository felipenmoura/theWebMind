<?php
/**
 * This class will apply the most important tokens to be used.
 * It also builds the main structure to compare it to avaliable
 * sintatics. This structure is called SPINE
 *
 * @author felipe
 */
class Tokenizer extends Token{

	public static $sintaticsList;
	public static $quantifiers;
	public static $qualifiers;
	public static $spine= Array();
	public static $string='';

	/**
	 * This method builds the required structure from the
	 * sent XML file
	 *
	 * @name loadSintatics
	 * @param SimpleXML $resource
	 * @return AssocArray
	 */
	public static function loadSintatics($resource)
	{
		while (!feof($resource))
		{
			$word= preg_replace('/\s/', '', fgets($resource, 4096));
			self::$sintaticsList[$word]= true;
		}
		return self::$sintaticsList;
	}

	/**
	 * This method will build the structure of quantifiers
	 * from the passed XML
	 *
	 * @name loadQuantifiers
	 * @param SimpleXML $xml
	 * @return AssocArray
	 */
	public static function loadQuantifiers($xml)
	{
		self::$quantifiers= Array();
		self::$quantifiers['none'] = explode(',', str_replace(', ', ',', (String)$xml->none));
		self::$quantifiers['one']  = explode(',', str_replace(', ', ',', (String)$xml->one));
		self::$quantifiers['many'] = explode(',', str_replace(', ', ',', (String)$xml->many));
		self::$quantifiers['or']   = explode(',', str_replace(', ', ',', (String)$xml->or));
		return self::$quantifiers;
	}

	/**
	 * This method loads and builds the structure required
	 * to use and identify the qualifiers, from a passed XML
	 *
	 * @name loadQualifiers
	 * @param SimpleXML $xml
	 * @return AssocArray
	 */
	public static function loadQualifiers($xml)
	{

		self::$qualifiers= Array();
		self::$qualifiers['must']   = explode(',', str_replace(', ', ',', (String)$xml->must));
		self::$qualifiers['may']   = explode(',', str_replace(', ', ',', (String)$xml->may));
		self::$qualifiers['notnull']   = explode(',', str_replace(', ', ',', (String)$xml->notnull));
		self::$qualifiers['key']   = explode(',', str_replace(', ', ',', (String)$xml->key));
		self::$qualifiers['of']   = explode(',', str_replace(', ', ',', (String)$xml->of));
		self::$qualifiers['be']   = explode(',', str_replace(', ', ',', (String)$xml->be));
		self::$qualifiers['coma'] = explode(',', str_replace(', ', ',', (String)$xml->coma));
		return self::$qualifiers;
	}

	/**
	 * This method verifies whether the passed word is
	 * a valid quantifier in the passed list of quantifiers
	 * or if it is a quantifier, in case a list is not sent
	 *
	 * @param string $which In which quantifier the word should be searched
	 * @param string $what The word to be verified
	 * @return boolean
	 */
	public static function isQuantifier($which, $what=false)
	{
		if($what)
			return (isset(self::$quantifiers[$which]) &&
				   in_array($what, self::$quantifiers[$which]));
		foreach(self::$quantifiers as $qt)
		{
			if(in_array($which, $qt))
				return true;
		}
		return false;
	}

	/**
	 * This method verifies whether the passed word is
	 * a valid quantifier in the passed list of qualifiers
	 * or if it is a qualifier, in case a list is not sent
	 *
	 * @param string $which In which qualifier the word should be searched
	 * @param string $what The word to be verified
	 * @return boolean
	 */
	public static function isQualifier($which, $what=false)
	{
		if($what)
			return (isset(self::$qualifiers[$which]) &&
				   in_array($what, self::$qualifiers[$which]));

		foreach(self::$qualifiers as $ql)
		{
			if(in_array($which, $ql))
				return true;
		}
		return false;
	}

	/**
	 * This method is called to load each possible modifier
	 */
	public static function loadModifiers()
	{
		if(!file_exists('sintatics.list'))
		{
			self::loadSintatics(fopen(Mind::$langPath.Mind::$l10n->name.'/sintatics.list', 'rb'));
			$qnt= simplexml_load_file(Mind::$langPath.
									  Mind::$l10n->name.
									  '/quantifiers.xml');
			$qlf= simplexml_load_file(Mind::$langPath.
									  Mind::$l10n->name.
									  '/qualifiers.xml');
			self::loadQuantifiers($qnt);
			self::loadQualifiers($qlf);
		}else{
				self::loadSintatics(fopen('sintatics.list', 'rb'));
				$qnt= simplexml_load_file('quantifiers.xml');
				$qlf= simplexml_load_file('qualifiers.xml');
				self::loadQuantifiers($qnt);
				self::loadQualifiers($qlf);
			 }
		self::$sintaticsList= Array();
	}

	/**
	 * This method runs through all the words within Mind::$content
	 * and perform all verifications
	 *
	 * @return Array the spine, the whole structure of the abstracted text
	 */
	public function sweep()
	{
		$cont= Mind::$content;
		foreach($cont as $word)
		{
			$word= strtolower($word);
			$this->add($word);
		}

		Mind::$syntaxer= new Syntaxer();
		return Token::$spine;
	}

	public function __construct(){
		self::loadModifiers();;
	}
}