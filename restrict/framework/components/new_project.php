<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	if($_POST)
	{
		/*$e= $_MIND['fw']->encrypt("");
		echo $e.'<br>';
		echo $_MIND['fw']->decrypt($e);
		exit;*/
		
		$_POST['name']= $_MIND['fw']->fixName(strtolower($_POST['name']));
		
		if(Project::projectExists($_POST['name']))
		{
			echo "Mind.Dialog.ShowError(".JSON_encode($_MIND['fw']->errorOutput(4)).")";
			exit;
		}
		$p= new Project();
		$_POST = $p->preparePost($_POST);
		$p->populate($_POST);
			
		if($p->save())
		{
			//echo "Mind.Dialog.ShowMessage('Project Added');";
			echo "
				  Mind.Component.Load('projectList',
										function (comps){
											comps= Mind.Component.Parse(comps);
											Mind.Panel['left'].Update(comps['projectList']);
										}
									 );
				  Mind.Dialog.CloseModal();
				  Mind.Project.Load('".$p->name."');
				  ";
			//echo "Mind.Panel['left'].Update(); ";
		}
		//print_r($p);
		exit;
	}
	$op= $_MIND['fw']->loadOptions();
?>
<form action='<?php echo $_SERVER['PHP_SELF']; ?>' onsubmit="return false;" id='newProjectForm' method='POST' target='_quot'>
	<div class='config'
		 style='width:100%;
				height:100%;'>
		<div class='errorMessageLabel'
			 id='options_errorMessageLabel'
			 style='display:none;'>
			<br/>
		</div>
		<br/>
		<table style='margin-bottom:4px; float:left;'>
			<tr>
				<td>
					Project Name
				</td>
				<td>
					<input type='text'
						   name="name"
						   class='iptText'
						   required='true'
						   maxlength='30'
						   label='Name'
						   id='new_project_name'>
				</td>
			</tr>
			<tr>
				<td>
					Mind-Language
				</td>
				<td>
					<div class="selCont">
					<select name='lang'
						    class='iptCombo'
							id="language_selectbox"
							required='true'
							label='Language'>
						<?php
							$l= $_MIND['fw']->getLanguages();
							for($i=0; $i<sizeof($l); $i++)
							{
								?>
								<option value='<?php echo $l[$i]; ?>'
										<?php
											if($op['defaultIdiom'] == $l[$i])
												echo ' selected ';
										?>>
									<?php echo $l[$i]; ?>
								</option>
								<?php
							}
						?>
					</select>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					DBMS
				</td>
				<td>
					<div class="selCont">
					<select name='dbms'
						    class='iptCombo'
							id="select_dbms"
							required='true'
							label='DBMS'>
						<?php
							$l= $_MIND['fw']->getDBMSs();
							for($i=0; $i<sizeof($l); $i++)
							{
								?>
								<option value='<?php echo $l[$i]; ?>'
										<?php
											if($op['defaultDBMS'] == $l[$i])
												echo ' selected ';
										?>>
									<?php echo $l[$i]; ?>
								</option>
								<?php
							}
						?>
					</select>
					</div>
				</td>
			</tr>			
		</table>
		<table>
			<tr>
				<td style='vertical-align:top;'>
						Users
				</td>
				<td>
					<select name='users[]'
							class='iptComboMultiple'
							required='true'
							label='DBMS'
							multiple='multiple'>
						<?php
								$l= User::getUsers();
								for($i=0; $i<sizeof($l); $i++)
								{
									?>
									<option value='<?php echo $l[$i]->login(); ?>'>
											<?php echo $l[$i]->name(); ?>
									</option>
									<?php
								}
						?>
					</select>
				</td>
			</tr>
		</table>
		<div class="tabs" style='width:500; margin-top:8px;'>
			<ul>
				<li><a href="#newProjectForm_tab1">Development DB</a></li>
				<li><a href="#newProjectForm_tab2">Production DB</a></li>
			</ul>
			<div id="newProjectForm_tab1">
				<table>
					<tr>
						<td>
							Address
						</td>
						<td>
							<input type='text'
								   name="dbAddress1"
								   class='iptText'
								   label="Data Base's Name"
								   value='localhost'>
						</td>
						<td>
							Port
						</td>
						<td>
							<input type='text'
								   name="port1"
								   class='iptText'
								   maxlength='4'
								   style='width:50px;'
								   label="Data Base's Port">
						</td>
					</tr>
					<tr>
						<td>
							DB Name
						</td>
						<td>
							<input type='text'
								   name="dbName1"
								   class='iptText'
								   label='Data Base Name'>
						</td>
						<td>
							
						</td>
						<td><br>
						</td>
					</tr>
					<tr>
						<td>
							Root User
						</td>
						<td>
							<input type='text'
								   name="userRoot1"
								   class='iptText'
								   value='root'>
						</td>
						<td>
							Password
						</td>
						<td>
							<input type='password'
								   name="userRootPwd1"
								   class='iptPwd'>
						</td>
					</tr>
					<tr>
						<td>
							User
						</td>
						<td>
							<input type='text'
								   name="user1"
								   class='iptText'>
						</td>
						<td>
							Password
						</td>
						<td>
							<input type='password'
								   name="userPwd1"
								   class='iptPwd'>
						</td>
					</tr>
				</table>
			</div>
			<div id="newProjectForm_tab2">
				<table>
					<tr>
						<td>
							Address
						</td>
						<td>
							<input type='text'
								   name="dbAddress2"
								   class='iptText'
								   label="Data Base's Name">
						</td>
						<td>
							Port
						</td>
						<td>
							<input type='text'
								   name="port2"
								   class='iptText'
								   maxlength='4'
								   style='width:50px;'
								   label="Data Base's Port">
						</td>
					</tr>
					<tr>
						<td>
							DB Name
						</td>
						<td>
							<input type='text'
								   name="dbName2"
								   class='iptText'
								   label='Data Base Name'>
						</td>
						<td>
							
						</td>
						<td><br>
						</td>
					</tr>
					<tr>
						<td>
							Root User
						</td>
						<td>
							<input type='text'
								   name="userRoot2"
								   class='iptText'>
						</td>
						<td>
							Password
						</td>
						<td>
							<input type='password'
								   name="userRootPwd2"
								   class='iptPwd'
								   style='width:100px;'>
						</td>
					</tr>
					<tr>
						<td>
							User
						</td>
						<td>
							<input type='text'
								   name="user2"
								   class='iptText'>
						</td>
						<td>
							Password
						</td>
						<td>
							<input type='password'
								   name="userPwd2"
								   class='iptPwd'>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<br>
		Project's Description<br>
		<textarea name='description'
				  class='iptTextArea'
				  style='width:505px;background-color:transparent;'></textarea>
	</div>
</form>
<script>
	$(function() {
		$("#newProjectForm .tabs").tabs();
		//setTimeout(function(){document.getElementById('new_project_name').focus();}, 1000);		
	});
</script>
