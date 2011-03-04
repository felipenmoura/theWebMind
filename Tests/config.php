<?php
	require_once dirname(__FILE__) . '/../mind3rd/API/classes/Mind.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/classes/MindSpeaker.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/cortex/Darwin/Darwin.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/cortex/Lexer/Lexer.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/cortex/canonic/Canonic.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/cortex/syntaxer/Syntaxer.php';

	require_once dirname(__FILE__) . '/../mind3rd/API/classes/MindEntity.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/utils/constants.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/classes/VersionManager.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/classes/MindProject.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/classes/MindRelation.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/classes/MindEntity.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/classes/MindProperty.php';

	require_once dirname(__FILE__) . '/../mind3rd/API/cortex/analyst/Normal.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/cortex/analyst/Normalizer.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/cortex/analyst/Analyst.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/cortex/tokenizer/Token.php';
	require_once dirname(__FILE__) . '/../mind3rd/API/cortex/tokenizer/Tokenizer.php';
	
	if(!class_exists('MindForUnitTest'))
	{
		class MindForUnitTest
		{
			public function __construct()
			{
				$this->defaults= Array(
					'pk_prefix'=>'pk_',
					'fk_prefix'=>'fk_'
				);
			}
		}
		$_MIND= new MindForUnitTest();
	}
	
	/*if(!defined('_MINDSRC_'))
		define('_MINDSRC_', dirname(__FILE__).'/..');
	if(!defined('_CONSOLE_LINE_LENGTH_'))
		define('_CONSOLE_LINE_LENGTH_', 80);
	$_MIND= new Mind();*/