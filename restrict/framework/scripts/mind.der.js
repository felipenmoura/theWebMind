Mind.ERD= {
	mapArea: null,
	shownTables: Array(),
	relations: Array(),
	randomColors: Array('f00', '0f0', '00f', 'f99', '9f9', '99f', '900', '090', '009', '606', '660', '066', '666'),
	ZoomAt: 0,
	Init: function(mA){
		Mind.ERD.generalization= false;
		Mind.ERD.generalization= Array();
		var tmp= document.getElementById(mA);
		Mind.ERD.shownTables= Array();
		Mind.ERD.relations= Array();
		Mind.ERD.mapArea= document.createElement('DIV');
		tmp.innerHTML= '';
		tmp.appendChild(Mind.ERD.mapArea);
		Mind.ERD.mapAreaContainer= document.createElement('DIV')
		Mind.ERD.mapAreaContainer.setAttribute('id', 'mapAreaContainer');
		$(Mind.ERD.mapAreaContainer).bind('mousedown', function(){this.style.cursor= 'move';});
		$(Mind.ERD.mapAreaContainer).draggable({});
		$(Mind.ERD.mapAreaContainer).bind('mouseup', function(){this.style.cursor= 'default';});
		$(Mind.ERD.mapAreaContainer).bind('mousedown', function(){
			Mind.ERD.currentScale= 	Mind.ERD.mapAreaContainer.getAttribute('currentScale');
		});
		
		Mind.ERD.mapAreaContainer.style.position= 'absolute';
		Mind.ERD.mapAreaContainer.style.top= -4555;
		Mind.ERD.mapAreaContainer.style.left= -4555;
		Mind.ERD.mapAreaContainer.style.width= 9999;
		Mind.ERD.mapAreaContainer.style.height= 9999;
		Mind.ERD.mapArea.innerHTML= '';
		Mind.ERD.mapAreaContainer.innerHTML= '';
		Mind.ERD.mapArea.setAttribute('autoresize', 'true');
		Mind.ERD.mapArea.style.position= 'relative';
		Mind.ERD.mapArea.style.zIndex= '0';
		Mind.ERD.mapArea.style.height= '100%';
		Mind.ERD.mapArea.style.width= '100%';
		Mind.ERD.mapArea.style.overFlow= 'hidden';
		Mind.ERD.mapArea.appendChild(Mind.ERD.mapAreaContainer);
		
		Mind.ERD.mapArea.style.overflow= 'hidden';
		Mind.ERD.mapAreaContainer.style.backgroundImage= 'url('+Mind.Properties.imagesPath+'/back_der.gif)';
		Mind.ERD.mapArea.style.backgroundColor= '#a0a0a0';
		Mind.Panel["bottom"].Adjust();
		document.getElementById('erdToolBar').style.display= 'none';
	},
	Print: function(){
		var p= window.open('printer.php?id=mapAreaContainer');
	},
	ShowTableDDL: function(obj){
		var rand= Math.floor(Math.random() * 100);
		if(typeof obj != 'string')
			obj= obj.name.replace('ddl_table_', '');
		else
			obj= obj.replace('ddl_table_', '');
		obj= Mind.Project.knowledge.tables[obj];
		
		/* if(document.getElementById('tmpSQLTabContainer_'+obj.name))
		{
			return;
		} */
		$('#tmpSQLTabContainer_'+obj.name).remove();
		var str= "<div class='tmpSQLTabContainer' id='tmpSQLTabContainer_"+obj.name+rand+"'><ul><li><a href='#tmpSQLTabContainer_create_"+obj.name+rand+"'>Create</a></li><li><a href='#tmpSQLTabContainer_insert_"+obj.name+rand+"'>Insert</a></li><li><a href='#tmpSQLTabContainer_select_"+obj.name+rand+"'>Select</a></li><li><a href='#tmpSQLTabContainer_update_"+obj.name+rand+"'>Update</a></li><li><a href='#tmpSQLTabContainer_delete_"+obj.name+rand+"'>Delete</a></li></ul>";
		var qrI= "INSERT into "+obj.name+"\n(\n";
		var qrS= "SELECT ";
		var qrU= "UPDATE "+obj.name+ '\n   SET ';
		var qrD= "DELETE from "+obj.name+"\n WHERE ";
		var qrC= obj.DDL;
		var first= true;
		var c= '';
		var t= false;
		var v= '';
		var pk ='';
			for(var att in obj.attributes)
			{
				// insert
				qrI+= ((first)? '':',\n')+'    ' + att;
				t= obj.attributes[att].type
				c= (t=='char' || t=='varchar' || t=='text')? "'":'';
				v+= (first? '':',\n')+ "    "+c+'?'+c;
				// select
				qrS+= (first? '':',\n       ')+att;
				// delete
				if(first)
					qrD+= att+'= ?';
				// update
				qrU+= (first? '':',\n       ')+att+'= '+c+'?'+c;
				
				if(first)
					pk= att;
				first= false;
			}
			// insert
			qrI+= '\n)\nVALUES\n(\n';
			qrI+= v;
			qrI+= '\n)';
			// select
			qrS+= "\n  FROM "+obj.name;
			// update
			qrU+= "\nWHERE "+pk+'= ?';
		str+= "<div id='tmpSQLTabContainer_create_"+obj.name+rand+"'>";
			str+= "<textarea readonly='readonly'>"+qrC+'</textarea>';
		str+= "</div>";
		str+= "<div id='tmpSQLTabContainer_insert_"+obj.name+rand+"'>";
			str+= "<textarea readonly='readonly'>"+qrI+'</textarea>';
		str+= "</div>";
		str+= "<div id='tmpSQLTabContainer_select_"+obj.name+rand+"'>";
			str+= "<textarea readonly='readonly'>"+qrS+'</textarea>';
		str+= "</div>";
		str+= "<div id='tmpSQLTabContainer_update_"+obj.name+rand+"'>";
			str+= "<textarea readonly='readonly'>"+qrU+'</textarea>';
		str+= "</div>";
		str+= "<div id='tmpSQLTabContainer_delete_"+obj.name+rand+"'>";
			str+= "<textarea readonly='readonly'>"+qrD+'</textarea>';
		str+= "</div>";
		Mind.Dialog.ShowData(str, 'SQL Commands for '+obj.name, 500, 400, true);
		var div= $('#tmpSQLTabContainer_'+obj.name+rand);
		div.tabs();
	},
	addTable: function(event, table, coords){
		if(Mind.ERD.shownTables[table.name])
			return false;
			
		var tb= document.createElement('TABLE');
		tb.className= 'der_table';
		tb.id= 'der_table_id_'+table.name;
		tb.style.border= 'none';
		tb.setAttribute('tableName', table.name);
		tb.cellSpacing= '0';
		tb.cellPadding= '0';
		var tBody= document.createElement('TBODY');
		     tb.appendChild(tBody);
		var tr= document.createElement('TR');
		var td= document.createElement('TD');
		     td.style.textAlign= 'right';
		var img1= document.createElement('IMG');
		var img0= document.createElement('IMG');
		var img2= document.createElement('IMG');
		
		img0.src= Mind.Properties.imagesPath+'/sql.png';
		img1.src= Mind.Properties.imagesPath+'/info.gif';
		img2.src= Mind.Properties.imagesPath+'/del.gif';
		img0.style.position= 'relative';
		img1.style.position= 'relative';
		img2.style.position= 'relative';
		img0.style.right= '3px';
		img0.style.top= '7px';
		img1.style.right= '3px';
		img1.style.top= '7px';
		img2.style.right= '3px';
		img2.style.top= '7px';
		img0.setAttribute('tableName', table.name);
		img1.setAttribute('tableName', table.name);
		img2.setAttribute('tableName', table.name);
		img0.onclick= function(event){
			Mind.ERD.ShowTableDDL(this.getAttribute('tableName'));
		}
		img1.onclick= function(event){
			event= event? event:window.event;
			Mind.ToolTip.Show(event, Mind.Project.DDLTableInfo(this.getAttribute('tableName')))
		}
		img1.onmouseout= function(){
			Mind.ToolTip.Hide();
		}
		img2.style.cursor= 'pointer';
		img2.alt= 'Remove this table from diagram';
		img2.onclick= function(){
			var tb= Mind.Project.knowledge.tables[this.getAttribute('tableName')]
			var tmp;
			for(var x in tb.foreignKeys)
			{
				Mind.ERD.RemoveRelation(this.getAttribute('tableName'), tb.foreignKeys[x][1]);
			}
			for(var x in tb.refered)
			{
				tmp= tb.refered[x].split('|');
				Mind.ERD.RemoveRelation(tmp[0], tmp[1]);
			}
			$('#outputPanel_der_listContainer [tableName='+this.getAttribute('tableName')+']').removeClass('der_list_table_');
			$(Mind.ERD.shownTables[this.getAttribute('tableName')].hiddenTable).remove();
			delete Mind.ERD.shownTables[this.getAttribute('tableName')];
			Mind.ERD.mapAreaContainer.removeChild(document.getElementById('der_table_id_'+this.getAttribute('tableName')));
		}
		
		td.appendChild(img0);
		td.appendChild(img1);
		td.appendChild(img2);
		tBody.appendChild(tr.appendChild(td));
		
		var tr= document.createElement('TR');
		var td= document.createElement('TD');
		td.innerHTML= table.name;
		td.className= 'der_table_title';
		tr.appendChild(td);
		tBody.appendChild(tr);
		
		for(att in table.attributes)
		{
			tr= document.createElement('TR');
			td= document.createElement('TD');
			td.innerHTML= att;
			td.className= 'der_table_att';
			tr.appendChild(td);
			tBody.appendChild(tr);
		}
		if(table.foreignKeys.length>0)
		{
			tr= document.createElement('TR');
			td= document.createElement('TD');
			td.className= 'der_table_separator';
			tr.appendChild(td);
			tBody.appendChild(tr);
			for(fk in table.foreignKeys)
			{
				tr= document.createElement('TR');
				td= document.createElement('TD');
				td.innerHTML= table.foreignKeys[fk][0];
				td.className= 'der_table_att';
				tr.appendChild(td);
				tBody.appendChild(tr);
				Mind.ERD.AddRelation(table, table.foreignKeys[fk][1]);
				if(table.name == table.foreignKeys[fk][1])
					td.innerHTML+= "<img src='"+Mind.Properties.imagesPath+"/self_referenced.gif'>";
			}
		}
		
		Mind.ERD.mapAreaContainer.appendChild(tb);
		if(coords)
		{
			tb.style.left= coords[0];
			tb.style.top= coords[1];
			tb.style.zIndex= coords[2]? coords[2]: 9;
		}else{
				tb.style.left= (event.clientX - document.getElementById('outputPanel_der_list').offsetWidth - tb.offsetWidth/2)+Mind.ERD.mapArea.scrollLeft;
				tb.style.top= (event.clientY - Mind.Panel["bottom"].htmlElement.offsetTop-tb.offsetHeight/2)+Mind.ERD.mapArea.scrollTop - (tb.offsetHeight/2) + 20;
				tb.style.zIndex= 9;
				if(Mind.ERD.mapAreaContainer.offsetLeft < 0)
					tb.style.left= tb.offsetLeft + Mind.ERD.mapAreaContainer.offsetLeft*(-1);
				if(Mind.ERD.mapAreaContainer.offsetTop < 0)
					tb.style.top= tb.offsetTop + Mind.ERD.mapAreaContainer.offsetTop*(-1);
			 }
		
		tb.setAttribute('DERTable', 'true')
		
		$(tb).draggable({stack: { group: '#outputPanel_der_body [DERTable=true]', min: 50 },
						 scroll:true,
						 drag:function(){
							Mind.ERD.ReDraw();
						 },
						 start: function(){
						 	
						 },
						 stop:function(){
							Mind.ERD.ReDraw();
							Mind.ERD.ApplyZoom(Mind.ERD.currentScale, true);
						 },
						 scrollSensitivity: tb.offsetWidth/2
						 });
		
		Mind.ERD.shownTables[table.name]= table;
		Mind.ERD.shownTables[table.name].OO= tb;
		
		/*  */
		
		var tmpTable= document.createElement('TABLE');
		tmpTable.setAttribute('id', 'hiddenTable_'+tb.name);
		
		tmpTable.style.height= tb.offsetHeight - 20;
		var tmpTbody= document.createElement('TBODY');
		var tmpDiv;
		
		for(i= 0; i < table.refered.length; i++)
		{
			tmpLine= document.createElement('TR');
			tmpCol= document.createElement('TD');
			tmpLine.appendChild(tmpCol);
			tmpCol.setAttribute('id', 'hiddenTable_'+table.name+'_'+table.refered[i].split('|')[1]);
			tmpCol.className= 'hiddenERDTable';
			tmpCol.innerHTML= '<!-- -->';
			tmpTbody.appendChild(tmpLine);
		}
		for(i in table.foreignKeys)
		{
			tmpLine= document.createElement('TR');
			tmpCol= document.createElement('TD');
			tmpCol.setAttribute('id', 'hiddenTable_'+table.name+'_'+table.foreignKeys[i][1]);
			tmpLine.appendChild(tmpCol);
			tmpCol.className= 'hiddenERDTable';
			tmpCol.innerHTML= '<!-- -->';
			tmpTbody.appendChild(tmpLine);
		}
		Mind.ERD.mapAreaContainer.appendChild(tmpTable);
		
		tmpTable.appendChild(tmpTbody);
		tmpTable.style.position= 'absolute';
		tmpTable.style.top= tb.offsetTop+20;
		tmpTable.style.left= tb.offsetLeft + tb.offsetWidth/2 - 10;
		tmpTable.style.width= '20px';
		tmpTable.style.zIndex= 0;
		tmpTable.style.backgroundColor= 'transparent';
		
		Mind.ERD.shownTables[table.name].hiddenTable= tmpTable;
		
		if(table.extends)
		{
			Mind.ERD.generalization[table.extends]= table;
		}
		
		Mind.ERD.ReDraw();
		
		$('#outputPanel_der_listContainer [tableName='+table.name+']').addClass('der_list_table_');
	},
	AddRelation: function (tb1, tb2){ // obj and the name of the other table
		Mind.ERD.relations[tb1.name+'|'+tb2]= tb1;
	},
	ReDraw: function(){
		var r;
		var leftTable, rightTable= null;
		var distance= 3;
		var tmp;
		
		for(r in Mind.ERD.relations)
		{
			r= r.split('|');
			if(Mind.ERD.shownTables[r[0]] && Mind.ERD.shownTables[r[1]])
			{
				var idLeft= 'lineRelation_'+r[0]+'-'+r[1];
				var idRight= 'lineRelation_'+r[1]+'-'+r[0];
				
				$('#'+idRight+', #'+idLeft+', #'+idLeft+'__'+idRight+', #'+idRight+'__'+idLeft).remove();
				
				leftTable= document.getElementById('der_table_id_'+r[0]);
				leftTable.setAttribute('tableName', r[0]);
				rightTable= document.getElementById('der_table_id_'+r[1]);
				rightTable.setAttribute('tableName', r[1]);
				
				var relLeft= document.getElementById('lineRelation_'+r[0]+'-'+r[1])
				var relRight= document.getElementById('lineRelation_'+r[1]+'-'+r[0])
				var color= Mind.ERD.randomColors[Math.round(Math.random*Mind.ERD.randomColors.length + 1)];
				draw= false;
				
				if(!relLeft)
				{
					var relLeft= document.createElement('div');
					relLeft.id= idLeft;
					relLeft.className= 'relationLeft';
					relLeft.style.position= 'absolute';
					relLeft.style.zIndex= 1;
					relLeft.style.width= '2px';
					relLeft.style.height= '2px';
					relLeft.style.fontSize= '1px';
					relLeft.innerHTML= '<br/>';
					
					Mind.ERD.shownTables[r[0]].hiddenTable.style.top= leftTable.offsetTop+20;
					relLeft.style.left= leftTable.offsetLeft + leftTable.offsetWidth/2;
					tmp= Mind.ERD.shownTables[r[0]].foreignKeys.length + Mind.ERD.shownTables[r[0]].refered.length;
					Mind.ERD.mapAreaContainer.appendChild(relLeft);
					var t= document.getElementById('hiddenTable_'+r[0]+'_'+r[1]);
					relLeft.style.top= t.offsetTop + t.parentNode.parentNode.parentNode.offsetTop + (t.offsetHeight/2) -2;
					
					draw= true;
				}
				
				if(!relRight)
				{
					var relRight= document.createElement('div');
					relRight.id= idRight;
					relRight.className= 'relationRight';
					relRight.style.position= 'absolute';
					relRight.style.zIndex= 1;
					relRight.style.width= '2px';
					relRight.style.height= '2px';
					relRight.style.fontSize= '1px';
					relRight.innerHTML= '<br/>';
					
					relRight.style.left= rightTable.offsetLeft + rightTable.offsetWidth/2;
					
					Mind.ERD.shownTables[r[1]].hiddenTable.style.top= rightTable.offsetTop+20;
					tmp= Mind.ERD.shownTables[r[1]].foreignKeys.length + Mind.ERD.shownTables[r[1]].refered.length;
					Mind.ERD.mapAreaContainer.appendChild(relRight);
					var t= document.getElementById('hiddenTable_'+r[1]+'_'+r[0]);
					relRight.style.top= t.offsetTop + t.parentNode.parentNode.parentNode.offsetTop + (t.offsetHeight/2) -2;
					
					draw= true;
				}
				
				relLeft.setAttribute('ERDRelLine', 'true');
				relRight.setAttribute('ERDRelLine', 'true');
				if(draw)
				{
					Mind.ERD.DrawLink(relLeft, relRight);
				}
			}
		}
	},
	DrawLink: function(a, b){
		var c;
		var tmp;
		if(a.offsetLeft > b.offsetLeft)
		{
			c= a;
			a= b;
			b= c;
		}
		a.style.width= (b.offsetLeft - a.offsetLeft)/2;
		b.style.left= a.offsetWidth + a.offsetLeft;
		b.style.width= a.offsetWidth;
		tmp= b.offsetLeft;
		if(a.offsetTop > b.offsetTop)
		{
			c= a;
			a= b;
			b= c;
		}
		c= a.cloneNode(false);
		c.id= a.id +'__'+ b.id;
		c.style.left= b.offsetLeft
		c.style.width= '2px';
		c.className= 'relationMiddle';
		c.style.fontSize= '1px';
		c.style.left= tmp-1;
		c.style.height= b.offsetTop - a.offsetTop;
		c.innerHTML= '<br/>';
		c.setAttribute('ERDRelLine', 'true');
		a.parentNode.appendChild(c);
	},
	RemoveRelation: function(a, b){
		$('#lineRelation_'+a+'-'+b+', #lineRelation_'+b+'-'+a+', #lineRelation_'+a+'-'+b+'__lineRelation_'+b+'-'+a+', #lineRelation_'+b+'-'+a+'__lineRelation_'+a+'-'+b).remove();
	},
	ListSavedDERs: function(callBack){
		Mind.Component.Load (
							{componentName: "savedDERList", data: {project:Mind.Project.attributes.name}},
							function(comps){
									comps= Mind.Component.Parse(comps);
									(callBack)(comps.savedDERList);
											}
							);
	},
	SaveCurrentDER: function(){
		if(document.getElementById('saveCurrentDERName').value.replace(/ /g, '') == '')
		{
			document.getElementById('saveCurrentDERName').focus();
			document.getElementById('saveCurrentDERName').style.backgroundColor= '#ff9';
			var x= function(){
				this.style.backgroundColor= '#fff';
			}
			document.getElementById('saveCurrentDERName').onfocus= x;
			document.getElementById('saveCurrentDERName').onblur= x;
			document.getElementById('saveCurrentDERName').onkeypress= x;
			return false;
		}
		var tmp= {
			action: 'saveDER',
			project: Mind.Project.attributes.name,
			name: document.getElementById('saveCurrentDERName').value,
			tables: Array(),
			mapTop: Mind.ERD.mapAreaContainer.offsetTop,
			mapLeft: Mind.ERD.mapAreaContainer.offsetLeft
		};
		for(i in this.shownTables)
		{
			tmp.tables.push({
								tableName: this.shownTables[i].name,
								top: this.shownTables[i].OO.offsetTop,
								left: this.shownTables[i].OO.offsetLeft,
								zIndex: this.shownTables[i].OO.style.zIndex
							});
		}
		if(tmp.tables.length==0)
			return false;
		$.ajax({
			url: Mind.Properties.path+'/actions.php',
			type:'post',
			data: 'action='+JSON.stringify(tmp),
			success: function(ret, ui){
				if(ret=='true')
				{
					document.getElementById('saveCurrentDERName').blur();
					Mind.Dialog.ShowMessage('ER Diagram Saved', false, 2000);
					Mind.ERD.currentER= document.getElementById('saveCurrentDERName').value;
				}
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				Mind.AjaxHandler.Capture(XMLHttpRequest);
			}
		});
	},
	RemoveSavedDiagram: function(d){
		if(!confirm('Are you sure you want to remove this saved Diagram?'))
			return false;
		var tmp= {
			action: 'removeDER',
			project: Mind.Project.attributes.name,
			name: d
		};
		$.ajax({
			url: Mind.Properties.path+'/actions.php',
			type:'post',
			data: 'action='+JSON.stringify(tmp),
			success: function(ret, ui){
				if(ret=='true')
					document.getElementById('DDRLoadList').style.display= 'none';
				else{
						eval(ret);
					}
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				Mind.AjaxHandler.Capture(XMLHttpRequest);
			}
		});
	},
	LoadSavedDiagram: function(d){
		var verify= false;
		for(i in this.shownTables)
		{
			verify= true;
			break;
		}
		if(!d || d == '')
			return;
		if(verify)
			if(!confirm('Loading a new diagram, the current diagram will be lost. Would you like to proceed, anyway?'))
				return false;
		Mind.Component.Load (
								{componentName: "savedDERItem", data: {project:Mind.Project.attributes.name, diagram:d}},
								function(comps)
								{
									comps= Mind.Component.Parse(comps);
									Mind.ERD.Clear();
									if(d)
									{
										Mind.ERD.currentER= d;
										document.getElementById('saveCurrentDERName').value= d;
									}
									Mind.ERD.mapAreaContainer.style.left= comps.savedDERItem.mapLeft;
									Mind.ERD.mapAreaContainer.style.top= comps.savedDERItem.mapTop;
									var tables= comps.savedDERItem.tables;
									var except= false;
									for(var i in tables)
									{
										if(Mind.Project.knowledge.tables[tables[i].tableName])
										{
											Mind.ERD.addTable(false,
														  Mind.Project.knowledge.tables[tables[i].tableName],
														  [
															tables[i].left,
															tables[i].top,
															tables[i].zIndex
														  ]);
										}else{
													except= true;
											   }
									}
									if(except)
									{
										Mind.Dialog.ShowData('Some tables do not exist anymore and will not be added to the current diagram', 'Error');
									}
								}
							);
	},
	Clear: function(){
		this.shownTables= Array();
		this.relations= Array();
		Mind.ERD.mapAreaContainer.innerHTML= '';
		$('#outputPanel_der_listContainer [tableName]').removeClass('der_list_table_');
	},
	showBallon:function(o, flag){
	
		o.style.height= '100px';
		o.style.width= '241px';
		if(flag)
		{
			if(document.getElementById('saveCurrentDERName').style.display != 'none')
			{
				Mind.ERD.showBallon(o, false);
				return;
			}
			o.style.backgroundPosition= '0px 0px';
			document.getElementById('saveCurrentDERName').style.display= '';
			document.getElementById('saveCurrentDERName').focus(true);
		}else{
				o.style.backgroundPosition= '0px -110px';
				document.getElementById('saveCurrentDERName').style.display= 'none';
			 }
		if(Mind.ERD.currentER)
			document.getElementById('saveCurrentDERName').value= Mind.ERD.currentER;
		o.style.display= '';
	},
	ShowToolBar: function(){
		var o= document.getElementById('erdToolBar');
		if(o.style.display != 'none')
		{
			o.style.display = 'none';
			return;
		}
		o.style.backgroundImage= 'url('+Mind.Properties.imagesPath+'/erd_balloons.png)';
		
		var str= "<div style='padding-top:10px; padding-right:14px; height:24px;'>";
		str+= "<input type='text' style='width:193px;' id='saveCurrentDERName' alt='Press ENTER to save' title='Press ENTER to save' onkeypress='if(event.keyCode == 13){Mind.ERD.SaveCurrentDER(); Mind.ERD.ShowToolBar()}'/>";
		str+= "</div>";
		str+= "<div style='padding-right:40px;padding-top:6px;'>";
		
		str+= "<table align='right'><tr><td><select id='savedDERList' onchange='Mind.ERD.LoadSavedDiagram(this.value)'>";
			str+= "<option>";
			str+= "</option>";
		str+= "</select></td>";
		str+= "<td><img src='"+Mind.Properties.imagesPath+"/save.png' id='DERSaveButton' title='save' onclick='Mind.ERD.showBallon(document.getElementById(\"erdToolBar\"), true);'/>";
		str+= "<img src='"+Mind.Properties.imagesPath+"/del.gif' id='DERDelSavedItemButton' title='Delete saved ER Diagram' onclick='Mind.ERD.RemoveSavedDiagram(document.getElementById(\"savedDERList\").value); Mind.ERD.ShowToolBar()'/>";
		str+= "</td></tr></table></div>";
		o.innerHTML= str;
		
		this.showBallon(o, false);
		
		this.ListSavedDERs(function(l){
			var o= document.getElementById('savedDERList');
			o.innerHTML= '<option value=""></option>';
			for(x in l)
				o.innerHTML+= "<option value='"+l[x]+"'>"+l[x]+"</option>";
			if(Mind.ERD.currentER)
				o.value= Mind.ERD.currentER;
		});
	},
	PinPanel: function(){
		var l= document.getElementById('outputPanel_der_listContainer');
		var o= document.getElementById('DERPinButton');
		if(l.style.display == '')
		{
			$(o.parentNode.getElementsByTagName('IMG')).css('display', 'none');
			o.style.display= '';
			o.className= 'pinActived';
			l.style.display= 'none';
			l.parentNode.style.width= '25px';
			l.parentNode.style.border= 'solid 4px #fff';
		}else{
				o.className= '';
				$(o.parentNode.getElementsByTagName('IMG')).css('display', '');
				l.style.display= '';
				l.parentNode.style.width= '210px';
				l.parentNode.style.border= 'none';
			 }
		if(document.getElementById('erdToolBar').style.display != 'none')
			Mind.ERD.ShowToolBar();
	},
	ResetZoom: function(){
		var o= Mind.ERD.mapAreaContainer;
		o.setAttribute('currentScale', 1);
		o.style.MozTransform= 'scale(1)';
		o.style.webkitTransform= 'scale(1)';
	},
	ApplyZoom: function(v, force){
		var o= Mind.ERD.mapAreaContainer;
		if(!o.getAttribute('currentScale'))
			o.setAttribute('currentScale', 1.0);
		var s= o.getAttribute('currentScale');
		if(!force)
		{
			if(v>0)
			{
				if(s > 1.3)
					return false;
				s= 0.1 - (s*-1);
			}else{
					if(s < 0.7)
						return false;
					s= s-0.1;
				 }
		}else
			s= v;
		o.setAttribute('currentScale', s);
		o.style.MozTransform= 'scale('+s+')';
		o.style.webkitTransform= 'scale('+s+')';
	},
	drawTable: function(ctx, l, t, w, h)
	{
		  var lingrad = ctx.createLinearGradient(0,t,0,t+h);
		
		  lingrad.addColorStop(0, '#bbb');
		  lingrad.addColorStop(1, '#fff');  
		  ctx.lineWidth= 0.7;
		  //lingrad.addColorStop(0.5, '#26C000');  
		  //lingrad.addColorStop(1, '#fff');  
		  
		  var lingrad2 = ctx.createLinearGradient(0,t,0,t+h);
		  lingrad2.addColorStop(0, '#000');
		  lingrad2.addColorStop(0.5, '#ddd');
		  lingrad2.addColorStop(1, '#000');
		  // assign gradients to fill and stroke styles  
		  ctx.fillStyle = lingrad;  
		  ctx.strokeStyle = lingrad2;  

		  // draw shapes
		  ctx.fillRect(l, t, w, h);
		  ctx.strokeRect(l, t, w, h);
	},
	Plot: function(){
		var oCanvas= document.getElementById('oCanvas');
		var strDataURI = oCanvas.toDataURL("image/jpeg");
		var ctx = document.getElementById("oCanvas").getContext("2d");
		
		var leftEst = -1;
		var lessLeft= -1;
		var toppEst = -1;
		var lessTop = -1;
		
		for(i in this.shownTables)
		{
			if(leftEst == -1 || leftEst > this.shownTables[i].OO.offsetLeft)
				leftEst= this.shownTables[i].OO.offsetLeft;
			if(toppEst == -1 || toppEst > this.shownTables[i].OO.offsetTop)
				toppEst= this.shownTables[i].OO.offsetTop;
				
			if(lessLeft == -1 || lessLeft < this.shownTables[i].OO.offsetLeft + this.shownTables[i].OO.offsetWidth)
				lessLeft= this.shownTables[i].OO.offsetLeft + this.shownTables[i].OO.offsetWidth;
			if(lessTop == -1 || lessTop < this.shownTables[i].OO.offsetTop + this.shownTables[i].OO.offsetHeight)
				lessTop= this.shownTables[i].OO.offsetTop + this.shownTables[i].OO.offsetHeight;
		}
		leftEst-= 12;
		lessLeft+= 12;
		toppEst-= 12;
		lessTop+= 12;
		oCanvas.setAttribute('width', lessLeft - leftEst);
		oCanvas.setAttribute('height', lessTop - toppEst);
		var w= oCanvas.offsetWidth;
		var h= oCanvas.offsetHeight;
		ctx.clearRect(0, 0, w, h);
		
		ctx.fillStyle = "red";
		ctx.strokeStyle = 'red';
		
		
		// drawing the tables
		ctx.font= "16px Tahoma";
		var l=0, t= 0;
		var tW=200, tH= 200;
		var c= 0;
		// first, the relations
		var links= Mind.ERD.mapAreaContainer.getElementsByTagName('DIV');
		var curPos= 0;
		var start= 0;
		var end= 0;
		//var lastLeft= 0;
		for(var i= 0, j= links.length; i<j; i++)
		{
			l= links[i].offsetLeft - leftEst;
			t= links[i].offsetTop - toppEst;
			tW= links[i].offsetWidth;
			tH= links[i].offsetHeight;

			/*ctx.fillStyle= 'red';
			ctx.fillStyle = "red";
			ctx.lineWidth = "8";
			ctx.lineStyle = "red";
			ctx.lineColor = "red";*/
			ctx.strokeStyle= '#777';
			ctx.lineWidth = "0.5";
			var curPos= 0;
			
			while(curPos < tW)
			{
				ctx.moveTo(l+curPos, t);
				//ctx.lineTo(l+curPos+4, t);
			    ctx.strokeRect(l+curPos+2, t, 2, 1);
				curPos+= 4;
			}
			//ctx.stroke();
			curPos= 0;
			while(curPos < tH -3)
			{
				ctx.moveTo(l, t+curPos);
				//ctx.lineTo(l, t+curPos+4);
				ctx.strokeRect(l+2, t+curPos+2, 1, 2);
				curPos+= 4;
			}
			//ctx.stroke();
		}
		
		// then, the tables
		for(i in this.shownTables)
		{
			//ctx.fillStyle = "black";
			l= this.shownTables[i].OO.offsetLeft - leftEst;
			t= this.shownTables[i].OO.offsetTop - toppEst+10;
			tW= this.shownTables[i].OO.offsetWidth;
			tH= this.shownTables[i].OO.offsetHeight-10;
			
			ctx.clearRect(l+1,t+1,tW-2,tH-2);
			this.drawTable(ctx, l, t, tW+10, tH);/**************************************************************/
			
			ctx.textAlign= 'center';
			ctx.font= "16px Arial, Tahoma";
			ctx.fillStyle = "#000";
			ctx.fillText(this.shownTables[i].name, l+(tW/2), t+15);
			ctx.fillStyle = "#444";
			
			ctx.textAlign= 'left';
			/*
			ctx.fillRect(l,t,tW,tH);
			ctx.clearRect(l+1,t+1,tW-2,tH-2);
			ctx.textAlign= 'center';
			ctx.fillStyle = "red";
			ctx.font= "16px Tahoma";
			ctx.fillText(this.shownTables[i].name, l+(tW/2), t+15);
			ctx.fillStyle = "black";
			
			//obj= Mind.Project.knowledge.tables[this.shownTables[i].name];
			ctx.textAlign= 'left';
			*/
			
			//ctx.fillStyle = "black";
			obj= Mind.Project.knowledge.tables[this.shownTables[i].name];
			t+= 2;
			c= 0;
			var lH= 20;
			if(obj.attributes)
			{
				ctx.lineWidth= 0.5;
				ctx.strokeRect(l, t+18, tW, 1);
				ctx.lineWidth= 1;
				
				t+= 4;
				for(var att in obj.attributes)
				{
					c+= lH;
					ctx.fillText(obj.attributes[att].name, l+13, t+8+c);
					if(obj.attributes[att].pk)
					{
						ctx.shadowOffsetX = 0;
						ctx.shadowOffsetY = 0;
						ctx.shadowBlur = 6;
						ctx.shadowColor = "rgba(0, 0, 0, 0.7)";
						
						ctx.fillStyle= '#ff0';
						ctx.beginPath();
						ctx.moveTo(l+6, t+20);
						ctx.lineTo(l+2, t+24);
						ctx.lineTo(l+6, t+28);
						ctx.lineTo(l+10, t+24);
						ctx.closePath();
						ctx.fill();
   						ctx.fillStyle= '#555';
   						
						ctx.shadowOffsetX = 0;
						ctx.shadowOffsetY = 0;
						ctx.shadowBlur = 0;
					}
				}
			}
			// foreign keys
			c+= lH+8;
			if(obj.foreignKeys.length > 0)
			{
				//ctx.moveTo(l,t+c);
				for(fk in obj.foreignKeys)
				{
					ctx.fillText(obj.foreignKeys[fk][0], l+13, t+c);
					
					ctx.fillStyle= '#777';
					ctx.beginPath();
					ctx.moveTo(l+6, t+c-8);
					ctx.lineTo(l+2, t+c+4-8);
					ctx.lineTo(l+6, t+c+8-8);
					ctx.lineTo(l+10, t+c+4-8);
					ctx.closePath();
					ctx.fill();
					ctx.fillStyle= '#555';
					
					c+= lH;
				}
			}
		}
		
		if(document.getElementById('tmpCanvas'))
			document.getElementById('tmpCanvas').parentNode.removeChild(document.getElementById('tmpCanvas'));
		if(w > 900)
			w= 900;
		if(h > 700)
			h= 700;
		Mind.tmpShownData= Mind.Dialog.ShowData("<div id='tmpCanvas'></div>",
						  'ER Diagram', w+40, h+60, true);
		var image= Canvas2Image.saveAsPNG(document.getElementById('oCanvas'), true);
		document.getElementById('tmpCanvas').appendChild(image);
		document.getElementById('tmpCanvas').parentNode.style.backgroundImage= "url("+Mind.Properties.imagesPath+"/back_der.gif)";
		ctx.clearRect(0, 0, 99999, 99999);
	}
};
