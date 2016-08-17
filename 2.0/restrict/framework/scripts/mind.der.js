Mind.ERD= {
	mapArea: null,
	tablesShown: Array(),
	relations: Array(),
	Init: function(mA){
		var tmp= document.getElementById(mA);
		Mind.ERD.tablesShown= Array();
		Mind.ERD.relations= Array();
		Mind.ERD.mapArea= document.createElement('DIV');
		tmp.innerHTML= '';
		tmp.appendChild(Mind.ERD.mapArea);
		Mind.ERD.mapAreaContainer= document.createElement('DIV')
		Mind.ERD.mapAreaContainer.setAttribute('id', 'mapAreaContainer');
		Mind.ERD.mapArea.innerHTML= '';
		Mind.ERD.mapAreaContainer.innerHTML= '';
		//Mind.ERD.mapAreaContainer.style.width= '100%';
		Mind.ERD.mapAreaContainer.style.height= '9999px';
		Mind.ERD.mapArea.setAttribute('autoresize', 'true');
		Mind.ERD.mapArea.style.position= 'relative';
		Mind.ERD.mapArea.style.zIndex= '0';
		Mind.ERD.mapArea.style.height= '100%';
		Mind.ERD.mapArea.style.width= '100%'; //screen.width - 260;
		Mind.ERD.mapArea.style.overFlow= 'auto';
		Mind.ERD.mapArea.appendChild(Mind.ERD.mapAreaContainer);
		
		
		//Mind.ERD.mapArea.style.width= '99%';
		//Mind.ERD.mapArea.style.height= '99%';
		//Mind.ERD.mapArea.style.position= 'relative';
		/*
		Mind.ERD.mapArea.style.clear= 'right';
		Mind.ERD.mapArea.style.height= '300px';
		Mind.ERD.mapArea.style.overflow= 'auto';
		Mind.ERD.mapArea.style.position= 'relative';
		Mind.ERD.mapArea.style.width= '100%';
		*/
		
		Mind.ERD.mapArea.style.overflow= 'auto';
		//document.getElementById(mA).innerHTML= '';
		//document.getElementById(mA).appendChild(Mind.ERD.mapArea);
		Mind.ERD.mapArea.style.backgroundImage= 'url('+Mind.Properties.imagesPath+'/back_der.gif)';
		Mind.Panel["bottom"].Adjust();
	},
	addTable: function(event, table){
		if(Mind.ERD.tablesShown[table.name])
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
		var img1= document.createElement('IMG');
		td.style.textAlign= 'right';
		img1.src= Mind.Properties.imagesPath+'/info.gif';
		var img2= document.createElement('IMG');
		img2.src= Mind.Properties.imagesPath+'/del.gif';
		img1.style.position= 'relative';
		img2.style.position= 'relative';
		img1.style.left= '6px';
		img1.style.top= '6px';
		img2.style.left= '6px';
		img2.style.top= '6px';
		img1.setAttribute('tableName', table.name);
		img2.setAttribute('tableName', table.name);
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
			delete Mind.ERD.tablesShown[this.getAttribute('tableName')];
			Mind.ERD.mapAreaContainer.removeChild(document.getElementById('der_table_id_'+this.getAttribute('tableName')));
		}
		
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
			//td.innerHTML= '<br>';
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
			}
		}
		//Mind.ERD.mapArea.appendChild(tb);
		
		Mind.ERD.mapAreaContainer.appendChild(tb);
		
		tb.style.left= (event.clientX- document.getElementById('outputPanel_der_list').offsetWidth - tb.offsetWidth/2)+Mind.ERD.mapArea.scrollLeft;
		tb.style.top= (event.clientY- Mind.Panel["bottom"].htmlElement.offsetTop-tb.offsetHeight/2)+Mind.ERD.mapArea.scrollTop - (tb.offsetHeight/2) + 20;
		
		tb.setAttribute('DERTable', 'true')
		tb.style.zIndex= 9;
		$(tb).draggable({stack: { group: '#outputPanel_der_body [DERTable=true]', min: 50 },
						 containment:Mind.ERD.mapArea,
						 scroll:true,
						 drag:function(){
							Mind.ERD.ReDraw();
						 },
						 stop:function(){
							Mind.ERD.ReDraw();
						 },
						 scrollSensitivity: tb.offsetWidth/2
						 });
		Mind.ERD.tablesShown[table.name]= table;
		Mind.ERD.ReDraw();
	},
	AddRelation: function (tb1, tb2){
		Mind.ERD.relations[tb1.name+'|'+tb2]= tb1;
	},
	ReDraw: function(){
		var r;
		var leftTable, rightTable= null;
		for(r in Mind.ERD.relations)
		{
			r= r.split('|');
			
			
			if(Mind.ERD.tablesShown[r[0]] && Mind.ERD.tablesShown[r[1]])
			{
				var idLeft= 'lineRelation_'+r[0]+'-'+r[1];
				var idRight= 'lineRelation_'+r[1]+'-'+r[0];
				
				$('#'+idRight+', #'+idLeft+', #'+idLeft+'__'+idRight+', #'+idRight+'__'+idLeft).remove();
				
				leftTable= document.getElementById('der_table_id_'+r[0]);
				leftTable.setAttribute('tableName', r[0]);
				rightTable= document.getElementById('der_table_id_'+r[1]);
				rightTable.setAttribute('tableName', r[1]);
				//alert(leftTable.offsetLeft+'\n'+rightTable.offsetLeft);
				
				var relLeft= document.getElementById('lineRelation_'+r[0]+'-'+r[1])
				var relRight= document.getElementById('lineRelation_'+r[1]+'-'+r[0])
				draw= false;
				
				if(!relLeft)
				{
					var relLeft= document.createElement('div');
					relLeft.id= idLeft;
					relLeft.className= 'relationLeft';
					relLeft.style.position= 'absolute';
					relLeft.style.zIndex= 0;
					relLeft.style.width= '2px';
					relLeft.style.height= '2px';
					relLeft.style.fontSize= '1px';
					relLeft.innerHTML= '<br/>';
					
					relLeft.style.left= leftTable.offsetLeft + leftTable.offsetWidth/2;
					relLeft.style.top= leftTable.offsetTop + leftTable.offsetHeight/2;
					
					Mind.ERD.mapAreaContainer.appendChild(relLeft);
					draw= true;
				}
				if(!relRight)
				{
					var relRight= document.createElement('div');
					relRight.id= idRight;
					relRight.className= 'relationRight';
					relRight.style.position= 'absolute';
					relRight.style.zIndex= 0;
					relRight.style.width= '2px';
					relRight.style.height= '2px';
					relRight.style.fontSize= '1px';
					relRight.innerHTML= '<br/>';
					
					relRight.style.left= rightTable.offsetLeft + rightTable.offsetWidth/2;
					relRight.style.top= rightTable.offsetTop + rightTable.offsetHeight/2;
					
					Mind.ERD.mapAreaContainer.appendChild(relRight);
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
		//a= 'lineRelation_'+a+'-'+b;
		//b= 'lineRelation_'+b+'-'+a;
		//$('#'+a+', #'+b+', #'+a+'__'+b+', #'+b+'__'+a).remove();
		
		$('#lineRelation_'+a+'-'+b+', #lineRelation_'+b+'-'+a+', #lineRelation_'+a+'-'+b+'__lineRelation_'+b+'-'+a+', #lineRelation_'+b+'-'+a+'__lineRelation_'+a+'-'+b).remove();
	}
};











