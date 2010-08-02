<?php
/**
*  Bootstrap
*  @filesource
 * @author			Jaydson Gomes
 * @author			Felipe Nascimento
 * @copyright		TheWebMind.org
 * @package			Hello
 * @version			1.0
*/

/** 
 * Errors message. 
 */
error_reporting(E_ALL | E_STRICT);

/**
 * Operantig System detector
 * Credits to this Bootstrap file go to Flavio Gomes da Silva Lisboa (http://www.fgsl.eti.br)
 */
 
 define('PROJECT_NAME', '<projectName>');
 
$operatingSystem =  stripos($_SERVER['SERVER_SOFTWARE'],'win32')!== FALSE ? 'WINDOWS' : 'LINUX';
$bar = $operatingSystem == 'WINDOWS' ? '\\' : '/' ;
$pathSeparator = $operatingSystem == 'WINDOWS' ? ';' : ':' ;
$documentRoot =  $operatingSystem == 'WINDOWS' ? str_replace('/','\\',$_SERVER['DOCUMENT_ROOT']) : $_SERVER['DOCUMENT_ROOT'];

/**
 * Path configuration 
 */
define ('PATH_APPLICATION', substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/index.php')));
$path 	= 	$pathSeparator.$documentRoot.$bar.'library';
$path 	.= 	$pathSeparator.$documentRoot.$bar.PATH_APPLICATION.$bar.'application'.$bar.'models';
set_include_path(get_include_path().$path);

/**
 * Include Component Zend_Loader
 */
include('Zend/Loader.php');

/**
 * Include Zend Registry
 */
Zend_Loader::loadClass('Zend_Registry');

/** 
 * Include Zend Session
 */
Zend_Loader::loadClass('Zend_Session');

/**
 * LoadClass
 */
Zend_Loader::loadClass('Zend_Controller_Front'); 	/** Controllers */
Zend_Loader::loadClass('Zend_View'); 				/** Views */
Zend_Loader::loadClass('Zend_Config_Ini'); 		/** Config */
Zend_Loader::loadClass('Zend_Db'); 				/** Db */
Zend_Loader::loadClass('Zend_Db_Table'); 			/** Tables */
Zend_Loader::loadClass('Zend_Filter_Input'); 		/** Filter */
Zend_Loader::loadClass('Zend_Session'); 			/** Sesssion */
Zend_Loader::loadClass('Zend_Session_Namespace'); /** Session Namespace */

/**
 *  O método set é responsável por armazenar variáveis que podem ser usadas
 *  Store variables and Filter
 */
Zend_Registry::set('post', new Zend_Filter_Input(NULL,NULL,$_POST));
Zend_Registry::set('get', new Zend_Filter_Input(NULL,NULL,$_GET));

/** Views */

/** New object View */
$view = new Zend_View();
 						
/** Encoding */
$view->setEncoding('UTF-8');
$view->setEscape('htmlentities');

/** Views path */
$view->setBasePath('./application/views/');

/** Register the view variable */	
Zend_Registry::set('view', $view); 				

/** Session Init */
Zend_Session::start();

/** Session Handler */
Zend_Registry::set('session',new Zend_Session_Namespace());

/** 
 * Project Controller.
 */
$baseUrl = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/index.php'));

/** New instance of controller class */
$frontController = Zend_Controller_Front::getInstance();

/** Address configuration */
$frontController->setbaseUrl($baseUrl);

/** Controllers dir */
$frontController->setControllerDirectory('./application/controllers');

/** Exceptions */
$frontController->throwExceptions(TRUE);

/**
 * Database configuration
 */
$config = new Zend_Config_Ini('./application/config.ini', 'database');

/** Register variable config */
Zend_Registry::set('config', $config);

/**
 *  Database Connection Configuration
 */
$db = Zend_Db::factory($config->db->adapter, $config->db->config->toArray());
Zend_Db_Table_Abstract::setDefaultAdapter($db);

/** Register variable db */
Zend_Registry::set('db', $db);

/** Monetary */
setlocale(LC_MONETARY,'ptb');

/** 
 * Dispatch
 */
$frontController->dispatch();