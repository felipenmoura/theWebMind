<?php
	class Language
	{
		public $verbs= Array();
		public $types= Array();
		public $required= Array();
		public $quantifiersId= Array();
		public $quantifiers= Array();
		public $especialChars= Array();
		public $fixedChars= Array();
		public $belongs= Array();
		public $obligation= Array();
		public $unique= Array();
		
		public function __construct($lang)
		{
			global $_MIND;
			$langFile= $_MIND['rootDir'].$_MIND['languageDir'].'/'.$lang.'/'.$lang.'.xml';
			//echo $langFile;
			if(file_exists($langFile))
			{
				$l= simplexml_load_file($langFile);
				
				$tmp= '';
				foreach($l->verbs->verb as $tmp)
				{
					$this->verbs[]= (string)$tmp['value'];
				}
				foreach($l->belongs->verb as $tmp)
				{
					$this->belongs[]= (string)$tmp['value'];
				}
				foreach($l->obligation->verb as $tmp)
				{
					$this->obligation[]= (string)$tmp['value'];
				}
				foreach($l->types->type as $tmp)
				{
					$this->types[(string)$tmp['value']]= Array();
					foreach($tmp->substantive as $tmpSub)
					{
						$this->types[(string)$tmp['value']][]= (string)$tmpSub['value'];
					}
				}
				//echo '<pre>'; print_r($this->types);
				foreach($l->required->adjective as $tmp)
				{
					$this->required[]= (string)$tmp['value'];
				}
				foreach($l->especialchars->char as $tmp)
				{
					$this->especialChars[]= (string)$tmp['value'];
				}
				foreach($l->fixedchars->char as $tmp)
				{
					$this->fixedChars[]= (string)$tmp['value'];
				}
				foreach($l->quantifiersId->world as $tmp)
				{
					$this->quantifiersId[]= (string)$tmp['value'];
				}
				foreach($l->unique->adjective as $tmp)
				{
					$this->unique[]= (string)$tmp['value'];
				}
				
				$this->quantifiers= Array('max'=>Array('min'=>Array(), 'max'=>Array()));
				
				foreach($l->quantifiers->min->min->quantifier as $tmp)
				{
					$this->quantifiers['min']['min'][]= (string)$tmp['value'];
				}
				foreach($l->quantifiers->min->max->quantifier as $tmp)
				{
					$this->quantifiers['min']['max'][]= (string)$tmp['value'];
				}
				foreach($l->quantifiers->max->min->quantifier as $tmp)
				{
					$this->quantifiers['max']['min'][]= (string)$tmp['value'];
				}
				foreach($l->quantifiers->max->max->quantifier as $tmp)
				{
					$this->quantifiers['max']['max'][]= (string)$tmp['value'];
				}
				
				if(!isset($_SESSION['current']))
					$_SESSION['current']= Array();
				$_SESSION['current']['defaultVerb']= $this->verbs[0];
				$_SESSION['current']['defaultObligation']= $this->obligation[0];
				$_SESSION['current']['defaultBelongs']= $this->belongs[0];
				$_SESSION['current']['defaultNotNull']= $this->required[0];
				$_SESSION['current']['defaultUnique']= $this->unique[0];
				
			}else{
					trigger_error("Failed loading the language");
					exit;
				}
			return $this;
		}
	}
?>