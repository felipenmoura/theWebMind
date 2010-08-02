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
						<td style='width:200px;'>
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
								/><img src="<?php echo $_MIND['imageDir']; ?>/compiler.gif"
									 onmouseover="showtip(this, event, 'Generate Project');
												  this.src='<?php echo $_MIND['imageDir']; ?>/compiler_over.gif'"
									 onmouseout="this.src='<?php echo $_MIND['imageDir']; ?>/compiler.gif'"
									 onclick="Mind.Project.Generate()"
								/><img src="<?php echo $_MIND['imageDir']; ?>/bt_export.gif"
									 onmouseover="showtip(this, event, 'Export Project');
												  this.src='<?php echo $_MIND['imageDir']; ?>/bt_export_over.gif'"
									 onmouseout="this.src='<?php echo $_MIND['imageDir']; ?>/bt_export.gif'"
									 onclick="Mind.Project.Export();"
								/><img src="<?php echo $_MIND['imageDir']; ?>/bt_full_editor.png"
									 onmouseover="showtip(this, event, 'Maximize/Restaure');
												  this.src=this.src.replace(/.png$/, '_over.png')"
									 onmouseout="this.src=this.src.replace(/_over.png$/, '.png')"									 
									 id="mindEditorFullScreenButton"
									 onclick="Mind.MindEditor.SetFull()"/>
								<br>
							</div>
							<img src='<?php echo $_MIND['imageDir']; ?>/mind_editor_gradient.png' style='padding:0px;margin:0px;*display:none; _display:none;'/><br>
						</td>
						<td style='text-align:left; font-weight:bold;color:#666;'>
							<?php
								echo $_POST['pName'].' <span id="currentVersionLabel">['.$p->version[0].'.'.$p->version[1].'.'.$p->version[2].']</span>';
							?>
							<span style='visibility:hidden;' id='mindEditorNotSavedMark'>
								<img src='<?php echo $_MIND['imageDir']; ?>/unsaved.jpg' />
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
							<td class='mindEditor_lineNumberColumnArea' id="mindEditor_lineNumberColumnArea">
								<b id='mind_editor_lineNumber_1'> </b>
							</td>
							<td class='mindEditor_lineNumberColumn'
								id='mindEditor_lineNumberColumn'>1
</td>
							<td style='vertical-align:top;' id='bgToChangeOnEditor'
								onscroll="alert(this)">
								<div style="position: relative;height:100%;width:100%;">
								   <!--<img src='<?php echo $_MIND['imageDir']; ?>/back_gray.png' style="position:absolute; width: 100%; height: 100%;z-index:0;" />
								   <img src='<?php echo $_MIND['imageDir']; ?>/gray_back_2.png' style="position:absolute; width: 100%; height: 100%;z-index:0;" />-->
								   <textarea id='mindEditor'
										  onkeyup='Mind.MindEditor.Typing(this.value, event);'
										  onblur='Mind.Project.attributes.wml= this.value;'
										  style="background-image:url(<?php echo $_MIND['imageDir']; ?>/gray_back_2.png);
										  		 background-position:top center;
										  		 background-attachment:fixed;"
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
						<!--
						<td>
							<div class='replaceButton'>
								R
							</div>
						</td>-->
						<td>
							<img src='<?php echo $_MIND['imageDir']; ?>/search_for.png'
								 onclick="Mind.MindEditor.ShowSearchPanel(event)"
								 style='cursor:pointer;'
								 title='Find in the code'
								 align='left'/>
						</td>
						<td>
							<img src='<?php echo $_MIND['imageDir']; ?>/help.png'
									 onclick="Mind.Help.Open('Web-Mind-Language')"
									 style='cursor:pointer;'
									 title='Help'/>
						</td>
						<td style='width:100%;text-align:right;'>
							<img src='<?php echo $_MIND['imageDir']; ?>/editor_tool.png'
								 style='cursor:pointer;'
								 class='normalButton'
								 onmouseover='this.className= "buttonOver";'
								 onmouseout='this.className= "normalButton";'
								 onclick="Mind.MindEditor.ShowCodeEditor(event)" />
							<img src='<?php echo $_MIND['imageDir']; ?>/bt_close.png'
								 style='cursor:pointer;'
								 onclick="Mind.MindEditor.HideTools(); Mind.Panel['bottom'].Adjust();" />&nbsp;
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<iframe id='hiddenFrame'
			style='display:none;'></iframe>
</div>
<?php
?>
