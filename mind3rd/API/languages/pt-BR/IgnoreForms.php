<?php
	/**
	 * This class provides a list of instructtions
	 * which define when a word should be ignored,
	 * plus a list of key words to ignore
	 * 
	 * @package cortex.analyst
	 * @author felipe
	 */
	class IgnoreForms {
		/**
		 * The list of words to be ignored
		 * @var Array
		 * @static
		 */
		public static $ignoreList= false;

		/**
		 * A list of rules, to identify words to be ignored
		 * @var Array
		 * @static
		 */
		public static $ignoreRules= Array(
			'/(.)mente$/'
		);

		/**
		 * This method reads the ignore.list file and
		 * parses it to an indexed array
		 * @static
		 * @name loadVerbs
		 */
		public static function loadIgnoreList()
		{
			if(!file_exists('ignore.list'))
				$fR= fopen(Mind::$langPath.Mind::$l10n->name.'/ignore.list', 'rb');
			else
				$fR= fopen('ignore.list', 'rb');
			self::$ignoreList= Array();
			while (!feof($fR)){
				$word= preg_replace('/\s/', '', fgets($fR, 4096));
				self::$ignoreList[$word]= true;
			}
		}

		/**
		 * Returns wheter the word should or not be ignored
		 * @name shouldBeIgnored
		 * @param string $word
		 * @return boolean
		 */
		public static function shouldBeIgnored($word)
		{
			if(!self::$ignoreList)
				self::loadIgnoreList();
			if(isset(self::$ignoreList[$word]))
				return true;
			foreach(self::$ignoreRules as $rule)
			{
				if(preg_match($rule, $word))
					return true;
			}
			return false;
		}

		/**
		 * Returns if the word should be used
		 * @name shouldBeUsed
		 * @static
		 * @param string $word
		 * @return boolean
		 */
		public static function shouldBeUsed($word)
		{
			return !self::shouldBeIgnored($word);
		}
	}