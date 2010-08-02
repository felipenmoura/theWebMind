<?php
	$n= (integer)$_POST['lns'];
	$n+= 10;
	for($i=1; $i<=$n; $i++)
	{
		echo $i.'
';
	}
?>