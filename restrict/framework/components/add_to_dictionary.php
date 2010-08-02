<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	$file= $_MIND['rootDir'].$_MIND['languageDir'].'/'.$_SESSION['current']['lang'].'/'.$_SESSION['current']['lang'].'.xml';
	if(!$xmlLang= simplexml_load_file($file))
	{
		trigger_error(E_USER_ERROR, "Failed trying to access the dictionary!");
		exit;
	}
	if(isset($_POST['newVerb']) && isset($_POST['verbType']))
	{
		$parent= null;
		switch($_POST['verbType'])
		{
			case 'm':
			{
				$parent= $xmlLang->obligation;
				break;
			}
			case 'px':
			{
				$parent= $xmlLang->belongs;
				break;
			}
			default:
			{
				$parent= $xmlLang->verbs;
			}
		}
		$curVerb= $parent->addChild('verb');
		$curVerb['value']= strip_tags(addslashes($_POST['newVerb']));
		file_put_contents($file, $xmlLang->asXML());
		exit;
	}
	if(isset($_POST['newType']) && isset($_POST['type']))
	{
		//$child= $xmlLang->types->$_POST['type']->addChild(strip_tags(addslashes($_POST['newType'])));
		$child= $xmlLang->XPath("types/type[@value='".$_POST['type']."']");
		if(isset($child[0]))
		{
			$substantive= $child[0]->addChild('substantive');
			$substantive['value']= strip_tags(addslashes($_POST['newType']));
			file_put_contents($file, $xmlLang->asXML());
		}
		exit;
	}
?>