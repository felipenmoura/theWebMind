<?php
	require_once dirname(__FILE__) . '/../mind3rd/API/classes/Mind.php';
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