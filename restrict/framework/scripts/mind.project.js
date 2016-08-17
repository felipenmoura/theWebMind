Mind.Project= {
	changed:false,
	attributes: null,
	waitingToRun:false,
	waitingToExport:false,
	oldCode:'',
	knowledge: false,
	Load: function(projectToLoad)
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
								{componentName: "mindApplicationList", data: {name:projectToLoad}},
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
											str+= "<li><a href='#outputPanel_infoConfTab'>About</a></li>";
											str+= "<li><a href='#outputPanel_DDLTab'>DDL</a></li>";
											str+= "<li><a href='#outputPanel_ERDTab'><nobr>ER Diagram</nobr></a></li>";
											str+= "<li><a href='#outputPanel_DDTab'><nobr>Data Dictionary</nobr></a></li>";
											str+= "<li><a href='#outputPanel_DebugTab'>Debug</a></li>";
										str+= "</ul>";
										str+= "<div style='height:100%;overflow:auto;background-color:#f5f5f5;' autoresize='true'>";
										str+= "<div style='position:absolute;width:24px;height:20px;top:10px;right:4px'>";
										str+= "<img src='images/bt_full_editor_over.png' onclick='Mind.Panels.SetFull();' onmouseover='this.src=\"images/bt_full_close_over.png\"' onmouseout='this.src=\"images/bt_full_editor_over.png\"'>";
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
											str+= "<div id='outputPanel_ERDTab'><table cellspacing='0' cellpadding='0' style='width:100%;height:100%;'><tr><td id='outputPanel_der_list'><br/></td><td id='outputPanel_der_body'></td></tr></table>";
											str+= "</div>";
											str+= "<div id='outputPanel_DDTab'>Not loaded";
											str+= "</div>";
											str+= "<div id='outputPanel_DebugTab'>Not loaded";
											str+= "</div>";
										str+= "</div>";
									str+= "</div>";
									
									Mind.Panel['bottom'].Update(str);
									$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs().find('.ui-tabs-nav').sortable({axis:'x'});
									$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('select', 0);
									if(projectData.processed == 'false')
									{
										$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 1);
										$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 2);
										$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 3);
										$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 4);
										$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 5);
									}
									Mind.Project.ActiveMenus();
									Mind.Panel['bottom'].Adjust();
									/*********************************  RIGHT PANEL  ************************************/
									var mindApplicationList= comps.mindApplicationList;
									Mind.Panel['right'].Update(mindApplicationList);
									/*
										rodar aqui, os plugins que devem rodar ao abrir um projeto
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
									//$.combobox.apply();
								}
							   );
							   //setTimeout(function(){Mind.MindEditor.SetUserConfig()},500);
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
							str+= "<div id='outputPanel_DDLTab'><br>";
							str+= "</div>";
							str+= "<div id='outputPanel_ERDTab'><br>";
							str+= "</div>";
							str+= "<div id='outputPanel_DDTab'><br>";
							str+= "</div>";
							str+= "<div id='outputPanel_DebugTab'><br>";
							str+= "</div>";
						str+= "</div>";
					str+= "</div>";
					Mind.Panel['bottom'].Update(str);
					$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs();
					$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('select', 0);
					if(!projectData.processed)
					{
						$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 0);
						$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 1);
						$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 2);
						$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 3);
						$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 4);
						$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('disable', 5);
					}
					Mind.Panel['right'].Update();
					Mind.Project.DeactiveMenus();
					this.attributes= null;
					apagaCookie('theWebMind_currentProject');
					Mind.Progress.Increment(20,"loadProject");
			 }		
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
	Save: function(){
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
	Run: function(){
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
		$.ajax({
			url: Mind.Properties.path+'/run_project.php',
			type: 'POST',
			data: 'json='+post,
			success: function(ret){
				Mind.Panel['right'].Update('<pre>'+ret);
				ret= JSON.parse(ret);
				//Mind.Dialog.ShowData(str, "Properties", 400, 300);
				$('#outputPanel_DDTab').html(Mind.Project.WriteDataDictionary(ret).innerHTML);
				$('#outputPanel_DebugTab').html(Mind.Project.MountDebugger(ret));
				$('#outputPanel_DDLTab').html(Mind.Project.MountDDLViewer(ret));
				// call all plugins of onRUN
				Mind.Project.knowledge= ret;
				Mind.Project.MountDDLViewerPlugin(ret);
				Mind.Project.MountERDiagramEngine(ret);
				
				Mind.Utils.SetLoad(false);
				$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('enable', 1);
				$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('enable', 2);
				$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('enable', 3);
				$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('enable', 4);
				$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('enable', 5);
			}
		});
	},
	/* output methods */
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
				
				//alert(tbs[tb].attributes[att].name+'\n'+tmpStr);
			}
			for(fk in tbs[tb].foreignKeys)
			{
				tr= document.createElement('TR');
				// attribute name
					td= document.createElement('TD');
					td.innerHTML= tbs[tb].foreignKeys[fk][0];
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
			/*
			ret+= tb+'<br>';
			for(att in tbs[tb].attributes)
				ret+= '-- '+att+' - '+tbs[tb].attributes[att].type+'('+tbs[tb].attributes[att].size+')'+'<br>';
			ret+= '<br>';
			*/
			ret.appendChild(htmlTable);
		}
		return ret;
	},
	MountDebugger: function(){
		/*var htmlElement= document.createElement('div');
		htmlElement.style.height= '100%';
		htmlElement.style.width= '100%';
		htmlElement.appendChild(document.getElementById('debuggerEstructure'));
		
		var x= document.getElementById('debuggerEstructureCodes');
		codes= Array(x.getElementsByTagName('textarea')[0], x.getElementsByTagName('textarea')[1]);
		//alert(codes);
		
		return htmlElement.innerHTML;*/
		return '';
	},
	MountDDLViewer: function(ob){
		var str= '<ol type="1" id="ddl_code_list" class="ddl_code_list">';
		var linhas= ob.DDL.split('\n');
		for(var i=0; i<linhas.length; i++)
		{
			str+= '<li>'+linhas[i]+'</li>';
		}
		for(i= 0; i<10; i++)
		{
			str+= '<li><br/></li>';
		}
		str+= '</ul>';
		return str;
	},
				MountDDLViewerPlugin: function(ob){
					var code= document.getElementById('ddl_code_list');
					var newCode= code.innerHTML;
					var exp= '';
					for(tb in ob.tables)
					{
						//newCode= newCode.replace(exp, "<a class='ddl_code_objTable' href='http://www.google.com'>"+tb+"</a>");
						exp= new RegExp('<span class="ddl_code_objTable">'+tb+'</span>', 'i');
						newCode= newCode.replace(exp, "<a class='ddl_code_objTable ddl_code_objTableItem' onmouseout='Mind.ToolTip.Hide();' onmouseover='Mind.ToolTip.Show(event, Mind.Project.DDLTableInfo(this))' name='ddl_table_"+tb+"'>"+tb+"</a>");
						exp= new RegExp('<span class="ddl_code_objTable">'+tb+'</span>', 'ig');
						newCode= newCode.replace(exp, "<a class='ddl_code_objTable' href='#ddl_table_"+tb+"'>"+tb+"</a>");
					}
					code.innerHTML= newCode;
					/*var tmpTables= document.getElementById('ddl_code_list').getElementsByTagName('SPAN');
					var tables= Array();
					for(var i=0; i< tmpTables.length; i++)
					{
						if(tmpTables[i].className== 'ddl_code_objTable')
							tables.push(tmpTables[i]);
					}
					delete tmpTables;
					alert(tables.length)*/
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
		//document.getElementById('hiddenFrame').src= Mind.Properties.components;
		var src= Mind.Properties.path+'/export.php?pName='+Mind.Project.attributes.name;
		//window.open(src);
		document.getElementById('hiddenFrame').src= src;
	},
	MountERDiagramEngine:function(obj){
		//outputPanel_der_list
		//outputPanel_der_body
		var listPane= document.getElementById('outputPanel_der_list');
		listPane.innerHTML= '';
		var tmp= false;
		for(table in obj.tables)
		{
			tmp= document.createElement('div');
			tmp.className= 'der_list_table';
			tmp.setAttribute('tableName', table);
			tmp.innerHTML= table;
			listPane.appendChild(tmp);
		}
		$('#outputPanel_der_list .der_list_table').draggable({  revert: 'invalid',
																opacity: 0.9,
																helper: 'clone',
																revertDuration: 500,
																zIndex: 9999});
		$("#outputPanel_der_body").droppable({
			accept: '.der_list_table',
			drop: function(event, ui){
				var tb= Mind.Project.knowledge.tables[ui.draggable[0].getAttribute('tableName')];
				Mind.ERD.addTable(event, tb);
			}
		});
		Mind.ERD.Init('outputPanel_der_body');
		/*
		var options= $('#outputPanel_der_list .der_list_table');
		options.draggable({ revert: 'invalid', opacity: 0.7, helper: 'clone' });
		$("#droppable").droppable({
			drop: function(event, ui){
					   //$(this).add()
			}
		});
		*/
	}
};




