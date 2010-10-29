<?php
	 class Create
	 {
	 	public function Create($params)
	 	{
	 		switch($params[0])
			{
				case 'project':
						array_shift($params);
						new CreateProject($params);
					break;
			}
			echo "\n";
	 	}
	 }
