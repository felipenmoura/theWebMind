<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	if($_POST)
	{
		if(!isset($_POST['noDataBase']) || $_POST['noDataBase'] == 'false')
		{
			?>
				<fieldset>
					<legend>
						DataBase Behavior
					</legend>
					Tables which already exist should : <br/>
					<input type='radio' name='existingTables' value='1' checked='checked'> be skipped<br/>
					<input type='radio' name='existingTables' value='2'> be replaced (all data will be lost)
				</fieldset>
			<?php
		}
		if(isset($_POST['module']))
		{
			$l= $_MIND['fw']->getModule($_POST['module']);
			$f= strtolower($_MIND['rootDir'].$_MIND['moduleDir'].'/'.$l->name.'/data/'.$l->configPage);
			if(file_exists($f))
			{
				$c= @file_get_contents($f);
				echo $c;
			}else{
					?>
						<br/>
						The selected module has no advanced options.<br/><br/>
						<i>theWebMind 2.0</i> is about to generate a version of your project into YOUR folder.<br/>
						Press <b>Finish</b> to close this wizard and run this feature.
					<?php
				 }
		}else{
				?>
					<br/>
					Only the database will be changed.<br/>
					<i>theWebMind 2.0</i> is about to generate the Data Base.<br/>
					Press <b>Finish</b> to close this wizard and run this feature.
				<?php
			 }
	}
?>