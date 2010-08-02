<?php
	require_once('../../config/mind.php');
	require_once('../../'.$_MIND['framework']);
	require_once('../../'.$_MIND['header']);
	
	
	$current= $_MIND['fw']->loadXML($_MIND['rootDir'].'config/current.xml');
	//print_r($current->mindFiles[0]['version']);
	$vs= $current->mindFiles[0]['version'];
//	$vs= implode($vs);
?>
<img src='<?php echo $_MIND['imageDir'].'/logo_light.png'; ?>'><br/>
<div>
	<h2>theWebMind <?php echo $vs; ?></h2><br/>
	WebPage: <a href='http://thewebmind.org' target='_quot'>http://thewebmind.org</a><br/>
	
</div>
