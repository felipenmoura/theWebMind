<?php
	$_MIND['errorMessage']= Array();
	$_MIND['errorMessage'][0]= Array("undefined message!", "", "");
	$_MIND['errorMessage'][1]= Array("Could not create directory", "Error when trying to save the project, probably there already were a directory with that name", "Verify the user's temp directory, there must not be any directory with the name of the new project");
	$_MIND['errorMessage'][2]= Array("Not Allowed", "You don't have permission to perform this action", "");
	$_MIND['errorMessage'][3]= Array("Could not save the Project", "The current project could not be saved", "Please, verify if the project still exists, and if your PHP server has access to change files and directories");
	$_MIND['errorMessage'][4]= Array("Project already exists", "The current project already exists, and cannot be replaced", '');
	$_MIND['errorMessage'][9]= Array("Faild creating managing file", "TheWebMind could not create or handle some file. Please, verify your permissions.", '');
	
	class Error
	{
		public $type;
		public $title;
		public $message;
		public $tip;
		public $code;
		
		function __construct($er)
		{
			GLOBAL $_MIND;
			$this->code= $er;
			$this->type= 'error';
			$this->title= $_MIND['errorMessage'][$er][0];
			if($_MIND['errorMessage'][$er][1])
				$this->message= $_MIND['errorMessage'][$er][1];
			if($_MIND['errorMessage'][$er][2])
				$this->tip= $_MIND['errorMessage'][$er][2];
		}
	}
?>