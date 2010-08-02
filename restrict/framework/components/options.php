<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
	
	
	if($_POST)
	{
		$_MIND['fw']->saveOptions($_POST);
		?>
Mind.Dialog.CloseModal();
		<?php
		exit;
	}
	
	$op= $_MIND['fw']->loadOptions();
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>"
	  method='POST'
	  onsubmit="return false"
	  id='optionsForm'>
	<ul>
		<li><a href="#optionsForm_tab1">Laytout</a></li>
		<li><a href="#optionsForm_tab2">Updates</a></li>
		<li><a href="#optionsForm_tab3">I.Q.</a></li>
	</ul>
	<div id="optionsForm_tab1">
		<table>
			<tr>
				<td colspan='2'>
					Default Idiom<br/>
					<select name='defaultIdiom'
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
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					Default DBMS<br/>
					<select name='defaultDBMS'
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
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					Theme<br/>
					<select class='iptCombo'
							label='DBMS'
							onchange="Mind.Theme.SetTheme(this.value);">
						<option value='default'>
							Default
						</option>
						<?php
							$l= $_MIND['fw']->getThemes();
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
				</td>
			</tr>
			<tr>
				<td>
					Reset layout options<br/>
					(position, size and colors)
				</td>
				<td>
					<input type='button'
						   class='ui-state-default ui-corner-all'
						   value="Reset"
						   onclick="if(confirm('Are you wure you want to reset your layout?')){Mind.Theme.Reset();}"/>
				</td>
			</tr>
		</table>
	</div>
	<div id="optionsForm_tab2">
		<table>
			<tr>
				<td>
					Check for updates<br/>
					<select name='lookForUpdate' id='mindOptionsLookForUpdate' onchange="optionsFormChangeUpdate(this)">
						<option value='1'
								<?php
									if($op['lookForUpdate'] == 1)
										echo 'selected';
								?>>
							Weekly
						</option>
						<option value='2'
								<?php
									if($op['lookForUpdate'] == 2)
										echo 'selected';
								?>>
							Monthly
						</option>
						<option value='3'
								<?php
									if($op['lookForUpdate'] == 3)
										echo 'selected';
								?>>
							Daily
						</option>
						<option value='never'
								<?php
									if($op['lookForUpdate'] != 1 && $op['lookForUpdate'] != 2 && $op['lookForUpdate'] != 3)
										echo 'selected';
								?>>
							Never
						</option>
					</select>
				</td>
			</tr>
			<tbody id='optionsForm_tab2TBody'>
				<tr>
					<td>
						New versions<br/>
						<select name='actionWithNewVersion'>
							<option value='1'
								<?php
									if($op['actionWithNewVersion'] == 1)
										echo 'selected';
								?>>
								Tell me before installing
							</option>
							<option value='2'
								<?php
									if($op['actionWithNewVersion'] == 2)
										echo 'selected';
								?>>
								Just install it without asking me anything
							</option>
							<option value='3'
								<?php
									if($op['actionWithNewVersion'] == 3)
										echo 'selected';
								?>>
								Install them all, and tell me after it.
							</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						New sub versions<br/>
						<select name='actionWithNewSubVersion'>
							<option value='1'
								<?php
									if($op['actionWithNewSubVersion'] == 1)
										echo 'selected';
								?>>
								Tell me before installing
							</option>
							<option value='2'
								<?php
									if($op['actionWithNewSubVersion'] == 2)
										echo 'selected';
								?>>
								Just install it without asking me anything
							</option>
							<option value='3'
								<?php
									if($op['actionWithNewSubVersion'] == 3)
										echo 'selected';
								?>>
								Install them all, and tell me after it.
							</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						New updates<br/>
						<select name='actionWithNewUpdates'>
							<option value='1'
								<?php
									if($op['actionWithNewUpdates'] == 1)
										echo 'selected';
								?>>
								Tell me before installing
							</option>
							<option value='2'
								<?php
									if($op['actionWithNewUpdates'] == 2)
										echo 'selected';
								?>>
								Just install it without asking me anything
							</option>
							<option value='3'
								<?php
									if($op['actionWithNewUpdates'] == 3)
										echo 'selected';
								?>>
								Install them all, and tell me after it.
							</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="optionsForm_tab3">
		<input  type='checkbox'
				<?php if(isset($op['useGlobalSynDic']) && $op['useGlobalSynDic'] == 'on') echo "checked='checked'" ?>
				name='useGlobalSynDic'/>
				Use Global Synonimous<br/>
				&nbsp;&nbsp;&nbsp;&nbsp; 
				<input  type='checkbox'
						name='addAutomatically'
						id='addAutomatically1'
						value='global'
						<?php if(isset($op['addAutomatically']) && $op['addAutomatically'] == 'global') echo "checked='checked'" ?> />
				Add new synonymous automatically<br/>
		<input  type='checkbox'
				<?php if(isset($op['useLocalSynDic']) && $op['useLocalSynDic'] == 'on') echo "checked='checked'" ?>
				name='useLocalSynDic'/>
				Use Project Synonimous<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input  type='checkbox'
						name='addAutomatically'
						id='addAutomatically2'
						value='local'
						<?php if(isset($op['addAutomatically']) && $op['addAutomatically'] == 'local') echo "checked='checked'" ?> />
				Add new synonymous automatically<br/>
		<input  type='checkbox'
				<?php if(isset($op['reportDecisions']) && $op['reportDecisions'] == 'on') echo "checked='checked'" ?>
				name='reportDecisions' />
				Report decisions<br/>
		<input  type='checkbox'
				<?php if(isset($op['reportDoubts']) && $op['reportDoubts'] == 'on') echo "checked='checked'" ?>
				name='reportDoubts' />
				Report doubts<br/>
		<!-- <input  type='checkbox'
				<?php if(isset($op['enableMindUniverse']) && $op['enableMindUniverse'] == 'on') echo "checked='checked'" ?>
				name='enableMindUniverse' />
				Enable Mind Universe<br/> -->
	</div>
