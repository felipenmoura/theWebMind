<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	if($_FILES)
	{
		if(
			($_FILES['fileToUpLoad']['type'] == 'package/mnd'
			||
			$_FILES['fileToUpLoad']['type'] == 'application/octet-stream')
			&&
			$_FILES['fileToUpLoad']['error'] == 0
		  )
		{
			$content= file_get_contents($_FILES['fileToUpLoad']['tmp_name']);
			$_FILES['fileToUpLoad']['name']= preg_replace('/\.mnd$/', '', $_FILES['fileToUpLoad']['name']);
			if(!Project::projectExists($_FILES['fileToUpLoad']['name']))
			{
				$content= $content;
				$content= explode("?>
", $content);
				$wmlVersion= $content[0].'?>';
				
				//echo htmlentities($content[1]);
				
				$content= (isset($content[1]))? $content[1]: $content[0];
				
				
				$ar= Array();
				$ar= JSON_decode($content, true);
				//$ar['users']= Array($_SESSION['user']['login']);
				
				//print_r($ar);
				
				
				$p= new Project();
				$p->populate($ar['projectData']);
				$p->users= Array();
				$p->users[]= $_SESSION['user']['login'];
				$p->wml= $ar['projectData']['originalWML'];
				if($p->save())
				{
					$p->save();
					echo "<script>
							  top.Mind.Component.Load('projectList',
													function (comps){
														comps= top.Mind.Component.Parse(comps);
														top.Mind.Panel['left'].Update(comps['projectList']);
													}
												 );
							  top.Mind.Project.Load('".$p->name."');
							  top.setTimeout(function(){(top.Mind || Mind).Dialog.CloseModal()}, 500);
							  //top.Mind.Dialog.CloseModal();
						  </script>";
				}
				//print_r($ar['ERDs']);
			}
		}
		exit;
	}
?>
<form method='POST'
	  action='<?php echo $_SERVER['PHP_SELF']; ?>'
	  target='hiddenImportFrame'
	  enctype="multipart/form-data">
	<fieldset>
		<table align='center'>
			<tr>
				<td>
					<input type='file' name='fileToUpLoad'/>
				</td>
				<td>
					<input type='submit'
						   class='ui-state-default ui-corner-all'
						   value='Import'>
				</td>
			</tr>
		</table>
	</fieldset>
</form>
<iframe name='hiddenImportFrame' style='display:none;'>
</iframe>