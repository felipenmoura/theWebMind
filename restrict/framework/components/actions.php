<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);

	if(!isset($_POST['action']))
		die('false');
	$data= JSON_decode($_POST['action']);
	
	if(!$data)
		$data= JSON_decode(stripslashes($_POST['action']));
	
	switch($data->action)
	{
		case 'saveDER':
		{
			$d= $_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$data->project.'/mind/er/';
			
			if(!file_exists($d))
				mkdir($d, 0777);
			$f= fopen($d.$data->name.'.json', 'w+');
			fclose($f);
			file_put_contents($d.$data->name.'.json', JSON_encode($data));
			echo 'true';
		}break;
		case 'removeDER':
			if(unlink($_MIND['rootDir'].$_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$data->project.'/mind/er/'.$data->name.'.json'))
				echo 'true';
			else
				echo JSON_encode($_MIND['fw']->errorOutput(6));
		break;
		case '':
		break;
	}
	
	
	/**********************************************
	
	ARQUIVO ATUALIZADO PARA TESTE
	
	**********************************************/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/// hgfhgfhgf hgfhgf hg fhgf
?>