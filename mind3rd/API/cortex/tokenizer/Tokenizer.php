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
	public static $string= '';
	public static $dataTypes= Array();

	/**
	 * Parses a string into an array, splited by comas
	 * @param Mixed $str
	 * @return Array The string splited by comas, ignoring a space after each coma
	 */
	private static function parseByComa($str)
	{
		return explode(',', str_replace(', ', ',', (String)$str));
	}

	/**
	 * This method builds the required structure from the
	 * sent XML file
	 *
	 * @name loadSintatics
	 * @param String $resource
	 * @return AssocArray
	 */
	public static function loadSintatics($resource)
	{
		while (!feof($resource))
		{
			$word= preg_replace(COMA_SEPARATOR, '', fgets($resource, 4096));
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
		self::$quantifiers['none'] = self::parseByComa($xml->none);
		self::$quantifiers['one']  = self::parseByComa($xml->one);
		self::$quantifiers['many'] = self::parseByComa($xml->many);
		self::$quantifiers['or']   = self::parseByComa($xml->or);
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
		self::$qualifiers['must']   = self::parseByComa($xml->must);
		self::$qualifiers['may']    = self::parseByComa($xml->may);
		self::$qualifiers['notnull']= self::parseByComa($xml->notnull);
		self::$qualifiers['key']    = self::parseByComa($xml->key);
		self::$qualifiers['of']     = self::parseByComa($xml->of);
		self::$qualifiers['be']     = self::parseByComa($xml->be);
		self::$qualifiers['coma']   = self::parseByComa($xml->coma);
		return self::$qualifiers;
	}

	/**
	 * Loads the possible types to be accepted
	 * @param SimpleXML $xml
	 * @return Array The parsed avaliable types
	 */
	public static function loadTypes($xml)
	{
		self::$dataTypes['varchar']  = self::parseByComa($xml->varchar);
		self::$dataTypes['char']     = self::parseByComa($xml->char);
		self::$dataTypes['int']      = self::parseByComa($xml->int);
		self::$dataTypes['float']    = self::parseByComa($xml->float);
		self::$dataTypes['boolean']  = self::parseByComa($xml->boolean);
		self::$dataTypes['date']     = self::parseByComa($xml->date);
		self::$dataTypes['time']     = self::parseByComa($xml->time);
		self::$dataTypes['file']     = self::parseByComa($xml->file);
		return self::$dataTypes;
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
	public static function loadModifiers($modifiersSrc= false)
	{
		/*if(self::$quantifiers) // it is already loaded
			return true;*/
		if(!$modifiersSrc && !file_exists('sintatics.list'))
		{
			self::loadSintatics(fopen(Mind::$langPath.Mind::$currentProject['idiom'].
									  '/sintatics.list', 'rb'));
			$qnt= simplexml_load_file(Mind::$langPath.
									  Mind::$currentProject['idiom'].
									  '/quantifiers.xml');
			$qlf= simplexml_load_file(Mind::$langPath.
									  Mind::$currentProject['idiom'].
									  '/qualifiers.xml');
			$tps= simplexml_load_file(Mind::$langPath.
									  Mind::$currentProject['idiom'].
									  '/datatypes.xml');
			self::loadQuantifiers($qnt);
			self::loadQualifiers($qlf);
			self::loadTypes($tps);
		}else{
				self::loadSintatics(fopen($modifiersSrc.'sintatics.list', 'rb'));
				$qnt= simplexml_load_file($modifiersSrc.'quantifiers.xml');
				$qlf= simplexml_load_file($modifiersSrc.'qualifiers.xml');
				$tps= simplexml_load_file($modifiersSrc.'datatypes.xml');
				self::loadQuantifiers($qnt);
				self::loadQualifiers($qlf);
				self::loadTypes($tps);
			 }
		self::$sintaticsList= Array();
	}

	/**
	 * This method runs through all the words within Mind::$content
	 * and perform all verifications
	 *
	 * @return Array the spine, the whole structure of the abstracted text
	 */
	public function sweep($content=false)
	{
		if($content)
			$cont= $content;
		else
			$cont= &Mind::$content;
		//print_r($cont);
		// seek for data types
		foreach(self::$dataTypes as $type=>$options)
		{
			$cont= preg_replace(
				"/\:".implode('(\(| )|\:', $options)."(\(| )/",
				':'.$type.'(',
				$cont
			);
		}

		foreach($cont as $word)
		{
			$word= strtolower($word);
			$this->add($word);
		}
		
		Mind::$syntaxer= new Syntaxer();
		//print_r(Token::$spine);
		return Token::$spine;
	}

	/**
	 * The constructor
	 */
	public function __construct(){
		Token::$spine= Array();
		self::$spine= Array();
		Token::$words= Array();
		Token::$string= "";
		self::loadModifiers();
	}
}