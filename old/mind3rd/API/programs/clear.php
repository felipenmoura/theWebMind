<?php
	class Clear
	{
		public function Clear($params=false){
			if(php_uname('s'))
			{
				if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
					system("clear");
				else
					system("cls");
			}
		}
	}
?>
