<?php
	class User
	{
		public $name= "";
		public $code= "";
		public $login= "";
		public $pwd= '';
		public $status= '1';
		public $age= '0';
		public $email= "";
		public $description= "";
		public $position= "";
		
		function code()
		{
			if($n)
				$this->code= $n;
			else
				return $this->login;
		}
		function name($n=false)
		{
			if($n)
				$this->name= $n;
			else
				return $this->name;
		}
		function login($n=false)
		{
			if($n)
			{
				GLOBAL $_MIND;
				$this->login= $_MIND['fw']->getEncoded($n);
			}else
				return $this->login;
		}
		function pwd($n=false)
		{
			if($n)
			{
				GLOBAL $_MIND;
				$this->pwd= $_MIND['fw']->getEncodedPwd($n);
			}else
				return $this->pwd;
		}
		function status($n=false)
		{
			if($n!==false)
				$this->status= ($n==0)? '0': '1';
			else
				return $this->status;
		}
		function age($n=false)
		{
			if($n && is_numeric($n))
				$this->age= $n;
			else
				return $this->age;
		}
		function email($n=false)
		{
			if($n)
				$this->email= $n;
			else
				return $this->email;
		}
		function description($n=false)
		{
			if($n)
				$this->description= $n;
			else
				return $this->description;
		}
		function position($n=false)
		{
			if($n)
				$this->position= $n;
			else
				return $this->position;
		}
		function loadUser($l, $p)
		{
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'];
			$l= $_MIND['fw']->getEncoded($l);
			$p= $_MIND['fw']->getEncodedPwd($p);
			$cF= $_MIND['userDir'].'/'.$l.'/'.$_MIND['userConfFile'].'.xml';
			if(!file_exists($cF))
				$bg= $dir;
			else
				$bg= '';
			$flag= false;
			
			if($xml= @simplexml_load_file($bg.$cF))
			{
				if($xml->pwd['value']== $p && $xml->login['value']== $l)
				{
					$xmlInfo= @simplexml_load_file($bg.$_MIND['userDir'].'/'.$l.'/info.xml');
					$this->login((string)$xml->login['value']);
					$this->code($this->login());
					$this->pwd('');
					$this->name(utf8_decode((string)$xmlInfo->name['value']));
					$this->status((string)$xml->status['value']);
					$this->email(utf8_decode((string)$xml->email['value']));
					$this->age((string)$xmlInfo->age['value']);
					$this->description(utf8_decode((string)$xmlInfo->description['value']));
					$this->position(utf8_decode((string)$xmlInfo->position['value']));
					$flag= true;
				}else{
						if(!$p)
						{
							$xmlInfo= @simplexml_load_file($bg.$_MIND['userDir'].'/'.$l.'/info.xml');
							$this->login((string)$xml->login['value']);
							$this->code($this->login());
							$this->pwd('');
							$this->name(utf8_decode((string)$xmlInfo->name['value']));
							$this->status((string)$xml->status['value']);
							$this->email(utf8_decode((string)$xml->email['value']));
							$this->age((string)$xmlInfo->age['value']);
							$this->description(utf8_decode((string)$xmlInfo->description['value']));
							$this->position(utf8_decode((string)$xmlInfo->position['value']));
						}else
							$_MIND['fw']->outputPane('Invalid current password');
					 }
			}else{
					$_MIND['fw']->outputPane('Error when trying to load the user information');
				 }
			return ($flag)? $this: false;
		}
		function userExists($u)
		{
			GLOBAL $_MIND;
			return file_exists($_MIND['rootDir'].$_MIND['userDir'].'/'.$u);
		}
		function getUsers()
		{
			GLOBAL $_MIND;
			
			$dir= $_MIND['rootDir'].$_MIND['userDir'];
			
			$d = dir($dir);
			$users= Array();
			$c=0;
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0,1) !='.')
				{
					$users[$c]= new User();
					$xmlInfo= @simplexml_load_file($dir.'/'.$entry.'/info.xml');
					$xml= @simplexml_load_file($dir.'/'.$entry.'/'.$_MIND['userConfFile'].'.xml');
					$users[$c]->login((string)$xml->login['value']);
					$users[$c]->code($users[$c]->login());
					$users[$c]->pwd('');
					$users[$c]->name(utf8_decode((string)$xmlInfo->name['value']));
					$users[$c]->status((string)$xml->status['value']);
					$users[$c]->email(utf8_decode((string)$xml->email['value']));
					$users[$c]->age((string)$xmlInfo->age['value']);
					$users[$c]->description(utf8_decode((string)$xmlInfo->description['value']));
					$users[$c]->position(utf8_decode((string)$xmlInfo->position['value']));
					$c++;
				}
			}
			$d->close();
			return $users;
		}
		function populate($ar)
		{
			$this->code($ar['login']);
			$this->name($ar['name']);
			$this->login($ar['login']);
			$this->age($ar['age']);
			$this->pwd($ar['pwd']);
			$this->status($ar['status']);
			$this->email($ar['email']);
			$this->description($ar['description']);
			$this->position($ar['position']);
		}
		function save($objUser=false)
		{
			GLOBAL $_MIND;
			$dir= $_MIND['rootDir'];
			$objUser= ($objUser)? $objUser: $this;
			$dir= $dir.$_MIND['userDir'].'/'.$objUser->login();
			if(trim($this->name)!= ''
				&& trim($this->login)!= ''
				&& trim($this->pwd)!= ''
				&& trim($this->email)!= ''
				&& (trim($this->status)== '0' || trim($this->status)== '1'))
			{
				if(!file_exists($dir.'/'.$_MIND['userConfFile'].'.xml'))
				{
					if(!@mkdir($dir))
						return false;
					mkdir($dir.'/temp');
				}
				$confF= $dir.'/'.$_MIND['userConfFile'].'.xml';
				$infoF= $dir.'/'.'info.xml';
				$conf= $_MIND['fw']->mkXML($confF);
				$info= $_MIND['fw']->mkXML($infoF);
				
				$conf->addChild('login');
				$conf->login['value']= $this->login();
				$conf->addChild('pwd');
				$conf->pwd['value']= $this->pwd();
				$conf->addChild('status');
				$conf->status['value']= $this->status();
				$conf->addChild('email');
				$conf->email['value']= utf8_encode($this->email());
				
				$info->addChild('name');
				$info->name['value']= utf8_encode($this->name());
				$info->addChild('age');
				$info->age['value']= $this->age();
				$info->addChild('description');
				$info->description['value']= utf8_encode($this->description());
				$info->addChild('position');
				$info->position['value']= utf8_encode($this->position());
				
				$_MIND['fw']->saveXML($conf, $confF);
				$_MIND['fw']->saveXML($info, $infoF);
				return true;
			}else{
					$_MIND['fw']->outputPane("Invalid or insuficient user data");
					return false;
				 }
		}
		public function __construct($l=false, $p=false)
		{
			if($l)
			{
				$this->loadUser($l, $p);
			}
		}
	}
?>
