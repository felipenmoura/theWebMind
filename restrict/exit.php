<?php
	include('config/mind.php');
	include($_MIND['framework']);
	include($_MIND['header']);
	
	session_destroy();
	header('Location: ../index.php');
?>