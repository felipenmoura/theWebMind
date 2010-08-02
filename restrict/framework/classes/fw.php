<?php
	class FW
	{
		private $mDir;
		private $pDir;
		
		public function pDir($d)
		{
			$this->pDir= $d;
		}
		
		public function mDir($d)
		{
			$this->mDir= $d;
		}
		
		public function mkDir($name,$permission=0777)
		{
			if(!file_exists($this->pDir.'/'.$name)){
				mkdir($this->pDir.'/'.$name,$permission);
			}
		}
		
		public function mkFile($name, $content)
		{
			if(file_exists($this->pDir))
			{
				$name= $this->pDir.'/'.$name;
				if(!@fopen($name, 'w+'))
					trigger_error("Directory not accessible", E_USER_ERROR);
				file_put_contents($name, $content);
			}else{
					trigger_error("Directory not accessible", E_USER_ERROR);
				 }
		}
		
		public function rename($name,$new_name){
			if(file_exists($this->pDir."/".$name)){
				rename($this->pDir."/".$name,$this->pDir."/".$new_name);
			}
		}
		
		public function getContent($file){
			return file_get_contents($this->mDir."/".$file);
		}
		
		public function __construct(){
		}
	}
?>