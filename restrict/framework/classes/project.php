<?php
/*
	CLass: Project 1.0
	Definition: class used to manage the Project information.
	Author: Felipe Nascimento
	
	Observations:
		* always save BEFORE processing
*/
	class Project
	{
		public $name;
		public $lang;
		public $dbms;
		public $description;
		public $environment;
		public $users;
		public $owner;
		public $date;
		public $email;
		public $processed;
		public $version;
		public $knowledge;
		public $ddl;
		public $maskedDDL;
		public $dbmsObj;
		public $fkDDL= Array();
		public $wml; // WebMindLanguage
		
		static function getProjects($usr)
		{
			GLOBAL $_MIND;
			$d = dir($_MIND['rootDir'].$_MIND['userDir'].'/'.$usr.'/temp');
			$c=0;
			$ar= Array();
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0, 1) != '.' && is_dir($_MIND['rootDir'].$_MIND['userDir'].'/'.$usr.'/temp/'.$entry))
				{
					$ar[$c]= new Project($entry);
				}
				$c++;
			}
			$d->close();
			if($c==0)
				return false;
			else
				return $ar;
		}
		static function getProject($cod, $usr)
		{
			GLOBAL $_MIND;
			$d = dir($_MIND['rootDir'].$_MIND['userDir'].'/'.$usr.'/temp');
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0, 1) != '.')
				{
					if($entry == $cod)
					{
						$p= new Project($entry);
						break;
					}
				}
			}
			$d->close();
			if($p)
				return $p;
			else
				return false;
		}
		static function projectExists($cod)
		{
			GLOBAL $_MIND;
			return file_exists($_MIND['rootDir'].$_MIND['publishDir'].'/'.$cod);
		}
		function save()
		{// atualiza ou cria os arquivos referentes ao projeto, no diretório do usuario
			GLOBAL $_MIND;
			$this->name= str_replace(' ', '_', $_MIND['fw']->getEncoded($this->name));
			
			$publishDir= $_MIND['rootDir'].$_MIND['publishDir'].'/'.$this->name;
			if($this->projectExists($this->name))
			{// atualiza
				$creating= false;
				$publConfF= $_MIND['fw']->mkXML($_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$this->name.'/conf.xml');
				$publInfoF= $_MIND['fw']->mkXML($_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$this->name.'/info.xml');
				$WebMindLanguage= $_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$this->name.'/mind/mind_code.php';
			}else{// cria
					$creating= true;
					@mkdir($publishDir);
					@mkdir($publishDir.'/mind');
					@mkdir($publishDir.'/root');
					@mkdir($publishDir.'/root/documentation');
					$WebMindLanguage= $publishDir.'/mind/mind_code.php';
					$f= fopen($WebMindLanguage, 'w+'); fclose($f);
					$publConfF= $_MIND['fw']->mkXML($publishDir.'/conf.xml');
					$publInfoF= $_MIND['fw']->mkXML($publishDir.'/info.xml');
				 }
			
			$publConfF->addChild('name');
			$publConfF->name['value']= $this->name;
			$publConfF->addChild('date');
			$publConfF->date['value']= @date('m/d/Y - H:i:s');
			$publConfF->addChild('owner');
			$publConfF->owner['value']= $_SESSION['user']['login'];
			$publConfF->addChild('processed');
			$publConfF->processed['value']= ($this->processed)? 'true':'false';
			
			$publConfF->addChild('version');
			$publConfF->version['value']= $this->version[0];
			$publConfF->addChild('subVersion');
			$publConfF->subVersion['value']= $this->version[1];
			$publConfF->addChild('update');
			$publConfF->update['value']= $this->version[2];
			
			$publConfF->addChild('lang');
			$publConfF->lang['value']= $this->lang;
			$publConfF->addChild('dbms');
			$publConfF->dbms['value']= $this->dbms;
			$publConfF->addChild('users');
			$c=0;
			foreach($this->users as $u)
			{
				$publConfF->users->addChild('user');
				$publConfF->users->user[$c]['login']= $u;
				$c++;
			}
			
			$publConfF->addChild('environment');
			$publConfF->environment->addChild('development');
			$publConfF->environment->development['dbAddress']	= $this->environment['development']['dbAddress'];
			$publConfF->environment->development['dbName']	 	= $this->environment['development']['dbName'];
			$publConfF->environment->development['dbPort']	 	= $this->environment['development']['dbPort'];
			$publConfF->environment->development['rootUser'] 	= $this->environment['development']['rootUser'];
			$publConfF->environment->development['rootUserPwd'] = $_MIND['fw']->encrypt($this->environment['development']['rootUserPwd']);
			$publConfF->environment->development['user']		= $this->environment['development']['user'];
			$publConfF->environment->development['userPwd']		= $_MIND['fw']->encrypt($this->environment['development']['userPwd']);
			
			$publConfF->environment->addChild('production');
			$publConfF->environment->production['dbAddress']	= $this->environment['production']['dbAddress'];
			$publConfF->environment->production['dbName']	 	= $this->environment['production']['dbName'];
			$publConfF->environment->production['dbPort']	 	= $this->environment['production']['dbPort'];
			$publConfF->environment->production['rootUser'] 	= $this->environment['production']['rootUser'];
			$publConfF->environment->production['rootUserPwd']  = $_MIND['fw']->encrypt($this->environment['production']['rootUserPwd']);
			$publConfF->environment->production['user']			= $this->environment['production']['user'];
			$publConfF->environment->production['userPwd']		= $_MIND['fw']->encrypt($this->environment['production']['userPwd']);
			
			$publInfoF->addChild('description', $this->description);
			$publInfoF->addChild('email');
			$publInfoF->email['value']= $this->email;
			if($creating)
			{
				$_MIND['fw']->saveXML($publConfF, $publishDir.'/conf.xml');
				$_MIND['fw']->saveXML($publInfoF, $publishDir.'/info.xml');
				foreach($this->users as $u)
				{
					$userDir = $_MIND['rootDir'].$_MIND['userDir'].'/'.$u.'/temp/'.$this->name;
					if(!@$_MIND['fw']->copyDir($publishDir, $userDir))
					{
						echo "Mind.Dialog.ShowError(".JSON_encode($_MIND['fw']->errorOutput(1)).")";
						exit;
					}
				}
			}else{
					$_MIND['fw']->saveXML($publConfF, $_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$this->name.'/conf.xml');
					$_MIND['fw']->saveXML($publInfoF, $_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$this->name.'/info.xml');
				 }
			
			file_put_contents($WebMindLanguage, $this->wml);
			return true;
		}
		
		function preparePost($p)
		{
			$p['environment']= Array();
			$p['environment']['development']= Array();
			$p['environment']['development']['dbAddress']= $p['dbAddress1'];
			$p['environment']['development']['dbName']= $p['dbName1'];
			$p['environment']['development']['dbPort']= $p['port1'];
			$p['environment']['development']['rootUser']= $p['userRoot1'];
			$p['environment']['development']['rootUserPwd']= $p['userRootPwd1'];
			$p['environment']['development']['user']= $p['user1'];
			$p['environment']['development']['userPwd']= $p['userPwd1'];
			$p['environment']['production']= Array();
			$p['environment']['production']['dbAddress'] = $p['dbAddress2'];
			$p['environment']['production']['dbName'] = $p['dbName2'];
			$p['environment']['production']['dbPort'] = $p['port2'];
			$p['environment']['production']['rootUser'] = $p['userRoot2'];
			$p['environment']['production']['rootUserPwd'] = $p['userRootPwd2'];
			$p['environment']['production']['user'] = $p['user2'];
			$p['environment']['production']['userPwd'] = $p['userPwd2'];
			return $p;
		}
		
		function remove($projectName)
		{
			GLOBAL $_MIND;
			if($this->hasProject($projectName))
			{
				$dir = $_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/';
				$dirP = $_MIND['rootDir'].$_MIND['publishDir']."/";
				if(file_exists($dirP.".".$projectName)){
					$_MIND["fw"]->deleteDirectory($dir.".".$projectName);
					$_MIND["fw"]->deleteDirectory($dirP.".".$projectName);
				}
				rename($dir.$projectName, $dir.".".$projectName);
				rename($dirP.$projectName, $dirP.".".$projectName);
				echo "success";
			}else{
				echo "Mind.Dialog.ShowError(".JSON_encode($_MIND['fw']->errorOutput(5)).")";
			}
		}
		function populate($ar)
		{
			//GLOBAL $_MIND;
			if(isset($ar['name'])) $this->name					= $ar['name'];
			if(isset($ar['lang'])) $this->lang					= $ar['lang'];
			if(isset($ar['dbms'])) $this->dbms					= $ar['dbms'];
			if(isset($ar['description'])) $this->description	= $ar['description'];
			if(isset($ar['wml']))  $this->wml					= $ar['wml'];
			if(isset($ar['environment']['development']))
			{
				$this->environment['development']['dbAddress']	= $ar['environment']['development']['dbAddress'];
				$this->environment['development']['dbName']	 	= $ar['environment']['development']['dbName'];
				$this->environment['development']['dbPort']	 	= $ar['environment']['development']['dbPort'];
				$this->environment['development']['rootUser']  	= $ar['environment']['development']['rootUser'];
				$this->environment['development']['rootUserPwd']= $ar['environment']['development']['rootUserPwd'];
				$this->environment['development']['user']		= $ar['environment']['development']['user'];
				$this->environment['development']['userPwd']	= $ar['environment']['development']['userPwd'];
			}
			
			if(isset($ar['environment']['production']))
			{
				$this->environment['production']['dbAddress']	= $ar['environment']['production']['dbAddress'];
				$this->environment['production']['dbName']		= $ar['environment']['production']['dbName'];
				$this->environment['production']['dbPort']		= $ar['environment']['production']['dbPort'];
				$this->environment['production']['rootUser']	= $ar['environment']['production']['rootUser'];
				$this->environment['production']['rootUserPwd']	= $ar['environment']['production']['rootUserPwd'];
				$this->environment['production']['user']		= $ar['environment']['production']['user'];
				$this->environment['production']['userPwd']		= $ar['environment']['production']['userPwd'];
			}
			
			if(isset($ar['users'])) $users= $ar['users']; else $users= false;
			
			if(@is_array($users))
			{
				foreach($users as $u)
				{
					if(User::userExists($u))
						if(!in_array($u, $this->users))
							$this->users[]= $u;
				}
			}else{
				 }
			
			if(!isset($ar['version']))
				$ar['version']= '0,0,1';
			elseif(is_array($ar['version']))
					$ar['version']= implode(',', $ar['version']);
			if(strstr($ar['version'], ','))
			{
				$ar['version']= explode(',', $ar['version']);
				$ar['subVersion']= $ar['version'][1];
				$ar['update']= $ar['version'][2];
				$ar['version']= $ar['version'][0];
			}
			
			if(isset($ar['version']))
				$this->version[0]= $ar['version'];
			if(isset($ar['subVersion']))
				$this->version[1]= $ar['subVersion'];
			if(isset($ar['update']))
				$this->version[2]= $ar['update'];
		}
		public function saveAsWML()
		{
			GLOBAL $_MIND;
			$fileToSave= $_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$this->name.'/mind/'.$this->name.'_'.(date('m-d-Y')).'.wml';
			if(file_exists($fileToSave))
				unlink($fileToSave);
			if(!@fopen($fileToSave, 'w+'))
				echo "Mind.Dialog.ShowError(".JSON_encode($_MIND['fw']->errorOutput(9)).")";
			else
				file_put_contents($fileToSave, '<?wml version="1.0"?>
'.$this->wml);
		}
		static function hasProject($prj)
		{
			GLOBAL $_MIND;
			return file_exists($_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$prj);
		}
		function load($prj)
		{// carrega e popula o objeto com as informações do objeto
			GLOBAL $_MIND;
			$dirToLoad= $_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/';
			if(!file_exists($_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$this->name))
			{
				echo "Mind.Dialog.ShowError(".JSON_encode($_MIND['fw']->errorOutput(2)).")";
				exit;
			}
			$info= $_MIND['fw']->loadXML($dirToLoad.$prj.'/info.xml');
			$conf= $_MIND['fw']->loadXML($dirToLoad.$prj.'/conf.xml');
			$this->name= (string)$conf->name['value'];
			$this->date= (string)$conf->date['value'];
			$this->email= (string)$info->email['value'];
			$this->owner= (string)$conf->owner['value'];
			$this->lang= (string)$conf->lang['value'];
			$this->dbms= (string)$conf->dbms['value'];
			$this->processed= (string)$conf->processed['value'];
			$this->description= (string)$info->description;
			
			$this->version= Array();
			$this->version[0]= (integer)$conf->version['value'];
			$this->version[1]= (integer)$conf->subVersion['value'];
			$this->version[2]= (integer)$conf->update['value'];
			
			$this->environment= Array();
			$this->wml= file_get_contents($dirToLoad.$prj.'/mind/mind_code.php');
			$this->originalWML= $this->wml;
			
			foreach($conf->environment->development->attributes() as $a => $b)
			{
				$this->environment['development'][$a]= (string)$b;
			}
			$this->environment['development']['rootUserPwd']= $_MIND['fw']->decrypt($this->environment['development']['rootUserPwd']);
			$this->environment['development']['userPwd']= $_MIND['fw']->decrypt($this->environment['development']['rootUserPwd']);
			foreach($conf->environment->production->attributes() as $a => $b)
			{
				$this->environment['production'][$a]= (string)$b;
			}
			$this->environment['production']['rootUserPwd']= $_MIND['fw']->decrypt($this->environment['production']['rootUserPwd']);
			$this->environment['production']['userPwd']= $_MIND['fw']->decrypt($this->environment['production']['rootUserPwd']);
			
			$this->users= Array();
			foreach($conf->users->user as $tmpUser)
			{
				$this->users[]= (string)$tmpUser['login'];
			}
			if(!isset($_SESSION['current']))
				$_SESSION['current']= Array();
			$_SESSION['current']['lang']= $this->lang;
			$_SESSION['current']['dbms']= $this->dbms;
			$_SESSION['current']['name']= $this->name;
			return $this;
		}
		
		function process()
		{
			include('mind_processor.php');
			$m= new MindProcessor($this);
			$this->knowledge= $m;
			return true;
		}
		
		function run()
		{
			$time= microtime();
			if($this->process())
			{
				$this->generateQueries();
				
				unset($this->knowledge->especialChars);
				unset($this->knowledge->fixedChars);
				unset($this->knowledge->relations);
				unset($this->knowledge->tmpSentences);
				unset($this->knowledge->processedWML);
				unset($this->knowledge->unique);
				unset($this->knowledge->required);
				unset($this->knowledge->verbId);
				unset($this->knowledge->quantifierId);
				unset($this->knowledge->quantifiers);
				
				$this->saveAsWML();
				
				$this->knowledge->DDL= $this->maskedDDL;
				
				$time= microtime() - $time;
				$this->knowledge->processTime= $time;
				
				$this->knowledge->debug['messages'][]= Array(4,
															 "Finished at ".(date('m/d/Y H:i:s')).' - User: '.$_SESSION['user']['login'].'<br/>'.
															 " &nbsp; &nbsp; &nbsp; ".$this->lang.' &nbsp; &nbsp; &nbsp; '. sizeof($this->knowledge->tables).
															 " tables &nbsp; &nbsp; &nbsp; ".sizeof($this->knowledge->sentences).' sentences'.
															 ' &nbsp; &nbsp; &nbsp; in '.$time.' seconds');
				$this->knowledge->debug['processTime']= $time;
				$this->knowledge->debug['status']= '1';
				//$this->knowledge->debug['messages']= Array();
				
				return $this->knowledge;
			}else{
					echo JSON_encode($_MIND['fw']->errorOutput(3));
				 }
		}
		function debug()
		{
			/*
				1-envia o codigo para o mind interpretar, e retornar o codigo inalizado
				2-abre modal pane exibindo o debug do codigo, insinuando o que e como o mind interpretará o mesmo, quando for rodar
			*/
		}
		function execute()
		{// link para a ultima versão gerada
		}
		function publish() // generate Version
		{// salva no diretorio dos projetos
			/*
				1-caso o usuario for o criador do projeto, verificar se o projeto tem alterações nas conf e info, para salvar antes(caso tenha, confirmar ação)
				2-verificar se o codigo foi alterado desde a ultima vez em que foi rodado, se sim, roda-lo
				3-copiar o projeto do diretorio de projetos para um backup
				4-criar um log com dados do usuario que gerou a ultima vez, horario, ip, etc
				5-enviar o projeto do diretorio temp do usuario para o diretorio dos projetos, alterando o terceiro dado da versao (z.y.x+1)
			*/
		}
		function export($onlyDDL= false)
		{// cria um arquivo e coloca para download com a extensao .mnd
			GLOBAL $_MIND;
			$this->process();
			header('Content-type: package/mnd');
			if($onlyDDL)
				header('Content-Disposition: attachment; filename="'.trim($this->name).'.sql"');
			else
				header('Content-Disposition: attachment; filename="'.trim($this->name).'.mnd"');
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			
			if($onlyDDL)
			{
				echo $_SESSION['currentDDL'];
				exit;
			}
			
			echo $_MIND['wmlVersion'][$_MIND['wmlCurrentVersion']].'
';
			$ar= Array();
			$ar['wml']= $this->wml;
			
			unset ($this->wml);
			unset ($this->knowledge);
			unset ($this->maskedDDL);
			unset ($this->ddl);
			
			$ar['projectData']= $_MIND['fw']->objectToArray($this);
			$ar['originalWML']= $_MIND['fw']->fixName($this->originalWML);
			
			echo JSON_encode($ar);
			
			exit;
		}
		function import()
		{// carrega um arquivo para criar o projeto
			// se o projeto ja existir no diretorio de projetos do usuario q está importando, deverá sobrescrever o mesmo, apos uma confirmação.
			// senao
				// se o projeto ja existe no diretorio global de projetos
					// retorna um erro para o usuario, informando q o projeto ja existe e que ele nao tem permissoes para substituí-lo
				// senao, cria um novo projeto colocando o usuario atual como criador.
		}
		function __construct($pName=false, $pUser=false)
		{
			if($pName)
				$this->name= $pName;
			$this->environment= Array();
			$this->environment['development']= Array();
			$this->environment['production']= Array();
			$this->users= Array();
			$this->processed= false;
			$this->email= $_SESSION['user']['email'];
			$this->owner=$_SESSION['user']['login'];
			$this->version= Array();
			$this->version[0]= 0;
			$this->version[1]= 0;
			$this->version[2]= 1;
			$this->originalWML= '';
			$this->wml= '';
			
			if($pUser && $pName)
			{
				$this->load($pName, $pUser);
			}
		}
		
		//	GENERATING QUERIES
		public function generateQueries()
		{
			if($this->knowledge)
			{
				GLOBAL $_MIND;
				$dbmsDir= $_MIND['rootDir'].$_MIND['dbmsDir'];
				//echo $dbmsDir;
				include($dbmsDir.'/dbms_interface.php');
				include($dbmsDir.'/'.$this->dbms.'/'.$this->dbms.'.php');
				$this->dbmsObj= new $this->dbms;
				$dbms= &$this->dbmsObj;
				
				$dbms= new $this->dbms;
				$this->ddl= strip_tags($dbms->getHeader());
				$tables= $this->knowledge->tables;
				$this->maskedDDL= $dbms->getHeader();
				$fkDDL= '';
				foreach($tables as $table)
				{
					$tmpDDL= $dbms->createTable();
					$tmpDDL= preg_replace('/<tablename>/', $table->name, $tmpDDL);
					$c= 0;
					$attDDL= '';
					foreach($table->attributes as $att)
					{
						$tmpAttDDL= '';
						if($c==0)
						{
							$pk= $att;
							$tmpAttDDL.= $dbms->defaultPrimaryKey();
							$tmpAttDDL= preg_replace('/<fieldname>/', trim($att->name), $tmpAttDDL);
							$tmpAttDDL= '	'.preg_replace('/<tablename>/', trim($table->name), $tmpAttDDL);
								$tmpAttDDL.= ',
';
						}else{
								$tmpType= preg_replace('/<length>/',
								$att->size,
								$dbms->attType[$att->type]);
								
								$tmpAttDDL= preg_replace('/<fieldname>/', trim($att->name), '	'.trim($dbms->createField()).',
');
								$tmpAttDDL= preg_replace('/<fieldtype>/', $tmpType, $tmpAttDDL);
								if(trim($att->defaultValue) != '')
								{
									$tmpAttDDL= preg_replace('/<defaultvalue>/', preg_replace('/<defaultvalue>/', $att->defaultValue, $dbms->setDefaultValue()), $tmpAttDDL);
								}else{
										$tmpAttDDL= preg_replace('/<defaultvalue>/', preg_replace('/<defaultvalue>/', "", ''), $tmpAttDDL);
									 }
								$tmpAttDDL= preg_replace('/<allownull>/', (($att->required)? 'not null': ''), $tmpAttDDL);
							 }
						$attDDL.= $tmpAttDDL;
						$c++;
					}
					foreach($table->foreignKeys as $fk)
					{
						$tmpAttDDL= preg_replace('/<fieldname>/', $fk[0], '	'.trim($dbms->createField()).',
');
						$tmpAttDDL= preg_replace('/<fieldtype>/', $dbms->attType['integer'], $tmpAttDDL);
						$attDDL.= $tmpAttDDL;
					}
					$attDDL.= '	'.preg_replace('/<pk>/', $pk->name, $dbms->createPK());
					$tmpDDL= preg_replace('/<fields>/', $attDDL, $tmpDDL).'

';
					$this->ddl.= '
'.strip_tags($tmpDDL);

					$this->knowledge->tables[$table->name]->DDL= strip_tags($tmpDDL);
					
					$this->maskedDDL.= preg_replace('/( +),/', ',', $tmpDDL);
					foreach($table->foreignKeys as $fk)
					{
						$tmpFK= preg_replace('/<table>/', $table->name, $dbms->createFK());
						$tmpFK= preg_replace('/<references>/', $fk[1], $tmpFK);
						$tmpFK= preg_replace('/<references_pk>/', $_MIND['primaryKeyPrefix'].$fk[1], $tmpFK);
						$tmpFK= preg_replace('/<fk>/', $fk[0], $tmpFK);
						$this->fkDDL[]= strip_tags($tmpFK);
						$fkDDL.= $tmpFK.'
';
					}
					$tmp= Array();
					reset($table->refered);
					$c= 0;
					while($cur= current($table->refered))
					{
						if($cur == $table->name.'|'.$table->name)
							$tmp[]= $c;
						next($table->refered);
						$c++;
					}
					for($i=0; $i<sizeof($tmp); $i++)
					{
						unset($table->refered[$tmp[$i]]);
					}
				}
				
				$this->maskedDDL.= '
	<mindComment>/* Adding Foreign Keys */</mindComment>
'.$fkDDL;
				$this->ddl.= '
	/* Adding Foreign Keys */
'.strip_tags($fkDDL);
				$this->maskedDDL= preg_replace('/<constructor>/', "<span class='ddl_code_constructor'>", $this->maskedDDL);
				$this->maskedDDL= preg_replace('/<\/constructor>/', "</span>", $this->maskedDDL);
				$this->maskedDDL= preg_replace('/<element>/', "<span class='ddl_code_element'>", $this->maskedDDL);
				$this->maskedDDL= preg_replace('/<\/element>/', "</span>", $this->maskedDDL);
				$this->maskedDDL= preg_replace('/<obj>/', "<span class='ddl_code_obj'>", $this->maskedDDL);
				$this->maskedDDL= preg_replace('/<\/obj>/', "</span>", $this->maskedDDL);
				$this->maskedDDL= preg_replace('/<objTable>/', '<span class="ddl_code_objTable">', $this->maskedDDL);
				$this->maskedDDL= preg_replace('/<\/objTable>/', "</span>", $this->maskedDDL);
				$this->maskedDDL= preg_replace('/<mindComment>/', "<span class='ddl_code_mindComment'>", $this->maskedDDL);
				$this->maskedDDL= preg_replace('/<\/mindComment>/', "</span>", $this->maskedDDL);
			}
			$_SESSION['currentDDL']= $this->ddl;
		}
	}
?>
