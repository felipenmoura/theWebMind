<html>
	<head>
		<title>theWebMind(s) - 2.0</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="shortcut icon" href="img/favico.png"/>
		<!-- Scripts -->		
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.ui.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/jquery.js"></script><!-- jQuery -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/jquery.ui.all.js"></script><!-- jQuery -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/jquery-ui/ui.progressbar.js"></script><!-- jQuery -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/jquery.metadata.js"></script><!-- jQuery -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/json.js"></script><!-- jSon -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/accelerators.js"></script><!-- Menus -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mbMenu.js"></script><!-- Menus -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/jquery.hoverIntent.js"></script><!-- jQuery -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/grow/jquery.blockui.js"></script><!-- jQuery -->
		
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/tooltip.js"></script><!-- Mind ToolTips -->		
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/coockies.js"></script><!-- Mind Cookies -->				
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.project.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.mindeditor.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.data_dictionary.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.tooltip.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.update.js"></script><!-- Mind -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/mind.der.js"></script><!-- Mind -->
		
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/colorpicker.js"></script><!-- ColorPicker -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/eye.js"></script><!-- ColorPicker -->
		<script type="text/javascript" src="{?$_MIND['scriptSrc']}/layout.js"></script><!-- ColorPicker -->
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
		
		<!-- Execution -->
		<script type="text/javascript">
			$(document).ready(function(){
				Mind.Utils.SetDocumentDefaults();
				Mind.Progress.Init();
				Mind.Progress.Increment();
				Mind.Theme.Load();
				Mind.Properties.path = "{?$_MIND['fwComponents']}";
				Mind.Properties.scriptsPath = "{?$_MIND['scriptSrc']}";
				Mind.Properties.showDeveloperMenu = '{?$_MIND['showDeveloperMenu']}';
				Mind.Properties.imagesPath = "{?$_MIND['imageDir']}";
				Mind.Panels.Init();
				$.getScript(Mind.Properties.scriptsPath + "/mind.components.js");
				$.getScript(Mind.Properties.scriptsPath + "/mind.menu.js");				
				Mind.ToolTip.Init();
				Mind.Menus.Init();
			});		
		</script>
	</head>
	<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0' style='margin:0px;padding:0px;'>
		<div id='colorPickerWrapper' class="wrapper"><p id="colorpickerHolder"></p></div>
		<div id='blocker'
			 style='width: 100%;
					height: 100%;
					position: absolute;
					left: 0px;
					top: 0px;
					z-index: 999999;
					background-color: #fff;
					-moz-opacity: 0.6;
					filter: Alpha(Opacity=60);'>
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
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#EDEDED" style='border-bottom: solid 1px #666;'>
						<tr>
							<td valign="bottom" style='border-bottom: solid 1px #dedede;'>
								<table  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="container">
									<tr>
										<td class="myMenu" align="right">
											<!-- start horizontal menu -->
											<table class="rootVoices" cellspacing='0' cellpadding='0' border='0'>
												<tr>
													<td accelerator="F" class="rootVoice {menu: 'menu_file'}">File</td>
													<td accelerator="V" class="rootVoice {menu: 'menu_display'}">View</td>
													<td accelerator="T" class="rootVoice {menu: 'menu_tools'}">Tools</td>
													<td accelerator="M" class="rootVoice {menu: 'menu_manager'}">Manage</td>
													<td accelerator="P" class="rootVoice {menu: 'menu_plugins'}">Plugins</td>
													<td class="rootVoice {menu: 'menu_developer'}" style='display:none;' id='developerMenu'>Developer</td>
													<td accelerator="H" class="rootVoice {menu: 'menu_help'}" >Help</td>													
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
					<div id="mind_layout_center" style='width:10px; height:10px; overflow:auto;'>
						<div>							
						</div>
					</div>
					<div class="vertical-resizable-bar-right" id="vertical_resizable_bar_right">
						<table cellpadding='0' cellspacing='0' style='height:100%;'>
							<tr>
								<td>
									<div class='barBt'>&nbsp;</div>
								</td>
							</tr>
						</table>
					</div>
					<div id="mind_layout_right">
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
							<img src='asdasd' style='cursor:pointer;' onclick="$('#debuggerEstructureCodes').hide();$('#debuggerEstructureTree').show();">
							<img src='asdasd' style='cursor:pointer;' onclick="$('#debuggerEstructureTree').hide();$('#debuggerEstructureCodes').show();">
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
	</body>
</html>