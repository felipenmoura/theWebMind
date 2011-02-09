<?php
	/**
	 * This file is used to define constants that may be
	 * used on the system
	 * It will have the regular expression dictionary, mainly
	 */

	// REGULAR EXPRESSIONS
	define('PROP_DETAILS', "/\(.*/");
	define('PROP_DEFAULT', "/\".*\"/");
	define('PROP_DEFEXEC', "/(^(\"=)|(\"exec\:))|(\"$)/i");
	define('PROP_SIZE', "/\d+(\.?\d+)/");
	define('COMA_SEPARATOR', '/\s/');
	define('SINGLE_COMMENT', '/\/\/.+\n/');
	define('MULTILINE_COMMENT', '/\/\*.+?\*\//');
	define('NEW_LINE', "/\n/");
	define('EXEC_STRING', 'exec:'); // equal(=) is also acceptable