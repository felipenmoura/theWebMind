<head>
	<?php
		include('config/mind.php');
	?>
		<link rel="stylesheet" type="text/css" href="<?php echo $_MIND['styleSrc']; ?>/mind.ddl.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?php echo $_MIND['styleSrc']; ?>/mind.datadictionary.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?php echo $_MIND['styleSrc']; ?>/mind.erd.css" media="screen" />
</head>
<body><br/></body>
<?PHP
	if(!$_GET)
	{
	?>
		<script>
			opener.Mind.MindEditor.PrintOnWindow(document.body);
			self.print();
			self.close();
		</script>
	<?php
	}else{
		?>
			<script>
				var content= opener.document.getElementById('<?php echo $_GET['id'] ?>').innerHTML;
				document.body.innerHTML= content;
				document.body.innerHTML+= "<center><input type='button' value='Print' onclick='self.print();'></center>";
			</script>
		<?php
	}
?>
