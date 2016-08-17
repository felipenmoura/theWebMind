<?php
	include('../../config/mind.php');
	//include('../../'.$_MIND['header']);
	
	if(!$_POST['pName'])
	{
		echo "Not allowed";
		exit;
	}
	$p= new Project($_POST['pName'], $_SESSION['user']['login']);
?>
<div style='height:100%;width:100%;overflow:hidden;'>
	<table cellpadding='0'
		   cellspacing='0'
		   class='mindLangEditorContainer'>
		<tr>
			<td class='header'>
				<table cellspacing='0'
					   cellpadding='0'
					   style='width:100%;'>
					<tr>
						<td>
							<div class='MindEditorButtons'>
								<img src="<?php echo $_MIND['imageDir']; ?>/bt_save.gif"
									 onmouseover="showtip(this, event, 'Save current changes');
												  this.src='<?php echo $_MIND['imageDir']; ?>/bt_save_over.gif'"
									 onmouseout="this.src='<?php echo $_MIND['imageDir']; ?>/bt_save.gif'"
									 onclick="Mind.Project.Save();"
								/><img src="<?php echo $_MIND['imageDir']; ?>/bt_prop.gif"
									 onmouseover="showtip(this, event, 'Properties');
												  this.src='<?php echo $_MIND['imageDir']; ?>/bt_prop_over.gif'"
									 onmouseout="this.src='<?php echo $_MIND['imageDir']; ?>/bt_prop.gif'"
									 onclick="Mind.Project.Properties('<?php echo $p->name; ?>');"
								/><img src="<?php echo $_MIND['imageDir']; ?>/bt_play.gif"
									 onmouseover="showtip(this, event, 'Run/Simulate Project');
												  this.src='<?php echo $_MIND['imageDir']; ?>/bt_play_over.gif'"
									 onmouseout="this.src='<?php echo $_MIND['imageDir']; ?>/bt_play.gif'"
									 onclick="Mind.Project.Run('<?php echo $p->name; ?>');"
								/><img src="<?php echo $_MIND['imageDir']; ?>/bt_export.gif"
									 onmouseover="showtip(this, event, 'Export Project');
												  this.src='<?php echo $_MIND['imageDir']; ?>/bt_export_over.gif'"
									 onmouseout="this.src='<?php echo $_MIND['imageDir']; ?>/bt_export.gif'"
									 onclick="Mind.Project.Export();"
								/><img src="<?php echo $_MIND['imageDir']; ?>/bt_full_editor.png"
									 onmouseover="showtip(this, event, 'Maximize/Restaure');
												  this.src=this.src.replace(/.png$/, '_over.png')"
									 onmouseout="this.src=this.src.replace(/_over.png$/, '.png')"
									 opened='1'
									 id="mindEditorFullScreenButton"
									 onclick="if(this.getAttribute('opened')=='1')
											  {
												Mind.Panels.Close();
												this.setAttribute('opened', '0');
												this.src='<?php echo $_MIND['imageDir']; ?>/bt_full_close.png';
											  }else {
														Mind.Panels.Open();
														this.src='<?php echo $_MIND['imageDir']; ?>/bt_full_editor.png';
														this.setAttribute('opened', '1');
													}"/>
								<br>
							</div>
							<img src='<?php echo $_MIND['imageDir']; ?>/mind_editor_gradient.png' style='padding:0px;margin:0px;*display:none; _display:none;'/><br>
						</td>
						<td>
							<?php
								echo $_POST['pName'].' vr'.$p->version[0].'.'.$p->version[1].'.'.$p->version[2];
							?>
							<span style='visibility:hidden;' id='mindEditorNotSavedMark'>
								(*)
							</span>
						</td>
						<td style='text-align: right; width: 50px;padding-right:8px;'>
							<img src='<?php echo $_MIND['imageDir']; ?>/red_close_button.png'
								 onmouseover="this.src='<?php echo $_MIND['imageDir']; ?>/red_close_button_over.png';"
								 onmouseout="this.src='<?php echo $_MIND['imageDir']; ?>/red_close_button.png';"
								 onclick="Mind.Project.Close();"/>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class='body'>
				<div id='mindEditor_Container'
					 style='width:100%; height:100%; overflow:auto;'>
					<table cellpadding='0'
						   cellspacing='0'
						   style='width:100%;
								  height:100%;'>
						<tr>
							<td class='mindEditor_lineNumberColumnArea'>
								<br>
							</td>
							<td class='mindEditor_lineNumberColumn'
								id='mindEditor_lineNumberColumn'>
								1
							</td>
							<td style='vertical-align:top;' id='bgToChangeOnEditor'
								onscroll="alert(this)">
								<div style="position: relative;height:100%;width:100%;">
								   <img src='<?php echo $_MIND['imageDir']; ?>/back_gray.png' style="position:absolute; width: 100%; height: 100%;z-index:0;" />
								   <textarea id='mindEditor'
										  onkeyup='Mind.MindEditor.Typing(this.value, event);'
										  onblur='Mind.Project.attributes.wml= this.value;'
										  wrap="off"><?php echo $p->wml; ?></textarea>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr id='MindEditorFooterTools'>
			<td class='footer'>
				<table class='tools' style='width:100%;'>
					<tr>
						<td>
							<img src='<?php echo $_MIND['imageDir']; ?>/bt_print.png'
								 onclick="Mind.MindEditor.Print();"
								 style='cursor:pointer;'>
						</td>
						<td>
							<!--<select>
								<option value='Verdana' selected>
									Verdana
								</option>
								<option value='Tahoma'>
									Tahoma
								</option>
								<option value='Arial'>
									Arial
								</option>
								<option value='Helvetica'>
									Helvetica
								</option>
								<option value='Times'>
									Times
								</option>
								<option value='Arial Black'>
									Arial Black
								</option>
								<option value='Comic Sans MS'>
									Comic Sans MS
								</option>
							</select>-->
							<div id="win-xp" class="selCont">
								<select onchange="Mind.MindEditor.SetSize(this.value);" id="font_size_select">
									<option value='8'>8px</option>
									<option value='10'>10px</option>
									<option value='12' selected>12px</option>
									<option value='14'>14px</option>
									<option value='18'>18px</option>
									<option value='24'>24px</option>
									<option value='36'>36px</option>
								</select>
							</div>
						</td>
						<td>
							<div class='bt' onclick="Mind.MindEditor.SetBold(this);">
								B
							</div>
						</td>
						<td>
							<div class='bt'
								 onclick="Mind.MindEditor.SetItalic(this);">
								I
							</div>
						</td>
						<td>
							Font
						</td>
						<td>
							<div class="fontColor"><div onclick="$('#colorpickerHolder').ColorPickerSetColor(this.style.backgroundColor||'666666'); Mind.MindEditor.ShowColorPicker(event, this);" changes='color'></div></div>
						</td>
						<td>
							Background
						</td>
						<td>
							<div class="bgColor"><div onclick="$('#colorpickerHolder').ColorPickerSetColor(this.style.backgroundColor||'ffffff'); Mind.MindEditor.ShowColorPicker(event, this);"
													  changes='backgroundColor'></div></div>
						</td>
						<td style='width:100%;text-align:right;'>
							<img src='<?php echo $_MIND['imageDir']; ?>/bt_close.png'
								 style='cursor:pointer;'
								 onclick="Mind.MindEditor.HideTools(); Mind.Panel['bottom'].Adjust();">&nbsp;
						</td>
					</tr>
				</table>
				<!--<div class='mindEditorTool'>
					Entity 
					<input type='text'
						   id='GUIEntity1'
						   name='GUIEntity1'>
					<select id='GUILikType'
							onchange="Mind.MindEditor.ChangeType(this);"
							name='GUILikType'>
						<option value='0'
								selected>
							Entity
						</option>
						<option value='1'>
							Attribute
						</option>
					</select>
					<span id='GUIEntityParams'
						  name='GUIEntityParams'>
						<select id='GUIEntityLinkType'>
							<option value='n'>
								1/n
							</option>
							<option value='nn'>
								n/n
							</option>
							<option value='1'>
								n/1
							</option>
							<option value='11'>
								1/1
							</option>
						</select>
						<input type='text'
							   id='GUIEntity2'>
					</span>
					<span id='GUIAttributeParams'
						  style='display: none;'>
						<input type='text'
							   id='GUIAttributeName'
							   name='GUIAttributeName'> 
						Type
						<select id="GUIAttributeType"
								name='GUIAttributeType'>
							<option value='varchar'>
								varchar
							</option>
							<option value='char'>
								char
							</option>
							<option value='text'>
								text
							</option>
							<option value='date'>
								date
							</option>
							<option value='time'>
								time
							</option>
							<option value='dateTime'>
								dateTime
							</option>
							<option value='blob'>
								blob
							</option>
							<option value='smallint'>
								smallint
							</option>
							<option value='integer'>
								integer
							</option>
							<option value='bigint'>
								bigint
							</option>
							<option value='float'>
								float
							</option>
							<option value='bool'>
								bool
							</option>
						</select>
						<br>
						Size 
						<input type='text'
							   id='GUIAttributeSize'
							   name='GUIAttributeSize'
							   style='width:40px;'> 
						Default 
						<input type='text'
							   id='GUIAttributeDefault'
							   name='GUIAttributeDefault'> 
						Not Null 
						<input type='checkbox'
							   id='GUIAttributeNotNull'
							   name='GUIAttributeNotNull'
							   style='width:40px;'> 
						Unique 
						<input type='checkbox'
							   id='GUIAttributeUnique'
							   name='GUIAttributeUnique'
							   style='width:40px;'> 
						Comment 
						<input type='text'
							   id='GUIAttributeComment'
							   name='GUIAttributeComment'>
					</span>
					<input type='button'
						   onclick="Mind.MindEditor.AddCommandLine();"
						   value="Ok">
				</div>
				-->
			</td>
		</tr>
	</table>
	<iframe id='hiddenFrame'
			style='display:none;'></iframe>
</div>
<?php
?>