</form>
<script>
	function optionsFormChangeUpdate(o)
	{
		if(o.value == 'never')
		{
			$('#optionsForm_tab2TBody select').attr('disabled', 'disabled');
		}else{
				$('#optionsForm_tab2TBody select').attr('disabled', false);
			 }
	}
	
	$(function() {
		$("#optionsForm").tabs();
		optionsFormChangeUpdate(document.getElementById('mindOptionsLookForUpdate'));
		
		var iq= $('#optionsForm_tab3');
		var gSynDic= iq.find("[name=useGlobalSynDic]");
		var lSynDic= iq.find('[name=useLocalSynDic]');
		
		if(!gSynDic.is(':checked'))
		{
			document.getElementById('addAutomatically1').disabled= 'disabled';
			document.getElementById('addAutomatically1').checked= false;
		}
		if(!lSynDic.is(':checked'))
		{
			document.getElementById('addAutomatically2').disabled= 'disabled';
			document.getElementById('addAutomatically2').checked= false;
		}

		$('#addAutomatically2').bind('click', function(){
			if(this.checked)
				document.getElementById('addAutomatically1').checked= false;
		});

		$('#addAutomatically1').bind('click', function(){
			if(this.checked)
				document.getElementById('addAutomatically2').checked= false;
		});
		gSynDic.bind('click', function(){
			if(this.checked)
			{
				document.getElementById('addAutomatically1').removeAttribute('disabled');
			}else{
					document.getElementById('addAutomatically1').setAttribute('disabled', 'disabled');
				 }
		})

		lSynDic.bind('click', function(){
			if(this.checked)
			{
				document.getElementById('addAutomatically2').removeAttribute('disabled');
			}else{
					document.getElementById('addAutomatically2').setAttribute('disabled', 'disabled');
				 }
		})
	});
</script>
