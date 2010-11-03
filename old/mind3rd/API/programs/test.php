<?php
	 class Test
	 {
	 	public function Test($params)
	 	{
			echo "This is a test. If you see this message, that's because it is working properly.\n";
			if(sizeof($params)>0)
		 		print_r($params);
	 	}
	 }
