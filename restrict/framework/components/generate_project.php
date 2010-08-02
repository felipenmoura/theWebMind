<?php 
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
	
	if(isset($_GET['projectName']) && isset($_SESSION['user']['login'])){
		$p= new Project($_GET['projectName'], $_SESSION['user']['login']);
	}
?>
<script type="text/javascript">
function enableNext()
{
	$("#generateModalForm").wizard("enableNext");
}
function disableNext()
{
	$("#generateModalForm").wizard("disableNext");
}
jQuery(function($){
	var a = $("#generateModalForm").wizard({
		animated: "slideDown",
		historyEnabled: false,
		formPluginEnabled: false,
		validationEnabled: false,
		show: function(evnt, args)
		{
			switch(args.stepInx)
			{
				case 0:
					Mind.Project.tmp= Array();
					$("#generateModalForm").wizard("enableNext");
				break;
				case 1:
				{
					if(document.getElementById('generate_only_database').checked)
					{
						document.getElementById('noModule').style.display= '';
						document.getElementById('moduleStep').style.display= 'none';
						enableNext();
						return;
					}else{
							document.getElementById('noModule').style.display= 'none';
							document.getElementById('moduleStep').style.display= '';
							if(Mind.Project.tmp['selectedModule'])
								enableNext();
						 }
					if(!Mind.Project.tmp['selectedModule'])
						$("#generateModalForm").wizard("disableNext");
				}
				break;
				case 2:
				{
					disableNext();
					document.getElementById('thirdStepGenerating').innerHTML= "Loading...";
					var x= null;
					
					var sec= 0;
					if(Mind.Project.tmp['dependences']) // it's not ONLY datasbase
					{
						for(x in Mind.Project.tmp['dependences']['styles'])
						{
							$("<link>").attr({"rel":"stylesheet",
											  "type":"text/css",
											  "href":Mind.Project.tmp['dependences']['styles'][x].toLowerCase(),
											  "media":"screen"
											 }).appendTo(document.getElementsByTagName("head")[0]);
							sec+= 500;
						}
						for(x in Mind.Project.tmp['dependences']['scripts'])
						{
							$.getScript(Mind.Project.tmp['dependences']['scripts'][x].toLowerCase(),function(){});
							sec+= 500;
						}
						setTimeout(function(){
												$.ajax({
														type: 'POST',
														url: Mind.Properties.path+'/module_options.php'.toLowerCase(),
														data: {'module': Mind.Project.tmp['selectedModule'], 'noDataBase': document.getElementById('database_pref_noDataBase').checked},
														success: function(ret){
																				document.getElementById('thirdStepGenerating').innerHTML= ret;
																				$("#generateModalForm").wizard("enableNext");
																			  },
														error : function(XMLHttpRequest,textStatus,errorThrown){
															Mind.AjaxHandler.Capture(XMLHttpRequest);
															enableNext();
														}
													  });
											 }, sec);
					}else{
							setTimeout(function(){
													$.ajax({
															type: 'POST',
															url: Mind.Properties.path+'/module_options.php'.toLowerCase(),
															data: {onlyDataBase: 'true'},
															success: function(ret){
																					document.getElementById('thirdStepGenerating').innerHTML= ret;
																					$("#generateModalForm").wizard("enableNext");
																				  },
															error : function(XMLHttpRequest,textStatus,errorThrown){
																Mind.AjaxHandler.Capture(XMLHttpRequest);
																enableNext();
															}
														  });
												 }, sec);
							//document.getElementById('thirdStepGenerating').innerHTML= "There are no advanced options";
							enableNext();
						 }
				}
				break;
				default:
					$("#generateModalForm").wizard("disableNext");
			}
			
			if(args.stepInx == 0)
			{
				$("#generateModalForm").wizard("enableNext");
			}
		},
		onSubmit : function(){
			Mind.Project.tmp= false;
			Mind.Project.tmp= Array();
			
			$('#generatingLoadBar').progressbar({
													value:0,
													change:function(event, ui){
														document.getElementById('generatingLoadBarPerc').innerHTML= $('#generatingLoadBar').progressbar('option', 'value')+'%';
													}
												});
			var v= Mind.Project.attributes['version'][0];
			if(document.getElementById('database_pref_noDataBase').checked)
			{
				v+= '.'+ Mind.Project.attributes['version'][1];
				v+= '.'+ (parseInt(Mind.Project.attributes['version'][2]) + 1);
			}else{
					v+= '.'+ (parseInt(Mind.Project.attributes['version'][1])+ 1);
					v+= '.0';
				 }
			document.getElementById('generatingProjectName').innerHTML= Mind.Project.attributes['name'];
			document.getElementById('generatingProjectVs').innerHTML= v;
			document.getElementById('hiddenProjectAttributes').value= JSON.stringify(Mind.Project.attributes);
			document.getElementById('generateModalForm').action= Mind.Properties.path+'/generating.php';
			document.getElementById('generateModalForm').submit();
			document.getElementById('generateModalForm').style.display= 'none';
			document.getElementById('generatingWithModule').style.display= '';
		}
	});
	
	$("#fieldsetDev").bind("click",function(){
		$("#database_pref_development").trigger("click");
	});
	$("#fieldsetDev").bind("mouseover",function(){
		//$(this).css("border","solid 2px #c99300");
		$(this).css("background","#f0f0f0");
	});
	$("#fieldsetDev").bind("mouseout",function(){
		//$(this).css("border","");
		$(this).css("background","transparent");
	});
	
	$("#fieldsetProd").bind("click",function(){
		$("#database_pref_production").trigger("click");
	});
	$("#fieldsetProd").bind("mouseover",function(){
		//$(this).css("border","solid 2px #c99300");
		$(this).css("background","#f0f0f0");
	});
	$("#fieldsetProd").bind("mouseout",function(){
		//$(this).css("border","");
		$(this).css("background","transparent");
	});
	
	$("#fieldsetOnlyDataBase").bind("click",function(){
		$("#generate_only_database").trigger("click");
	});	
	$("#fieldsetOnlyDataBase").bind("mouseover",function(){
		//$(this).css("border","solid 2px #c99300");
		$(this).css("background","#f0f0f0");
	});
	$("#fieldsetOnlyDataBase").bind("mouseout",function(){
		//$(this).css("border","");
		$(this).css("background","transparent");
	});
	
	$("#fieldsetNoDataBase").bind("click",function(){
		$("#database_pref_noDataBase").trigger("click");
	});
	$("#fieldsetNoDataBase").bind("mouseover",function(){
		//$(this).css("border","solid 2px #c99300");
		$(this).css("background","#f0f0f0");
	});
	$("#fieldsetNoDataBase").bind("mouseout",function(){
		//$(this).css("border","");
		$(this).css("background","transparent");
	});
	<?php
		$p= new Project($_SESSION['currentProject'], $_SESSION['user']['login']);
		echo ' Mind.Project.attributes["version"][0]= '.$p->version[0].'; ';
		echo ' Mind.Project.attributes["version"][1]= '.$p->version[1].'; ';
		echo ' Mind.Project.attributes["version"][2]= '.$p->version[2].'; ';
	?>
});
</script>
<div class="project_generate_wizardParent" id='project_generate_wizardParent'>
	<div class="project_generate_wizard">
		<!--<img src="images/wizard.png" style="position:absolute;bottom:-5px;right:-6px;z-index:0">-->
	   <form id="generateModalForm" name='generateModalForm' target='generatingAllStatus' method='POST'>
		   <div class="step" style="padding:10px; height:400px;">
			 <span style="font-weight:bold;font-size:15px">Step <span style="font-size:24px">1</span> - Choose your database preferences</span>
			 <table style='width:100%;'>
				<tr>
					<td>
						<input style="width:16px;height:16px;" id="database_pref_noDataBase" type="radio" name="database_pref" checked='checked' value="no" />
					</td>
					<td>
						<fieldset id="fieldsetNoDataBase">
							<legend><b>Do NOT Generate Data Base:</b></legend>
							Your Database wont suffer any change.
						</fieldset>
					</td>
				</tr>
				<tr>
					<td>
						<input style="width:16px;height:16px;" id="database_pref_development" name="database_pref" type="radio" value="development" />
						<textarea style='display:none;' name='p' id='hiddenProjectAttributes' ></textarea>
					</td>
					<td>
						<fieldset id="fieldsetDev">
							<legend><b>Development Database:<b></legend>
							<table>		
								<tr>
									<td>
										Address:
									</td>
									<td>
										<i><?php echo $p->environment["development"]["dbAddress"];?> : <?php echo $p->environment["development"]["dbPort"];?></i>
									</td>
								</tr>
								<tr>
									<td>
										Name:
									</td>
									<td>
										<i><?php echo $p->environment["development"]["dbName"];?></i>
									</td>
								</tr>
								<tr>
									<td>
										Root User: 
										<i><?php echo $p->environment["development"]["rootUser"];?></i>
									</td>
									<td style='padding-left:15px;'>
										User: 
										<i><?php echo $p->environment["development"]["user"];?></i>
									</td>
								</tr>
							</table>
						</fieldset>					
					</td>
				</tr>
				<tr>
					<td>
						<input style="width:16px;height:16px;" id="database_pref_production" type="radio" name="database_pref" value="production" />
					</td>
					<td>
						<fieldset id="fieldsetProd">
							<legend><b>Production Database:</b></legend>
							<table>
								<tr>
									<td>
										Address:
									</td>
									<td>
										<i><?php echo $p->environment["production"]["dbAddress"];?> : <?php echo $p->environment["production"]["dbPort"];?></i>
									</td>
								</tr>
								<tr>
									<td>
										Name:
									</td>
									<td>
										<i><?php echo $p->environment["production"]["dbName"];?></i>
									</td>
								</tr>
								<tr>
									<td>
										Root User: 
										<i><?php echo $p->environment["production"]["rootUser"];?></i>
									</td>
									<td style='padding-left:15px;'>
										User: 
										<i><?php echo $p->environment["production"]["user"];?></i>
									</td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td>
						<input style="width:16px;height:16px;" id="generate_only_database" type="radio" name="database_pref" value="only_database" />
					</td>
					<td>
						<fieldset id="fieldsetOnlyDataBase">
							<legend><b>Generate only Database:</b></legend>
							<table>
								<tr>
									<td>
										<select name='only_database'>
											<option id='onlyDB_development' value='development'>Development</option>
											<option id='onlyDB_production' value='production'>Production</option>
										</select>
									</td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>
		
		   </div>

		   <div class="step" style="padding:10px" id="step2">
			<div style="height:400px;overflow:auto;">
				<div id='noModule'
					 style='display:none;'>
					<span style="font-weight:bold;font-size:15px">Step <span style="font-size:24px">2</span> - Choose your Module preferences</span>
					<br/><br/>
					No Module will be needed.
				</div>
				<div id='moduleStep'>
					<span style="font-weight:bold;font-size:15px">Step <span style="font-size:24px">2</span> - Choose your Module preferences</span>
					<table cellspacing='0' cellpadding='0'>
						<tr>
							<td style="border:solid 1px; vertical-align:top;" id="mind_module_list">
								<table cellpadding='2' cellspacing='0'>
								 <?php 
									$l= $_MIND['fw']->getModulesList();
									for($i=0;$i<sizeof($l);$i++)
									{
										if(is_dir($_MIND['rootDir'].$_MIND['moduleDir'].'/'.$l[$i]))
										{
										?>
											<tr>
												<td class='wizardModuleItem'
													onmouseover="this.className= 'wizardModuleItem_over';"
													onmouseout="this.className= 'wizardModuleItem';"
													onclick="disableNext(); Mind.Project.GetModule('<?php echo $l[$i]; ?>', this);">
													<input type='radio' name='module' style='display:non;' value="<?php echo $l[$i]; ?>">
													<?php echo $l[$i]; ?>
												</td>
											</tr>
										<?php
										}
									}
								 ?>
								</table>
							</td>
							<td style="vertical-align:top;padding:8px;border:solid 1px;width:100%" id="mind_module_content">
								<br/>
							</td>
						</tr>
					</table>
				</div>
			</div>
		   </div>
		   <div id="step3" class="step" style='padding:10px; height:400px;overflow:auto;'>
			 <span style="font-weight:bold;font-size:15px">Step <span style="font-size:24px">3</span> - Choose your advanced preferences</span>
			 <div id='thirdStepGenerating'>
			 </div>
		   </div>
		   <br>
		   <button value="Reset" style="background-repeat:no-repeat;background-position:left;background-image:url(images/arrow_left.png);width:80px;height:27px;font-size:12px" class="ui-corner-all wizard_back">Reset</button>
		   <button value="Submit" class="ui-corner-all wizard_next" style="background-repeat:no-repeat;background-position:right;background-image:url(images/arrow_right.png);width:80px;height:27px;font-size:12px" >next</button>
		 </form>
		 <div style='height:360px; display:none;' id='generatingWithModule'>
			<fieldset style='border-bottom:none;'>
				<legend>
					<b>
						Generating
					</b>
				</legend>
				<br/>
				<div>
					Generating project <b><span id='generatingProjectName'>NAME</span> <span id='generatingProjectVs'>0.0.0</span></b><br/>
					in <b><span id='generatingProjectDir'>user's directory</span></b><br/><br/>
					Generated by <b><?php echo $_SESSION['user']['name']; ?></b> at <b><?php echo date('d\t\h F/Y - H:i a '); ?></b>
				</div>
				<br/>
				<div style='text-align:center;padding:4px;'>
					<!--<div style='background-image:url(<?php echo $_MIND['imageDir'].'/gen_loader.gif'?>);width:120px;height:12px;'>
					</div>-->
					<center>
						<div id='generatingLoadBar' style='margin:4px;width:420px;height:16px;'>
							<span id='generatingLoadBarPerc'
								  style='position:absolute;right:115px;'>
							</span>
						</div>
					</center>
				</div>
				<center>
					<br/>
					<div style='height:200px;'>
						<div style='border:solid 1px #666;width:460px; overflow:hidden;padding3px;white-space:nowrap;text-align:left;padding:2px;height:15px;'>
							Currently:
							<span id='generatingCurrentStatus' style='padding-left:14px;'>
								<br/>
							</span>
						</div>
						<div style='text-align:right'>
							<sup style='cursor:pointer;font-size:9px;'onclick="$('#generatingAllStatus').toggle();">Show/Hide Details</sup>
						</div>
						<iframe  name='generatingAllStatus' id='generatingAllStatus'
								 style='border:inset 1px #666;
										height:140px;
										width:460px;
										overflow:auto;
										display:non;
										padding:2px;
										background-color:#fff;
										text-align:left;
										margin-top:4px;'></iframe>
					</div>
				</center>
				<div style='text-align:right;'>
					<input class='ui-state-default ui-corner-all'
						   style='width:50px;height:22px;text-align:center;cursor:default;'
						   value='Abort'
						   onclick="Mind.Project.AbortProcessing();"/>
				</div>
			</fieldset>
		</div>
	</div>
</div>

