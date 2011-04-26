<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
	 * This file is used to define constants that may be
	 * used on the system
	 * It will have the regular expression dictionary, mainly
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     */

	// REGULAR EXPRESSIONS
	define('PROP_DETAILS',        "/\(.*/");
	define('PROP_DEFAULT',        "/(?<!\\\)\".+?(?<!\\\)\"/");
	define('PROP_OPTIONS',        "/\{(.+?)\}/");
	define('PROP_OPTIONS_CLEAR',  "/^\{|\}$/");
	define('PROP_DEFEXEC',        "/(^(\"=)|(\"exec\:))|(\"$)/i");
	define('PROP_SIZE',           "/\d+(\.?\d+)?/");
	define('COMA_SEPARATOR',      "/\s/");
	define('SINGLE_COMMENT',      "/\/\/.+\n/");
	define('MULTILINE_COMMENT',   "/\/\*.+?\*\//");
	define('NEW_LINE',            "/\n/");
	define('EXEC_STRING',         "exec:"); // equal(=) is also acceptable
	define('VALID_SUBST_SYNTAX',  "S((( )?\,( )?S)?)+");
	define('COMPOSED_SUBST',      "/SCS/");
	define('FIX_PROP_NAME',       "/\\\|\,|\./");
	define('IMPORT_SOURCE',       "/@import [a-z0-9_\-\/\\\]+/i");
	define('PROP_FIX',            "((\(|[\., \n]))?+|\:");
	define('BETWEEN_QUOTES',      "/^(\\\)\"|(\\\)\"$/");

	// addresses
	define('PROJECTS_DIR',        '/mind3rd/projects/');
	define('MODELS_DIR',          '/mind3rd/API/models/');
	define('ABOUT_INI',           '/mind3rd/env/about.ini');
	define('DEFAULTS_INI',        '/mind3rd/env/defaults.ini');
	define('MIND_CONF',           '/mind3rd/env/mind.ini');
	define('L10N_DIR',            '/mind3rd/API/L10N/');
	define('LANG_PATH',           '/mind3rd/API/languages/');
	define('SQLITE',              '/mind3rd/SQLite/mind');

	// other constants
	define('QUANTIFIER_MAX_MAX',  'n');
	define('QUANTIFIER_MAX_MIN',   1);
	define('PROPERTY_SEPARATOR',  "_");
	define('AUTOINCREMENT_DEFVAL', 'AUTO');
	define('COMMIT_STATUS_OK'  ,   0);
	define('COMMIT_STATUS_CHANGED',1);
	define('COMMIT_STATUS_DROP',   2);