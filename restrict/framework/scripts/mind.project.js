Mind.Project= {
	changed:false,
	attributes: null,
	waitingToRun: false,
	waitingToExport: false,
	oldCode: '',
	tmp: Array(),
	knowledge: false,
	Load: function(projectToLoad, callback)
	{
		if(Mind.Project.attributes!=null)
			if(projectToLoad == Mind.Project.attributes.name)
				return false;
		if(Mind.Project.changed)
			if(!confirm("There is an unsaved project opened, continuing, you will loose the unsaved changes.\nAre you sure you want to continue?"))
				return false;
		if(projectToLoad)
		{
			Mind.Utils.SetLoad(true, 'Loading Project...');
			Mind.Component.Load(
								{componentName: "projectData", data: {name:projectToLoad}},
								{componentName: "project", data: {name:projectToLoad}},
								function(comps){
									if(comps.substring(0,1)=='M')
									{
										eval(comps)
										Mind.Project.Load();
										apagaCookie('theWebMind_currentProject');
										return false;
									}
									comps= Mind.Component.Parse(comps);
									projectData= comps.projectData;
									/*********************************  BOTTOM PANEL  ************************************/
									var str= '';									
									str+= "<div class='tabs' style='width:100%; height:100%;margin-left:-6px;'>";
										str+= "<ul>";
											str+= "<li mindEditorTab='true' ><a href='#outputPanel_infoConfTab'>About</a></li>";
											str+= "<li mindEditorTab='true' ><a href='#outputPanel_DDLTab'>DDL</a></li>";
											str+= "<li mindEditorTab='true' ><a href='#outputPanel_ERDTab'><nobr>ER Diagram</nobr></a></li>";
											str+= "<li mindEditorTab='true' ><a href='#outputPanel_DDTab'><nobr>Data Dictionary</nobr></a></li>";
											str+= "<li mindEditorTab='true' ><a href='#outputPanel_DebugTab' id='outputPanel_DebugTab_Label'>Debug</a></li>";
										str+= "</ul>";
										str+= "<div id='outputPanelPaneBody' style='height:100%;overflow:auto;background-color:#f5f5f5;' autoresize='true'>";
											str+= "<div style='position:absolute;width:24px;height:20px;top:10px;right:4px'>";
												str+= "<img src='"+Mind.Properties.imagesPath+"/bt_full_editor_over.png' onclick='Mind.Panels.SetFull();' onmouseover='this.src=\"images/bt_full_close_over.png\"' onmouseout='this.src=\"images/bt_full_editor_over.png\"'>";
											str += "</div>";
												str+= "<div id='outputPanel_infoConfTab'>";
													str+= '<table><tr>';
													str+= '<td><b>Name:</b> '+projectData.name+'</td>';
													str+= '<td><b>Created:</b> '+projectData.date+'</td><td></td></tr>';
													str+= '<tr><td><b>Language:</b> '+projectData.lang+' &nbsp; &nbsp; &nbsp; &nbsp; </td>';
													str+= '<td><b>DBMS:</b> '+projectData.dbms+'</td>';
													str+= '<td><b>Owner:</b> '+projectData.owner+' ('+projectData.email+')</td></tr>';
													str+= '<tr><td colspan="3"><hr/></td></tr>';
													str+= '<tr><td><b>Description:</b></td><td colspan="2">'+projectData.description+'</td></tr></table>';
												str+= "</div>";
												str+= "<div id='outputPanel_DDLTab'>Not loaded";
												str+= "</div>";
												str+= "<div id='outputPanel_ERDTab'><table cellspacing='0' cellpadding='0' style='width:100%;height:100%;'><tr><td id='outputPanel_der_list' style='width:210px;'><div id='outputPanel_der_listContainer'><br/></div></td><td id='outputPanel_der_body' rowspan='2'></td></tr>";
												str+= "<tr>";
												
												str+= "<td style=''><div id='erdToolBar' style='display:none;'><br/></div><img src='"+Mind.Properties.imagesPath+"/der_tools_button.png' onclick='Mind.ERD.ShowToolBar();' />";
												str+= "&nbsp;<img src='"+Mind.Properties.imagesPath+"/clear_console.png' style='cursor:pointer;' id='DERClearButton' title='Clear' onclick='if(confirm(\"Clear the current Diagram?\")) Mind.ERD.Clear();'/>";
												str+= "&nbsp;<img src='"+Mind.Properties.imagesPath+"/pin.png' style='cursor:pointer;' id='DERPinButton' title='Clear' onclick='Mind.ERD.PinPanel()'/>";
												str+= "&nbsp;<img src='"+Mind.Properties.imagesPath+"/help.png' style='cursor:pointer;' title='Help' onclick='Mind.Help.Open(\"IDE#Diagrama_ER\")' height:16/>";
												
												str+= "&nbsp;<img src='"+Mind.Properties.imagesPath+"/Zoom-more.png' style='cursor:pointer;' title='More Zoom' onclick='Mind.ERD.ApplyZoom(1)' height:16/>";
												str+= "&nbsp;<img src='"+Mind.Properties.imagesPath+"/Zoom-less.png' style='cursor:pointer;' title='Less Zoom' onclick='Mind.ERD.ApplyZoom(-1)' height:16/>";
												str+= "&nbsp;<img src='"+Mind.Properties.imagesPath+"/plot.png' style='cursor:pointer;' title='Plot the current diagram' onclick='Mind.ERD.Plot()' height:16/>";
												
												str+= "<br/></td>";
												
												str+= "</tr></table>";
												str+= "</div>";
												str+= "<div id='outputPanel_DDTab'>Not loaded";
												str+= "</div>";
												str+= "<div id='outputPanel_DebugTab'>No Errors";
												str+= "</div>";
										str+= "</div>";
									str+= "</div>";
									
									Mind.Panel['bottom'].Update(str);
									$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs({
									   select: function(event, ui) {
										if(ui.index == 3 &&
											(document.getElementById('outputPanel_DDTab').innerHTML== 'Building...' ||
												document.getElementById('outputPanel_DDTab').innerHTML== 'Not loaded'
											)
										  )
										{
											// loading message here AQUI
											document.getElementById('outputPanel_DDTab').innerHTML= Mind.Project.WriteDataDictionary(Mind.Project.knowledge).innerHTML;
										}else if(ui.index == 1 &&
													(document.getElementById('outputPanel_DDLTab').innerHTML== 'Building...' ||
														document.getElementById('outputPanel_DDLTab').innerHTML== 'Not loaded'
													))
											  {
												document.getElementById('outputPanel_DDLTab').innerHTML= Mind.Project.MountDDLViewer(Mind.Project.knowledge);
												Mind.Project.MountDDLViewerPlugin(Mind.Project.knowledge);
												Mind.Panel['bottom'].Adjust();
											  }else {
														//$('#outputPanel_DDTab').html('Building...');
														//$('#outputPanel_DDLTab').html('Building...');
													}
									   }
									}).find('.ui-tabs-nav').sortable({axis:'x'});
									$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('select', 0);
									
										$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 1).tabs('disable', 2).tabs('disable', 3).tabs('disable', 4).tabs('disable', 5);
										
										
									Mind.Project.ActiveMenus();
									Mind.Panel['bottom'].Adjust();
									
																				
									$("#DERPrintButton").bind('click', function(){
																					Mind.ERD.Print();
																				});
									$('#'+Mind.Panel['bottom'].htmlElement.id+' .tabs [mindEditorTab=true]').bind('dblclick', function(){
										Mind.Panels.SetFull();
									})
									/*********************************  RIGHT PANEL  ************************************/
									/*var mindApplicationList= comps.mindApplicationList;
									alert(mindApplicationList);*/
									//Mind.Panel['right'].Update(mindApplicationList);
									/*
										
									*/
									/*********************************  CENTER PANEL  ************************************/
									var project= comps.project;
									Mind.Panel['center'].Update(project);
									/*
										rodar aqui, os plugins que devem rodar ao abrir um projeto
									*/
									$('#colorpickerField1').ColorPicker({
										onSubmit: function(hsb, hex, rgb, el) {
											$(el).val(hex);
											$(el).ColorPickerHide();
										},
										onBeforeShow: function () {
											$(this).ColorPickerSetColor(this.value);
										}
									})
									.bind('keyup', function(){
										$(this).ColorPickerSetColor(this.value);
									});
									/*********************************  POLPULATE THE OBJECT  ************************************/
									// project properties in JS
									Mind.Project.attributes= {};
									Mind.Project.attributes.name= projectData.name;
									Mind.Project.attributes.lang= projectData.lang;
									Mind.Project.attributes.dbms= projectData.dbms;
									Mind.Project.attributes['description']= projectData.description;
									Mind.Project.attributes['verb']= projectData.verb;
									//Mind.Project.attributes['environment']= projectData.environment;
									Mind.Project.attributes.users= false;
									Mind.Project.attributes.users= Array();
									
									for(var x in projectData.users)
									{
										Mind.Project.attributes.users.push(projectData.users[x].login);
									}
									
									//Mind.Project.attributes['users']= projectData.users;
									Mind.Project.attributes.owner= projectData.owner;
									Mind.Project.attributes.date= projectData.date;
									Mind.Project.attributes.email= projectData.email;
									Mind.Project.attributes.processed= projectData.processed;
									Mind.Project.attributes.version= projectData.version;
									Mind.Panel['bottom'].Adjust();
									gravaCookie('theWebMind_currentProject', projectToLoad);
									
									Mind.Project.attributes.wml= document.getElementById('mindEditor').value;
									Mind.Project.oldCode= Mind.Project.attributes.wml;
									Mind.MindEditor.lineNumber= 1;
									Mind.MindEditor.Typing(Mind.Project.attributes.wml);
									Mind.Utils.SetLoad(false);
									Mind.MindEditor.SetUserConfig();
									Mind.Progress.Increment(20,"loadProject");
									
									Mind.Plugins.LoadTabPlugins();
									setTimeout(Mind.Plugins.OnOpening, 2000);
									
									// AQUI se op��es para rodar automaticamente estiverem setadas
									setTimeout(function(){Mind.Project.Run();}, 2000);
									if(callback)
										(callback)();
									
									Mind.Recent.Add(projectToLoad);
								}
							   );
		}else{
				Mind.Project.changed= false;
				projectData= '';
				Mind.Component.Load(
								{componentName: "home", data: {name:projectToLoad}},
								function(comps){
									comps= Mind.Component.Parse(comps);
									Mind.Panel['center'].Update(comps.home);
								});
				var str= '';
					str+= "<div class='tabs' style='width:100%; height:100%;margin-left:-6px;'>";
						str+= "<ul>";
							str+= "<li><a href='#outputPanel_infoConfTab'>About</a></li>";
							str+= "<li><a href='#outputPanel_DDLTab'>DDL</a></li>";
							str+= "<li><a href='#outputPanel_ERDTab'>ER Diagram</a></li>";
							str+= "<li><a href='#outputPanel_DDTab'>Data Dictionary</a></li>";
							str+= "<li><a href='#outputPanel_DebugTab'>Debug</a></li>";
						str+= "</ul>";
						str+= "<div style='height:100%;overflow:auto;background-color:#f5f5f5;' autoresize='true'>";
							str+= "<div id='outputPanel_infoConfTab'>";
								str+= '<table class="disabled"><tr>';
								str+= '<td><b>Name:</b> '+projectData+'</td>';
								str+= '<td><b>Created:</b> '+projectData+'</td><td></td></tr>';
								str+= '<tr><td><b>Language:</b> '+projectData+' &nbsp; &nbsp; &nbsp; &nbsp; </td>';
								str+= '<td><b>DBMS:</b> '+projectData+'</td>';
								str+= '<td><b>Owner:</b> '+projectData+'</td></tr>';
								str+= '<tr><td colspan="3"><hr/></td></tr>';
								str+= '<tr><td><b>Description:</b></td><td colspan="2">'+projectData+'</td></tr></table>';
							str+= "</div>";
							str+= "<div id='outputPanel_DDLTab'>Building...";
							str+= "</div>";
							str+= "<div id='outputPanel_ERDTab'><br>";
							str+= "</div>";
							str+= "<div id='outputPanel_DDTab'>Building...";
							str+= "</div>";
							str+= "<div id='outputPanel_DebugTab'>No errors";
							str+= "</div>";
						str+= "</div>";
					str+= "</div>";
					Mind.Panel['bottom'].Update(str);
					$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs();
					$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('select', 0);
					if(!projectData.processed)
					{
					
						$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 0).tabs('disable', 1).tabs('disable', 2).tabs('disable', 3).tabs('disable', 4).tabs('disable', 5);
					}
					Mind.Panel['right'].Update();
					Mind.Project.DeactiveMenus();
					this.attributes= null;
					apagaCookie('theWebMind_currentProject');
					Mind.Progress.Increment(20,"loadProject");
			 }
		try{document.getElementById('DDRLoadList').style.display= 'none';}catch(e){}
	},
	ActiveMenus: function()
	{
		$("[projectDependence]").removeAttr('disabled');
	},
	DeactiveMenus: function()
	{
		$("[projectDependence]").attr('disabled', 'true');
	},
	Close: function(){
		Mind.Component.Load('projectList',
								function (comps){
									comps= Mind.Component.Parse(comps);
									Mind.Panel['left'].Update(comps['projectList']);
								}
							);
		this.Load();
	},
	Properties: function(p)
	{
		Mind.Component.Load({
		componentName : "projectData",
		data : {name:p}
		}, function(comps){
				comps= Mind.Component.Parse(comps);
				var projectData= comps.projectData;
				projectData.version= projectData.version[0]+'.'+projectData.version[1]+'.'+projectData.version[2];
				var str= '<b>Project:</b> '+projectData.name+' vr'+projectData.version+'<br>';
				str+= '<b>Created:</b> '+projectData.date+'<br>';
				str+= '<b>Language:</b> '+projectData.lang+' &nbsp; &nbsp; &nbsp; &nbsp; ';
				str+= '<b>DBMS:</b> '+projectData.dbms+'<br>';
				str+= '<b>Owner:</b> '+projectData.owner+' ('+projectData.email+')<br>';
				str+= '<hr/>';
				str+= '<b>Description:</b> '+projectData.description+'<br>';
				Mind.Dialog.ShowData(str,"Properties", 400, 300);
			});
	},
	Save: function(callBack){
		if(!this.changed)
			return false;
		Mind.Utils.SetLoad(true, 'Saving Project...');
		this.attributes.wml= document.getElementById('mindEditor').value;
		var post= JSON.stringify(Mind.Project.attributes);
		$.ajax({
			url: Mind.Properties.path+'/save_project.php',
			type: 'POST',
			data: 'json='+post,
			success: function(ret){
				if(ret == '1')
				{
					Mind.Project.changed= false;
					Mind.Project.oldCode= document.getElementById('mindEditor').value;
					document.getElementById('mindEditorNotSavedMark').style.visibility= 'hidden';
					if(Mind.Project.waitingToRun)
					{
						Mind.Project.waitingToRun= false;
						Mind.Project.Run();
					}
					if(Mind.Project.waitingToExport)
					{
						Mind.Project.waitingToExport= false;
						Mind.Project.Export();
					}
				}else{
						ret= JSON.parse(ret);
						Mind.Dialog.ShowError(ret);
						Mind.Utils.SetLoad(false);
					 }
				Mind.Utils.SetLoad(false);
				if(callBack)
					(callBack)();
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				Mind.AjaxHandler.Capture(XMLHttpRequest);
			}
		});
	},
	Change: function(){
		if(this.oldCode != document.getElementById('mindEditor').value)
		{
			document.getElementById('mindEditorNotSavedMark').style.visibility= 'visible';
			this.changed= true;
		}else{
				if(this.changed)
				{
					document.getElementById('mindEditorNotSavedMark').style.visibility= 'hidden';
				}
			 }
	},
	DebugMessage:function(status, message){
		switch(status)
		{
			case 1:
			{
				document.getElementById("outputPanel_DebugTab").innerHTML += "<div class='debugger-ok'>"+message+"</div>";
				break;
			}
			case 2:
			{
				document.getElementById("outputPanel_DebugTab").innerHTML += "<div class='debugger-ok-tips'>"+message+"</div>";
				break;
			}
			case 3:
			{
				document.getElementById("outputPanel_DebugTab").innerHTML += "<div class='debugger-ok-alerts'>"+message+"</div>";
				break;
			}
			case 4:
			{
				document.getElementById("outputPanel_DebugTab").innerHTML += "<div class='debugger-step'>"+message+"</div>";
				break;
			}
			case 0:
			{
				document.getElementById("outputPanel_DebugTab").innerHTML += "<div class='debugger-fail'>"+message+"</div>";
				break;
			}
			default:{
						document.getElementById("outputPanel_DebugTab").innerHTML += "<div class='debugger-ok-alerts'>Undefined message ("+status+': '+message+")! </div>";
					}
		}
	},
	Run: function(){
		document.getElementById('outputPanel_DDTab').innerHTML= 'Not loaded';
		document.getElementById('outputPanel_DDLTab').innerHTML= 'Not loaded';
		$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('select', 4);
		if(this.changed)
		{
			if(confirm('The project is not saved, you  must save it before running it.\nSave and continue?'))
			{
				Mind.Project.waitingToRun= true;
				Mind.Project.Save();
				return false;
			}else{
					return false;
				 }
		}
		Mind.Utils.SetLoad(true, 'Processing ...');
		var post= JSON.stringify(Mind.Project.attributes);
		document.getElementById("outputPanel_DebugTab").innerHTML= "Loading/Processing...";
		$.ajax({
			url: Mind.Properties.path+'/run_project.php',
			type: 'POST',
			data: 'json='+post,
			success: function(ret){
				ret= JSON.parse(ret);
				Mind.Project.knowledge= ret;
				Mind.Project.MountERDiagramEngine(ret);
				Mind.Project.MountTree(ret);
				document.getElementById("outputPanel_DebugTab").innerHTML= "";
				
				for(var i=0, j=ret.debug.messages.length; i<j; i++)
					Mind.Project.DebugMessage(ret.debug.messages[i][0], ret.debug.messages[i][1]);
				
				Mind.Utils.SetLoad(false);
				$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('enable', 0).tabs('enable', 1).tabs('enable', 2).tabs('enable', 3).tabs('enable', 4).tabs('enable', 5);
				// call all plugins of onRUN event
				Mind.Plugins.OnRunning();
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				Mind.AjaxHandler.Capture(XMLHttpRequest);
			}
		});
		Mind.Project.CloseBalloons();
	},
	/* output methods */
	MountTree: function(oList){
		Mind.Panel['left'].Update(oList, true);
	},
	WriteDataDictionary: function(pObj){
		var pTitle= '<h2>'+Mind.Project.attributes.name+'</h2>';
		var tbs= pObj.tables;
		var htmlTable= '';
		var htmlTBody= '';
		var tr= '';
		var td= '';
		var ret= document.createElement('DIV');
		ret.innerHTML= pTitle;
		
		var c= 0;
		for(tb in tbs)
		{
			htmlTable= document.createElement('TABLE');
			htmlTable.cellPadding= '2px';
			htmlTable.cellSpacing= '0px';
			htmlTable.style.width= '90%';
			htmlTable.align='center';
			htmlTable.style.marginBottom= '10px';
			htmlTable.style.borderBottom= 'solid 1px #666';
			htmlTBody= document.createElement('TBODY');
			tr= document.createElement('TR');
			htmlTable.appendChild(htmlTBody);
			
			td= document.createElement('TD');
			td.colSpan= 5;
			td.innerHTML= '<span class="ui-corner-top ui-tabs-selected ui-state-active">'+tb+'</span>';
			td.className= 'dd_tableName';
			tr.appendChild(td);
			htmlTBody.appendChild(tr);
			htmlTBody.innerHTML+= "<tr><td class='dd_columnName' style='width:270px;'>Attribute</td><td class='dd_columnName' style='width:125px;'>Type</td><td class='dd_columnName' style='width:75px;'>Not Null</td><td class='dd_columnName'>Default Value</td><td class='dd_columnName' style='border-right:none;width:200px;'>Refferences</td></tr>";
			c=0;
			for(att in tbs[tb].attributes)
			{
				c++;
				tr= document.createElement('TR');
				// attribute name
					td= document.createElement('TD');
					if(c==1)
					{
						td.style.backgroundImage= 'url('+Mind.Properties.imagesPath+'/key.gif)';
						td.style.backgroundRepeat= 'no-repeat';
						td.style.backgroundPosition= '3px';
					}
					td.innerHTML= att;
					td.className= 'dd_attName';
					tr.appendChild(td);
				// attribute type (size)
					td= document.createElement('TD');
					td.innerHTML= tbs[tb].attributes[att].type;
					td.className= 'dd_attType';
					if(tbs[tb].attributes[att].size != 0)
						td.innerHTML+= ' ('+tbs[tb].attributes[att].size+')';
					tr.appendChild(td);
				// attribute NOT NULL
					td= document.createElement('TD');
					str= "<img src='"+Mind.Properties.imagesPath+'/'+((tbs[tb].attributes[att].required==1)? 'checked.png':'unchecked.gif')+"' >";
					td.innerHTML= str;
					td.className= 'dd_attNotNull';
					tr.appendChild(td);
				// attribute default value
					td= document.createElement('TD');
					td.innerHTML= tbs[tb].attributes[att].defaultValue;
					td.innerHTML= td.innerHTML.replace(/</g, '&lt;');
					if(td.innerHTML.replace(/ /g, '') == '')
						td.innerHTML= '<br/>';
					td.className= 'dd_attDefaultValue';
					tr.appendChild(td);
				// references
					td= document.createElement('TD');
					td.innerHTML= '&nbsp;';
					td.className= 'dd_attReferences';
					tr.appendChild(td);
				htmlTBody.appendChild(tr);
				
				var tmpStr= '';
				tmpStr+= '\n - '+tbs[tb].attributes[att].mask;
				tmpStr+= '\n - '+tbs[tb].attributes[att].options;
			}
			for(fk in tbs[tb].foreignKeys)
			{
				tr= document.createElement('TR');
				// attribute name
					td= document.createElement('TD');
					td.innerHTML= tbs[tb].foreignKeys[fk][0];
					if(tbs[tb].foreignKeys[fk][1] == tb)
						td.style.backgroundImage= 'url('+Mind.Properties.imagesPath+'/self_referenced.gif)';
					else
						td.style.backgroundImage= 'url('+Mind.Properties.imagesPath+'/fkey.gif)';
					td.style.backgroundRepeat= 'no-repeat';
					td.style.backgroundPosition= '3px';
					td.className= 'dd_attName';
					td.colSpan= '4';
					tr.appendChild(td);
				// references
					td= document.createElement('TD');
					td.innerHTML= tbs[tb].foreignKeys[fk][1];
					td.className= 'dd_attReferences';
					tr.appendChild(td);
				htmlTBody.appendChild(tr);
			}
			ret.appendChild(htmlTable);
		}
		return ret;
	},
	DDLSearch: function(){
		var str= document.getElementById('DDL_search').value;
		if(str.replace(/ /g, '') == '')
			return false;
		var lines= $('#ddl_code_list li');
		var txt= '';
		var rx= new RegExp(str, "gi");
		for(var i=0, j= lines.length-10; i<j; i++)
		{
			if(!lines[i].originalContent)
				lines[i].originalContent= lines[i].innerHTML;
			
			txt= $(lines[i]).text().replace(rx, "<span class='ddl_find'>"+str.toUpperCase()+"</span>");
			$(lines[i]).html("<span style='color:#d0d0d0;'>"+txt+"</span>");
		}
	},
	DDLClearSearch: function(){
		var lines= $('#ddl_code_list li');
		for(var i=0, j= lines.length-10; i<j; i++)
		{
			if(lines[i].originalContent)
			{
				lines[i].innerHTML= lines[i].originalContent;
				lines[i].originalContent= false;
			}
		}
	},
	DDLSave: function(){
		var code= $('#ddl_code_list').text();
		var src= Mind.Properties.path+'/export.php?pName='+Mind.Project.attributes.name+'&onlyDDL=1';
		document.getElementById('hiddenFrame').src= src;
	},
	MountDDLViewer: function(ob){
		var str= '<div style="height:100%;overflow:auto;" autoresize="34"><ol type="1" id="ddl_code_list" class="ddl_code_list">';
		var lines= ob.DDL.split('\n');
		
		for(var i=0; i<lines.length; i++)
		{
			str+= '<li onmouseover="this.style.backgroundColor= \'#ffb\';" onmouseout="this.style.backgroundColor= \'#ffffff\';">'+lines[i]+'</li>';
		}
		for(i= 0; i<10; i++)
		{
			str+= '<li onmouseover="this.style.backgroundColor= \'#ffb\';" onmouseout="this.style.backgroundColor= \'#ffffff\';"><br/></li>';
		}
		str+= '</ul>';
		str+= '</div>';
		str+= "<div class='ui-state-default ui-corner-all DDL_ToolBar'>";
		str+= '&nbsp;Search <input id="DDL_search" type="text" onkeypress="if(event.keyCode == 13) Mind.Project.DDLSearch();"/><img src="'+Mind.Properties.imagesPath+'/search.png" onclick="Mind.Project.DDLSearch()" title="Search"/>';
		str+= "<img src='"+Mind.Properties.imagesPath+"/visto.png' onclick='Mind.Project.DDLClearSearch();' title='Clear search'/>";
		//str+= "&nbsp;&nbsp;<img id='imageDDL_ClipBoard' src='"+Mind.Properties.imagesPath+"/page_white_copy.png' alt='copy to clipboard' title='Copy to Clipboard' />";
		str+= "&nbsp;&nbsp;<img src='"+Mind.Properties.imagesPath+"/save.jpg' width='16' alt='Download DDL code (.sql)' title='Download DDL code (.sql)' onclick='Mind.Project.DDLSave()'/>";
		str+= '</div>';
		return str;
	},
				MountDDLViewerPlugin: function(ob){
					
					var code= document.getElementById('ddl_code_list');
					
					//Mind.Project.clip = new ZeroClipboard.Client();
					//Mind.Project.clip.moviePath= Mind.Properties.scriptsPath+'/ZeroClipboard/ZeroClipboard.swf';
					
					//Mind.Project.clip.glue(document.getElementById("imageDDL_ClipBoard"));
					//Mind.Project.clip.setText(Mind.Utils.StripTags(Mind.Project.knowledge.DDL)); // AQUI
					//alert(Mind.Properties.scriptsPath);
					/*Mind.Project.clip.addEventListener('complete', function(client, text){
						document.getElementById('DDL_search').focus();
						Mind.Dialog.ShowMessage('Copied to clipboard');
					});*/
					
					var newCode= code.innerHTML;
					var exp= '';
					for(tb in ob.tables)
					{
						exp= new RegExp('<span class="ddl_code_objTable">'+tb+'</span>', 'i');
						newCode= newCode.replace(exp, "<a class='ddl_code_objTable ddl_code_objTableItem' onmouseout='Mind.ToolTip.Hide();' onclick='Mind.ERD.ShowTableDDL(this.getAttribute(\"name\"));' onmousemove='Mind.ToolTip.Show(event, Mind.Project.DDLTableInfo(this))' name='ddl_table_"+tb+"'>"+tb+"</a>");
						exp= new RegExp('<span class="ddl_code_objTable">'+tb+'</span>', 'ig');
						newCode= newCode.replace(exp, "<a class='ddl_code_objTable' href='#ddl_table_"+tb+"'>"+tb+"</a>");
					}
					$('#ddl_code_list li').bind('mouseover', function(){this.css('backgroundColor', 'red')});
					code.innerHTML= newCode;
				},
				DDLTableInfo: function(obj){
					if(typeof obj != 'string')
						obj= obj.name.replace('ddl_table_', '');
					obj= Mind.Project.knowledge.tables[obj];
					var str='<b><i>'+obj.name+'</i></b><br>';
					//alert(obj.refered);
					str+= "<table class='toolTipTable' align='left'><tr><td colspan='3' style='border-bottom:solid 1px #333;'><b><center>Attributes</center></b></td></tr>";
					for(var att in obj.attributes)
					{
						str+= "<tr><td><b>"+obj.attributes[att].name+'</b></td><td>'+obj.attributes[att].type+ '</td><td>'+((obj.attributes[att].comment)? ': <i>'+obj.attributes[att].comment+'</i>': ': --')+'</td></tr>';
					}
					str+= "</table>";
					
					str+= "<table class='toolTipTable' align='left'><tr><td colspan='3' style='border-bottom:solid 1px #333;'><b><center>References</center></b></td></tr>";
					for(var fk in obj.foreignKeys)
					{
						str+= "<tr><td>"+obj.foreignKeys[fk][0]+': </td><td colspan="2">'+obj.foreignKeys[fk][1]+ '</td></tr>';
					}
					str+= "</table>";
					
					str+= "<table class='toolTipTable' style='border-right: none;'><tr><td style='border-bottom:solid 1px #333;'><b><center>Refered By</center></b></td></tr>";
					for(var ref in obj.refered)
					{
						ref= obj.refered[ref].split('|')[1];
						str+= "<tr><td>"+ref+'</td></tr>';
					}
					str+= "</table>";
					return str;
				},
	Export: function(){
		if(this.changed)
		{
			if(confirm('The project is not saved, you  must save it before exporting it.\nSave and continue?'))
			{
				Mind.Project.waitingToExport= true;
				Mind.Project.Save();
				return false;
			}else{
					return false;
				 }
		}
		var src= Mind.Properties.path+'/export.php?pName='+Mind.Project.attributes.name;
		document.getElementById('hiddenFrame').src= src;
	},
	MountERDiagramEngine:function(obj){
		var listPane= document.getElementById('outputPanel_der_listContainer');
		listPane.innerHTML= '';
		listPane.setAttribute('autoresize', '24');
		var tmp= false;
		for(table in obj.tables)
		{
			tmp= document.createElement('div');
			tmp.className= 'der_list_table';
			tmp.setAttribute('tableName', table);
			tmp.innerHTML= table;
			listPane.appendChild(tmp);
		}
		$('#outputPanel_der_listContainer .der_list_table').draggable({
																		revert: 'invalid',
																		opacity: 0.9,
																		helper: 'clone',
																		containment:Mind.Panel['bottom'].htmlElement,
																		scroll:false,
																		revertDuration: 500,
																		zIndex: 99999
																	  });
		$("#outputPanel_der_body").droppable({
			accept: '.der_list_table',
			drop: function(event, ui){
				var tb= Mind.Project.knowledge.tables[ui.draggable[0].getAttribute('tableName')];
				Mind.ERD.addTable(event, tb);
			}
		});
		Mind.ERD.Init('outputPanel_der_body');
	},
	ToogleSavedERDiagram: function(){
										var l= document.getElementById('DDRLoadList');
										if(l.parentNode.tagName != 'BODY')
										{
											document.body.appendChild(l);
											l.style.zIndex= '999999';
										}
										if(l.style.display=='')
										{
											l.innerHTML= 'Loading...';
											l.style.display= 'none';
										}else{
												l.style.display= '';
												Mind.ERD.ListSavedDERs(function(data){
													l.innerHTML= '';
													for(var i in data)
														l.innerHTML+= "<div class='savedERDItemContainer' onmouseover='this.className=\"savedERDItemContainer_over\";' onmouseout='this.className=\"savedERDItemContainer\";'><div class='savedERDItem' onclick='Mind.ERD.LoadSavedDiagram(\""+data[i]+"\");'>"+data[i]+'</div><img src="'+Mind.Properties.imagesPath+'/del.gif" onclick="Mind.ERD.RemoveSavedDiagram(\''+data[i]+'\')" /></div>';
													if(l.innerHTML=='')
														l.innerHTML= 'No saved data to list';
												});
											 }
										
										/* $(document).bind('click', function(){
											$(document).unbind('click', Mind.Project.ToogleSavedERDiagram);
										}); */
									},
	Generate: function(){
		Mind.Dialog.OpenModal(true,
							  '800',
							  '520',
							  'Generate Project ',
							  'midle',
							  'generate_project.php?projectName='+ Mind.Project.attributes.name,
							  'form',
							  function(){},
							  false,
							  true);
	},
	GetModule: function(m, o){
		document.getElementById('mind_module_content').innerHTML= 'Loading...';
		Mind.Component.Load(
								{componentName: "getModule", data: {moduleName:m}},
								function(comps){
									comps= (Mind.Component.Parse(comps))['getModule'];
									var str= '<table>';
									for(x in comps)
									{
										switch(x)
										{
											case 'details':
												str+= '<tr>';
												str+= '<td style="vertical-align:top">details: </td>';
												str+= '<td>';
												for(i in comps[x])
													str+= comps[x][i].name+'-> '+comps[x][i].value+'<br/>';
												str+= '</td></tr>';
											break;
											case 'authors':
												str+= '<tr>';
												str+= '<td style="vertical-align:top">authors: </td>';
												str+= '<td>';
												for(i in comps[x])
													str+= "<a href='mailto:"+comps[x][i].email+"' title='"+comps[x][i].email+"'>"+comps[x][i].name+"</a><br/>";
												str+= '</td></tr>';
											break;
											case 'license':
												str+= '<tr>';
												str+= '<td style="vertical-align:top">license: </td>';
												str+= '<td>';
												str+= "<a href='"+comps[x]+"' target='_quot' title='"+comps[x]+"'>"+comps[x]+"</a><br/>";
												str+= '</td></tr>';
											break;
											case 'thumb':
												str+= '<tr>';
												str+= '<td style="vertical-align:top">thumb: </td>';
												str+= '<td>';
												str+= "<a href='"+comps[x]+"' target='_quot'><img src='"+comps[x]+"'></a>";
												str+= '</td></tr>';
											break;
											case 'configPage':
												Mind.Project.tmp['configPage']= comps[x];
											break;
											case 'dependences':
												Mind.Project.tmp['dependences']= comps[x];
											break;
											default:
												str+= '<tr><td style="vertical-align:top;">'+x+':</td><td>'+comps[x]+'</td></tr>';
										}
									}
									str+= '</table>';
									Mind.Project.tmp['selectedModule']= m;
									document.getElementById('mind_module_content').innerHTML= str;
									try
									{
										enableNext();
									}catch(error){}
								}
							);
		if(o)
		{
			o.getElementsByTagName('INPUT')[0].click();
		}
	},
	AbortProcessing: function(){
		document.getElementById('generatingAllStatus').src= Mind.Properties.path+'/cancel.php';
	},
	Commit: function(){
		Mind.Dialog.OpenModal(true,
							  '600',
							  '435',
							  'Commit Project ',
							  'midle',
							  'commit.php',
							  'form',
							  function(){},
							  false,
							  true);
	},
	ConfirmCommit: function(){
		Mind.Dialog.ShowMessage('Commiting');
		$.ajax({
			url: Mind.Properties.path+'/commit.php',
			type: 'POST',
			data: 'confirmCommit=true',
			success: function(ret){
				Mind.Dialog.CloseMessage();
				Mind.Dialog.ShowMessage('Done');
				setTimeout(Mind.Dialog.CloseMessage, 4000);
				Mind.Project.Update();
			}
		});
	},
	Update: function(){
		Mind.Dialog.OpenModal(true,
							  '600',
							  '435',
							  'Update Project ',
							  'midle',
							  'update.php',
							  'form',
							  function(){},
							  false,
							  true);
	},
	ConfirmUpdate: function(){
		document.getElementById('mindEditor').value= document.getElementById('newUpToDateCode').innerHTML;
		var vs= document.getElementById('newUpToDateVersion').innerHTML.split('.');
		Mind.Project.attributes.version= vs;
		Mind.Project.changed= true;
		Mind.Project.Save(function(){
			var tmp= Mind.Project.attributes.name;
			Mind.Project.Close();
			Mind.Project.Load(tmp, function(){
				setTimeout(function(){Mind.Project.Update();}, 1000);
			});
		});
	},
	SeeCurrentUserFiles: function(flag){
		var url= 'address_here.php?p='+Mind.Project.attributes.name;
		if(flag)
			url+= '&projectdirtoshow=true';
		Mind.Dialog.OpenModal(true,
							  '600',
							  '435',
							  'See Project Files',
							  'midle',
							  url,
							  true,
							  function(){});
	},
	SeeCurrentProjectFiles: function(){
		this.SeeCurrentUserFiles(true);
	},
	CloseBalloons: function(){
		document.getElementById('debugBalloon').style.display= 'none';
	},
	AddTypeSave: function(newType){
		var type= document.getElementById('addTypeSel').value;
		Mind.Utils.SetLoad(true, 'Saving type');
		Mind.Project.CloseBalloons();
		$.ajax({
					url: Mind.Properties.path+'/add_to_dictionary.php',
					type: 'POST',
					data: 'newType='+ newType+'&type='+document.getElementById('addTypeSel').value,
					success: function(ret){
						Mind.Project.Run();
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						Mind.AjaxHandler.Capture(XMLHttpRequest);
					}
			   });
	},
	AddType: function(event, o, options){
		var balloon= document.getElementById('debugBalloon');
		var content= balloon.getElementsByTagName('DIV')[0];
		content.innerHTML = "<nobr>Choose the type this word represents<nobr/><br/>";
		var opts= "";
		for(var i=0, j=options.length; i<j; i++)
		{
			opts+= "<option value='"+options[i]+"'>"+options[i]+"</option>";
		}
		content.innerHTML+= "<div style='text-align:right;'><select id='addTypeSel'>"+opts+"</select> <img src='"+Mind.Properties.imagesPath+"/bt_play_over.gif' onclick='Mind.Project.AddTypeSave(\""+o+"\")' align='right'></div>";
		balloon.style.display= '';
		balloon.style.left= event.clientX - balloon.offsetWidth;
		balloon.style.top= event.clientY - balloon.offsetHeight;
	},
	AddVerbOnkeyUp: function (event, o){
		if(event.keyCode==13 && o.value.replace(/ /g, '') != '')
		{
			Mind.Project.CloseBalloons();
			Mind.Utils.SetLoad(true, 'Adding verb');
			$.ajax({
					url: Mind.Properties.path+'/add_to_dictionary.php',
					type: 'POST',
					data: 'newVerb='+ o.value+'&verbType='+document.getElementById('addVerbSel').value,
					success: function(ret){
						Mind.Project.Run();
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						Mind.AjaxHandler.Capture(XMLHttpRequest);
					}
				   });
;		}
	},
	AddVerb: function(event, o){
		var balloon= document.getElementById('debugBalloon');
		var content= balloon.getElementsByTagName('DIV')[0];
		content.innerHTML = "<nobr>Type the verb and press enter<nobr/><br/>";
		content.innerHTML+= "<select id='addVerbSel'><option value='p'>Possessive/Action</option><option value='m'>Mandatory</option><option value='px'>Possibility</option></select>";
		content.innerHTML+= "<input id='addVerbIpt' type='text' class='iptText' style='width:100%; font-size:12px;' onkeyup='Mind.Project.AddVerbOnkeyUp(event, this);'/>";
		balloon.style.display= '';
		balloon.style.left= event.clientX - balloon.offsetWidth;
		balloon.style.top= event.clientY - balloon.offsetHeight;
		document.getElementById('addVerbIpt').select();
	}
};