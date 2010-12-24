<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// Tokens to be used
// MT stands for MindToken
define('MT_PERIOD'  , -2);
define('MT_COMA'    , -1);
define('MT_VOID'    , 0);
define('MT_VERB'    , 1);
define('MT_SUBST'   , 2);
define('MT_NONE'    , 4);
define('MT_ONE'     , 8);
define('MT_OR'      , 16);
define('MT_MANY'    , 32);
define('MT_QMUST'   , 64);
define('MT_QMAY'    , 128);
define('MT_QNOTNULL', 254);
define('MT_QKEY'    , 564);
define('MT_QOF'     , 1024);
define('MT_QBE'     , 2048);
define('MT_ANY'     , 4096);

/**
 * This class will apply the most important tokens to be used.
 * It also builds the main structure to compare it to avaliable
 * sintatics. This structure is called SPINE
 *
 * @author felipe
 */
class Tokenizer {

	public static $sintaticsList;
	public static $quantifiers;
	public static $qualifiers;
	public static $spine='';

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
		return self::$qualifiers;
	}

	/**
	 * This method verifies whether the passed word is
	 * a valid quantifier in the passed list of quantifiers
	 *
	 * @param string $which In which quantifier the word should be searched
	 * @param string $what The word to be verified
	 * @return boolean
	 */
	public static function isQuantifier($which, $what)
	{
		return (isset(self::$quantifiers[$which]) &&
			   in_array($what, self::$quantifiers[$which]));
	}

	/**
	 * This method verifies whether the passed word is
	 * a valid quantifier in the passed list of qualifiers
	 *
	 * @param string $which In which qualifier the word should be searched
	 * @param string $what The word to be verified
	 * @return boolean
	 */
	public static function isQualifier($which, $what)
	{
		return (isset(self::$qualifiers[$which]) &&
			   in_array($what, self::$qualifiers[$which]));
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
	 */
	public function sweep()
	{
		self::loadModifiers();
		$cont= Mind::$content;
		foreach($cont as $word)
		{
			$word= strtolower($word);
			if(IgnoreForms::shouldBeIgnored($word))
			{
				self::$spine[]= MT_ANY;
				continue;
			}
			if($word==',')
			{
				self::$spine[]= MT_COMA;
				continue;
			}
			if($word=='.')
			{
				self::$spine[]= MT_PERIOD;
				continue;
			}
			// let's check for quantifiers
			if(self::isQuantifier('none', $word))
			{
				self::$spine[]= MT_NONE;
				continue;
			}
			if(self::isQuantifier('one', $word))
			{
				self::$spine[]= MT_ONE;
				continue;
			}
			if(self::isQuantifier('many', $word))
			{
				self::$spine[]= MT_MANY;
				continue;

			}
			if(self::isQuantifier('or', $word))
			{
				self::$spine[]= MT_OR;
				continue;
			}
			// and here, the qualifiers
			if(self::isQualifier('must', $word))
			{
				self::$spine[]= MT_QMUST;
				continue;
			}
			if(self::isQualifier('may', $word))
			{
				self::$spine[]= MT_QMAY;
				continue;
			}
			if(self::isQualifier('notnull', $word))
			{
				self::$spine[]= MT_QNOTNULL;
				continue;
			}
			if(self::isQualifier('of', $word))
			{
				self::$spine[]= MT_QOF;
				continue;
			}
			if(self::isQualifier('be', $word))
			{
				self::$spine[]= MT_QBE;
				continue;
			}
			if(self::isQualifier('key', $word))
			{
				self::$spine[]= MT_QKEY;
				continue;
			}
			// we know these words are already on its
			// canonic form, so, we can simply look for
			// it on the list
			if(Verbalizer::isInVerbList($word))
			{
				self::$spine[]= MT_VERB;
				continue;
			}
			self::$spine[]= MT_SUBST;
		}
		print_r(self::$spine); // AQUI
		print_r(Mind::$content);
	}
}
