<html>
	<head>
		<title>theWebMind(s) - 2.0</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="shortcut icon" href="favico.png"/>
		
		<!-- Scripts -->		
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.ui.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/jquery.js"></script><!-- jQuery -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/jquery.ui.all.js"></script><!-- jQuery -->
		
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/jquery.metadata.js"></script><!-- jQuery -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/json.js"></script><!-- jSon -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/accelerators.js"></script><!-- Menus -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mbMenu.js"></script><!-- Menus -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/jquery.hoverIntent.js"></script><!-- jQuery -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/grow/jquery.blockui.js"></script><!-- jQuery -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/jquery.ui.wizard.js"></script><!-- jQuery -->
		
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/tooltip.js"></script><!-- Mind ToolTips -->		
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/coockies.js"></script><!-- Mind Cookies -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.default.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.plugins.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.project.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.mindeditor.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.data_dictionary.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.tooltip.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.update.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.der.js"></script><!-- Mind -->
		
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/colorpicker.js"></script><!-- ColorPicker -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/eye.js"></script><!-- ColorPicker -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/layout.js"></script><!-- ColorPicker -->
		
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/base64.js"></script><!-- canvas -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/canvas2image.js"></script><!-- canvas -->
		
		<!-- Styles -->
		
		<link rel="stylesheet" href="{?$_MIND['styleSrc']}/colorpicker.css" type="text/css" />
		<link rel="stylesheet" type="text/css" href="{?$_MIND['styleSrc']}/menu.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="{?$_MIND['styleSrc']}/layout.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="{?$_MIND['styleSrc']}/default.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="{?$_MIND['styleSrc']}/theme/ui.all.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="{?$_MIND['styleSrc']}/mind.lang_editor.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="{?$_MIND['styleSrc']}/mind.ddl.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="{?$_MIND['styleSrc']}/mind.datadictionary.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="{?$_MIND['styleSrc']}/mind.erd.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="{?$_MIND['styleSrc']}/mind.tree.css" media="screen" />
											
		
		
		<!-- Execution -->
		<script type="text/javascript">
			$(document).ready(function(){
				Mind.Recent.Init();
				Mind.Utils.SetDocumentDefaults();
				Mind.Progress.Init();
				Mind.Progress.Increment();
				Mind.Properties.path = "{?$_MIND['fwComponents']}";
				Mind.Properties.pluginPath = "{?$_MIND['pluginDir']}";
				Mind.Properties.scriptsPath = "{?$_MIND['scriptSrc']}";
				Mind.Properties.stylesPath = "{?$_MIND['styleSrc']}";
				Mind.Properties.showDeveloperMenu = '{?$_MIND['showDeveloperMenu']}';
				Mind.Properties.imagesPath = "{?$_MIND['imageDir']}";
				Mind.Panels.Init();
				$.getScript(Mind.Properties.scriptsPath + "/mind.components.js");
				$.getScript(Mind.Properties.scriptsPath + "/mind.menu.js");				
				Mind.ToolTip.Init();
				Mind.Menus.Init();
				$("#mindEditorTool .tabs").tabs();
				Mind.Theme.Load();
				setTimeout(function(){
					Mind.Plugins.Load();
				}, 2000);
				Mind.ImagePreLoader.Call();
				
				Mind.Theme.LoadCurrentTheme();
				
			});		
		</script>
	</head>
	<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0' scroll="no" style='margin:0px;padding:0px;'>
		<div id='colorPickerWrapper' class="wrapper"><p id="colorpickerHolder"></p></div>
		<div id='blocker'
			 style='width: 100%;
					height: 100%;
					position: absolute;
					left: 0px;
					top: 0px;
					z-index: 999999;
					background-color: #fff;'>
			<table align='center'
				   style='width: 100%;
						  height: 100%;'
				   cellpadding='0'
				   cellspacing='0'>
				<tr>
					<td>
						<table align='center'>
							<tr>
								<td style=" background-image: url({?$_MIND['imageDir']}/loadingGrid.gif);
											text-align: center;
											width: 408px;
											height: 306px;">
									<span id='blockerMessage'>
											<span id="welcome_mind" style='font-weight:bold;font-size: 30px; color: #666;'>Loading Mind IDE...</span><br><br>
											<div id="mind_progressbar"></div>										
									</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>	
		<table style='width: 100%;
					  height: 100%;'
			   cellpadding='0'
			   cellspacing='0'>
			<tr>
				<td style='height: 30px;'>
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#EDEDED" class='menuTopBar'>
						<tr>
							<td valign="bottom" style='border-bottom: solid 1px #dedede;'>
								<table  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="container">
									<tr>
										<td class="myMenu" align="right">
											<!-- start horizontal menu -->
											<table class="rootVoices" cellspacing='0' cellpadding='0' border='0'>
												<tr>
													<td accelerator="F" class="rootVoice {menu: 'menu_file'}" onclick="Mind.Recent.AddToMenu();">File</td>
													<td accelerator="V" class="rootVoice {menu: 'menu_display'}">View</td>
													<td accelerator="T" class="rootVoice {menu: 'menu_tools'}">Tools</td>
													<td accelerator="M" class="rootVoice {menu: 'menu_manager'}">Manage</td>
													<td accelerator="P" class="rootVoice {menu: 'menu_plugins'}">Plugins</td>
													<td class="rootVoice {menu: 'menu_developer'}" style='display:none;' id='developerMenu'>Developer</td>
													<td accelerator="H" class="rootVoice {menu: 'menu_help'}" >Help</td>
													<td id='pluginsIcons'>
														<!-- -->
													</td>
												</tr>
											</table>											
											<!-- end horizontal menu -->
											<div style='display:none;'>
												{?$_MIND_MENUS}
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<div id="mind_layout_left">
						<div>							
						</div>
					</div>
					<div class="vertical-resizable-bar-left" id="vertical_resizable_bar_left">
						<table cellpadding='0' cellspacing='0' style='height:100%;'>
							<tr>
								<td>
									<div class='barBt'>&nbsp;</div>
								</td>
							</tr>
						</table>
					</div>
					<div id="mind_layout_center" style='width:10px; height:10px; overflow:auto;overflow-x:hidden'>
						<div>							
						</div>
					</div>
					<div class="vertical-resizable-bar-right" id="vertical_resizable_bar_right"
						 style='display:none;'>
						<table cellpadding='0' cellspacing='0' style='height:100%;'>
							<tr>
								<td>
									<div class='barBt'>&nbsp;</div>
								</td>
							</tr>
						</table>
					</div>
					<div id="mind_layout_right"
						 style='display:none;'><!-- ALTERAR AQUI para exibir o painel da direita panel['right'] right panel-->
						<div>
						</div>
					</div>
					<div class="horizontal-resizable-bar" id="horizontal_resizable_bar">
						<center><div class='barBt'>&nbsp;</div></center>
					</div>
					<div id="mind_layout_bottom">					
						<div></div>
					</div>
				</td>
			</tr>
		</table>

		<div style='display:none;'>
			<table id='debuggerEstructure' style='width:100%;'>
				<tr>
					<td style='width:20px;'>
							<!---->
					</td>
					<td>
						<div style='width:100%;'
							 id='debuggerEstructureTree'>
							<br>
						</div>
						<div style='width:100%;display:non'
							 id='debuggerEstructureCodes'>
							<center>
								<textarea style='width:49%;height:100%;'></textarea>
								<textarea style='width:49%;height:100%;'></textarea>
							</center>
						</div>
					</td>
				</tr>
			</table>
		</div>		
		<!--Mind Alert-->
		<div id="mind_alert" class="mind-alert"></div>
		<!--Mind Dialog-->
		<div id="mind_dialog" class="mind_dialog">
			<div id="mind_dialog_message" class="mind-message"></div>
			<div id="mind_dialog_content"></div>
			<iframe class="iframe" id="mind_dialog_iframe" src=""></iframe>
		</div>
		<!--Mind Editor Tool-->
		<div id='mindEditorTool'
			 style='left:500px;top:400px;display:none;height:260px;'>
			<form style='margin:0px; padding:0px;' id='mindEditorToolForm' onsubmit='return false'>
				<div class="tabs" style='height:100%;'>
					<ul>
						<li><a href="#mindEditorTool_subtypes">Subtypes</a></li>
						<li><a href="#mindEditorTool_Entities">Entities</a></li>
						<li><a href="#mindEditorTool_Attributes">Attributes</a></li>
					</ul>
					<div id="mindEditorTool_subtypes" style='width:262px;' guia='true'>
						<table style='width:100%; height:220px;'>
							<tr>
								<td>
									<div style='height:190px;overflow-y:auto;overflow-x:hidden;'>
										<table style='width:100%;'>
											<tr style='height:23px;'>
												<td>
													Subtype
												</td>
												<td>
													<input type='text' maxlength='40' class='iptText' id='mindEditorTool_subtype_name'/>
												</td>
											</tr>
											<tr style='height:23px;'>
												<td>
													Type
												</td>
												<td>
													<select id='mindEditorTool_subtype_type'>
														<option value='char'>
															char
														</option>
														<option value='varchar'>
															varchar
														</option>
														<option value='text'>
															text
														</option>
														<option value='password'>
															password
														</option>
														<option value='file'>
															file
														</option>
														<option value='smallint'>
															smallint
														</option>
														<option value='int'>
															int
														</option>
														<option value='bigint'>
															bigint
														</option>
														<option value='real'>
															real
														</option>
														<option value='time'>
															time
														</option>
													</select>
												</td>
											</tr>
											<tr style='height:23px;'>
												<td>
													Weight
												</td>
												<td style='vertical-align:middle;'>
													<input type='text' maxlength='5' style='width:50px;' class='iptText' id='mindEditorTool_subtype_weight'/>
														<input type='checkbox' id='mindEditorTool_subtype_notNull'> Not Null
												</td>
											</tr>
											<tr style='height:23px;'>
												<td>
													Comment
												</td>
												<td>
													<input type='text' maxlength='240' style='width:100%;' class='iptText' id='mindEditorTool_subtype_comment'/>
												</td>
											</tr>
											<tr style='height:23px;'>
												<td>
													Mask
												</td>
												<td>
													<input type='text' maxlength='240' style='width:100%;' class='iptText' id='mindEditorTool_subtype_mask'/>
												</td>
											</tr>
											<tr style='height:23px;'>
												<td colspan='2'>
													Options
												</td>
											</tr>
											<tr>
												<td colspan='2'>
													<textarea style='width:100%; height:70px;' class='iptText' id='mindEditorTool_subtype_options'></textarea>
													<sup>
														Use = to identify your option's values, and [ENTER] to separate them<br/>
														e.g.: value1=text that belongs to value 1<br>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; value2=text that belongs to value 2
													</sup>
												</td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<tr style='height:23px;'>
								<td class='mindEditorTool_button'>
									<input type='button'
										   class='ui-state-default ui-corner-all'
										   value='Apply'
										   onclick="Mind.MindEditor.AddsubtypeLine();">
								</td>
							</tr>
						</table>
					</div>
					<div id="mindEditorTool_Entities" style='width:262px;' guia='true'>
						<table style='width:100%; height:220px;'>
							<tr style='height:23px;'>
								<td>
									Left Entity
								</td>
								<td>
									<input type='text' maxlength='40' class='iptText' id='mindEditorTool_Entities_left'/>
								</td>
							</tr>
							<tr style='height:23px;'>
								<td>
									Right Entity
								</td>
								<td>
									<input type='text' maxlength='40' class='iptText' id='mindEditorTool_Entities_right'/>
								</td>
							</tr>
							<tr style='height:23px;'>
								<td>
									Relation
								</td>
								<td>
									<select id='mindEditorTool_Entities_Rel'>
										<option value='1/1'>
											1/1
										</option>
										<option value='n/n'>
											n/n
										</option>
										<option value='1/n'>
											1/n
										</option>
										<option value='n/1'>
											n/1
										</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan='2'>
								</td>
							</tr>
							<tr style='height:23px;'>
								<td colspan='2'
									class='mindEditorTool_button'>
									<input type='button'
										   class='ui-state-default ui-corner-all'
										   value='Apply'
										   onclick="Mind.MindEditor.AddEntityLine();">
								</td>
							</tr>
						</table>
					</div>
					<div id="mindEditorTool_Attributes" style='width:262px;' guia='true'>
						<table style='width:100%; height:220px;'>
							<tr>
								<td>
									<div style='height:190px;overflow-y:auto;overflow-x:hidden;'>
										<table style='width:100%;'>
											<tr style='height:23px;'>
												<td>
													Entity
												</td>
												<td>
													<input type='text' maxlength='40' class='iptText' id='mindEditorTool_Attribute_entity'/>
												</td>
											</tr>
											<tr style='height:23px;'>
												<td>
													Attribute
												</td>
												<td>
													<input type='text' maxlength='40' class='iptText' id='mindEditorTool_Attribute_att'/>
												</td>
											</tr>
											<tr style='height:23px;'>
												<td>
													SubType
												</td>
												<td>
													<select id='mindEditorTool_Attribute_subType'
															onchange="document.getElementById('attOptionsNotSubType').style.display=  (this.value!= '')? 'none': '';">
														<option value=''>
														</option>
													</select>
												</td>
											</tr>
											<tbody id='attOptionsNotSubType'>
												<tr style='height:23px;'>
													<td>
														Type
													</td>
													<td>
														<select id='mindEditorTool_Attribute_type'>
															<option value='char'>
																char
															</option>
															<option value='varchar'>
																varchar
															</option>
															<option value='text'>
																text
															</option>
															<option value='password'>
																password
															</option>
															<option value='file'>
																file
															</option>
															<option value='smallint'>
																smallint
															</option>
															<option value='int'>
																int
															</option>
															<option value='bigint'>
																bigint
															</option>
															<option value='real'>
																real
															</option>
															<option value='time'>
																time
															</option>
														</select>
													</td>
												</tr>
												<tr style='height:23px;'>
													<td>
														Weight
													</td>
													<td style='vertical-align:middle;'>
														<input type='text' maxlength='5' style='width:50px;float:left;' class='iptText' id='mindEditorTool_Attribute_weight'/>
														
															<input type='checkbox' id='mindEditorTool_Attribute_notNull'> Not Null
														
													</td>
												</tr>
												<tr style='height:23px;'>
													<td>
														Default
													</td>
													<td>
														<input type='text' maxlength='240' style='width:100%;' class='iptText' id='mindEditorTool_subtype_default'/>
													</td>
												</tr>
												<tr style='height:23px;'>
													<td>
														Comment
													</td>
													<td>
														<input type='text' maxlength='240' style='width:100%;' class='iptText' id='mindEditorTool_Attribute_comment'/>
													</td>
												</tr>
												<tr style='height:23px;'>
													<td>
														Mask
													</td>
													<td>
														<input type='text' maxlength='240' style='width:100%;' class='iptText' id='mindEditorTool_Attribute_mask'/>
													</td>
												</tr>
												<tr style='height:23px;'>
													<td colspan='2'>
														Options
													</td>
												</tr>
												<tr>
													<td colspan='2'>
														<textarea style='width:100%; height:70px;' class='iptText' id='mindEditorTool_Attribute_options'></textarea>
														<sup>
															Use = to identify your option's values, and [ENTER] to separate them<br/>
															e.g.: value1=text that belongs to value 1<br>
															&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; value2=text that belongs to value 2
														</sup>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
							<tr style='height:23px;'>
								<td class='mindEditorTool_button'>
									<input type='button'
										   class='ui-state-default ui-corner-all'
										   value='Apply'
										   onclick="Mind.MindEditor.AddAttributeLine();">
								</td>
							</tr>
						</table>
					</div>
				</div>
			</form>
		</div>
		<div id='debugBalloon'
			 style='display:none;'>
			<img src='images/del.gif'
				 align='right'
				 onclick="Mind.Project.CloseBalloons()"/>
			<div class='balloon'>
				<br/>
			</div>
			<div class='tip'>
			</div>
		</div>
		<canvas id='oCanvas' width='600' height='400'></canvas>
		<div id='mindEditorSearch'
			 style='display:none;'>
			 <center>
				<input type='text' class='iptText' id='mindEditorSearch_filter' />
				<input type='button' value='Find' class='ui-state-default ui-corner-all' onclick="Mind.MindEditor.Search(this);" />
				<div style='width:100%;text-align:left;'>
					<br/>
					<img src='{?$_MIND['imageDir']}/blue_marker.png'>(full match): <span></span><br/>
					<img src='{?$_MIND['imageDir']}/light_blue_marker.png'>(partial match): <span></span>
				</div>
			</center>
		</div>
	</body>
</html>
