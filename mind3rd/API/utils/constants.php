<?php
	/**
	 * This file is used to define constants that may be
	 * used on the system
	 * It will have the regular expression dictionary, mainly
	 */

	// REGULAR EXPRESSIONS
	define('PROP_DETAILS', "/\(.*/");
	define('PROP_DEFAULT', "/\".*\"/");
	define('PROP_OPTIONS', '/\{(.+?)\}/');
	define('PROP_OPTIONS_CLEAR', '/^\{|\}$/');
	define('PROP_DEFEXEC', "/(^(\"=)|(\"exec\:))|(\"$)/i");
	define('PROP_SIZE', "/\d+(\.?\d+)/");
	define('COMA_SEPARATOR', '/\s/');
	define('SINGLE_COMMENT', '/\/\/.+\n/');
	define('MULTILINE_COMMENT', '/\/\*.+?\*\//');
	define('NEW_LINE', "/\n/");
	define('EXEC_STRING', 'exec:'); // equal(=) is also acceptable
	define('VALID_SUBST_SYNTAX', 'S((( )?\,( )?S)?)+');

	// addresses
	define('PROJECTS_DIR', '/mind3rd/projects/');
	define('MODELS_DIR',   '/mind3rd/API/models/');
	define('ABOUT_INI',    '/mind3rd/env/about.ini');
	define('DEFAULTS_INI', '/mind3rd/env/defaults.ini');
	define('MIND_CONF',    '/mind3rd/env/mind.ini');
	define('L10N_DIR',     '/mind3rd/API/L10N/');
	define('LANG_PATH',    '/mind3rd/API/languages/');