<?php
	/*{*/
	$slashes= '';
	if(!is_dir('restrict'))
	{
		$curDir= 'config/mind.php';
		$slashes= '';
		while(!file_exists($curDir))
		{
			$curDir= '../'.$curDir;
			$slashes.= '../';
		}
	}
	$_MIND['rootDir']= $slashes;
	/*}*/
	class Mind
	{
		private $output;
		function apply($str)
		{
			GLOBAL $_MIND;
			reset($_MIND);
			foreach($_MIND as $cur)
			{
				$tmp= (isset($_MIND[key($_MIND)]))? $_MIND[key($_MIND)]: false;
				if(is_string($tmp) || is_bool($tmp))
					$str= str_replace('{?$_MIND[\''.key($_MIND).'\']}', $tmp, $str);
				next($_MIND);
			}
			return $str;
		}
		function import($url)
		{
			ob_start();
			include($url);
			$x= ob_get_contents();
			ob_end_clean();
			return $x;
		}
		function loadXML($file)
		{
			return simplexml_load_file($file);
		}
		function mkXML($file)
		{
			$f = fopen($file,"w+");
			fwrite($f,'<?xml version="1.0" encoding="UTF-8" ?><root></root>');
			fclose($f);
			return simplexml_load_file($file);
		}
		function saveXML($c, $file)
		{
			$f = fopen($file, "w+");
			fwrite($f, $c->asXML());
			fclose($f);
		}
		function getEncodedPwd($n)
		{
			return md5($n);
		}
		function getEncoded($n)
		{
			$n= utf8_decode($n);
			$n= addslashes(strip_tags(preg_replace('/[\!\@\#\$\%\¨\&\*\(\)\\_\-\=\+\^\~\,\.\{\[\]\}\?\"\']\;\/\:/', '', $n)));
			$n= preg_replace('/[áâãà]/i', 'a', $n);
			$n= preg_replace('/[éêèë]/i', 'e', $n);
			$n= preg_replace('/[íìîï]/i', 'i', $n);
			$n= preg_replace('/[óòõôö]/i', 'o', $n);
			$n= preg_replace('/[úùûü]/i', 'u', $n);
			$n= preg_replace('/ç/i', 'c', $n);
			$n= preg_replace('/ñ/i', 'n', $n);
			$n= preg_replace('/^[0-9]/', '_', $n);
			return $n;
		}
		function mountIde()
		{
			GLOBAL $_MIND;
			$ide= file_get_contents($_MIND['fwComponents'].'/ide.php');
			$ide= $this->apply($ide);
			$menus= file_get_contents($_MIND['fwComponents'].'/menus.php');
			$ide= str_replace('{?$_MIND_MENUS}', $menus, $ide);
			$this->output.= $ide.'<br>';
		}
		function output()
		{
			echo $this->output;
			return $this->output;
		}
		function errorOutput($erCod)
		{
			GLOBAL $_MIND;
			include($_MIND['rootDir'].'/'.$_MIND['errorMessagesFile']);
			return new Error($erCod);
		}
		function outputPane($m)
		{
			$this->output.= $m.'<br>';
			echo "<script>outputPane['error']= '".$m."'</script>";
			return $this->output;
		}
		function loadExternal()
		{
			GLOBAL $_MIND;
			reset($_MIND['load']);
			while($m= current($_MIND['load']))
			{
				if(!@include($_MIND['load'][key($_MIND['load'])]))
					$this->outputPane("Error when trying to load the config file <b>".key($_MIND['load'])."</b>");
				next($_MIND['load']);
			}
		}
		public function _contruct()
		{
			$this->output= '';
		}
		public function filter($str)
		{
			return addslashes(strip_tags($str));
		}
		function getLanguages()
		{
			GLOBAL $_MIND;
			$d = dir($_MIND['rootDir'].$_MIND['languageDir']);
			$ar= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0, 1) != '.' && is_dir($_MIND['rootDir'].$_MIND['languageDir']."/".$entry))
				{
					$ar[]= $entry;
				}
				$c++;
			}
			$d->close();
			return $ar;
		}
		function getDBMSs()
		{
			GLOBAL $_MIND;
			$d = dir($_MIND['rootDir'].$_MIND['dbmsDir']);
			$ar= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0, 1) != '.' && is_dir($_MIND['rootDir'].$_MIND['dbmsDir'].'/'.$entry))
				{
					$ar[]= $entry;
				}
				$c++;
			}
			$d->close();
			return $ar;
		}
		function copyDir($source, $dest)
		{
			  // Simple copy for a file
			  if (is_file($source))
			  {
				  $c = copy($source, $dest);
				  chmod($dest, 0777);
				  return $c;
			  }
			  // Make destination directory
			  if (!is_dir($dest))
			  {
				  $oldumask = umask(0);
				  mkdir($dest, 0777);
				  umask($oldumask);
			  }else{
						return false;
				   }
			  // Loop through the folder
			  $dir = dir($source);
			  while (false !== $entry = $dir->read())
			  {
				  // Skip pointers
				  if ($entry == "." || $entry == "..")
				  {
					continue;
				  }
				  // Deep copy directories
				  if ($dest !== "$source/$entry")
				  {
					$this->copyDir("$source/$entry", "$dest/$entry");
				  }
			  }
			  // Clean up
			  $dir->close();
			  return true;
		}
		function treatClientInfo($clientInfo)
		{
			// validate here, the needed verifications on client Info pushed
			//$clientInfo= addslashes($clientInfo);
			return $clientInfo;
		}
		
		function decrypt($text)
		{
			return $text;//base64_decode(convert_uudecode($text));
		}
		function encrypt($text)
		{
			return $text;//convert_uuencode(base64_encode($text));
		}
		function objectToArray($obj)
		{
			return get_object_vars($obj);
		}
		
		function getUsersList($x='')
		{
			GLOBAL $_MIND;
			
			$dir= $x.$_MIND['rootDir'].$_MIND['userDir'];
			$d = dir($dir);
			
			$users= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0,1) !='.')
					array_push($users, $entry);
			}
			return $users;
		}
	}
	$_MIND['fw']= new Mind();
	$_MIND['fw']->loadExternal();
	
	/*$u= new User();
	$u->name('Felipe Nascimento');
	$u->pwd('pqp');
	$u->login('felipe');
	$u->email('felipe@thewebmind.org');
	$u->save();*/
?>