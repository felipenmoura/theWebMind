Mind.MindEditor= {
	ActivedColorPicker: false,
	fullMode : true,
	lineNumber:1,
	CharNumber:0,
	useAreas:true,
	SetItalic: function(bt){
		if(bt.className == 'bt')
		{
			bt.className= 'bt_active';
			document.getElementById('mindEditor').style.fontStyle='italic';
		}else{
				bt.className= 'bt';
				document.getElementById('mindEditor').style.fontStyle='normal';
			 }
	},
	SetBold: function(bt){
		bt = bt ? bt : "normal";
		if(bt == "normal" || bt == "bold"){
			document.getElementById('mindEditor').style.fontWeight=bt;
			return false;
		}
		if(bt.className == "bt")
		{
			bt.className= 'bt_active';
			document.getElementById('mindEditor').style.fontWeight='bold';
			Mind.Theme.userConfig.bold = 'bold';
		}else{
				bt.className= 'bt';
				document.getElementById('mindEditor').style.fontWeight='normal';
				Mind.Theme.userConfig.bold = 'normal';
			 }
	},
	SetSize: function(s){		
		if(document.getElementById('mindEditor') != null){
			document.getElementById('mindEditor').style.fontSize=s+'px';
			document.getElementById('mindEditor').style.lineHeight= s+'px';
			document.getElementById('mindEditor_lineNumberColumn').style.fontSize=s+'px';
			document.getElementById('mindEditor_lineNumberColumn').style.lineHeight=s+'px';
			document.getElementById('mindEditor_lineNumberColumnArea').style.lineHeight= s+'px';
			document.getElementById('mindEditor_lineNumberColumnArea').style.fontSize= s+'px';
			Mind.Theme.userConfig.fontSize = s+'px';
		}
	},
	PrintOnWindow:function(o){
		o.innerHTML= '<pre>'+document.getElementById('mindEditor').value+'</pre>';
	},
	Print: function(){
		var p= window.open('printer.php', 'p');
	},
	ShowColorPicker: function(event, obj){
		Mind.MindEditor.ActivedColorPicker= obj;
		$('#colorPickerWrapper').css('left', event.clientX);
		$('#colorPickerWrapper').css('top', event.clientY);
		$('#colorPickerWrapper').css('height', 5);
		$('#colorPickerWrapper').show();
		$('#colorPickerWrapper').animate({height:190, top:event.clientY-190});
		$('#blocker').show();
		$('#blocker').css('zIndex', '998');
		$('#blocker').bind('click', function(event){
			var y= (document.getElementById('colorPickerWrapper').offsetTop + document.getElementById('colorPickerWrapper').offsetHeight);
			$('#colorPickerWrapper').animate({height:5, top:y}, function(){$('#colorPickerWrapper').hide()});
			$('#blocker').hide();
			Mind.MindEditor.ActivedColorPicker= false;
		})
	},
	ColorPickerChanged: function(rgb){				
		Mind.MindEditor.ActivedColorPicker.style.background= '#'+rgb+'';
		if(Mind.MindEditor.ActivedColorPicker.getAttribute('changes') == 'color'){
			document.getElementById('mindEditor').style[Mind.MindEditor.ActivedColorPicker.getAttribute('changes')]= rgb;
			Mind.Theme.userConfig.fontColor = "#" + rgb;
		}else{
			document.getElementById('bgToChangeOnEditor').style[Mind.MindEditor.ActivedColorPicker.getAttribute('changes')]= rgb;
			Mind.Theme.userConfig.backgroundColor = "#" + rgb;
		}
	},
	ChangeType: function(obj)
	{
		if(obj.value == '0')
		{
			document.getElementById('GUIEntityParams').style.display= '';
			document.getElementById('GUIAttributeParams').style.display= 'none';
		}else{
				document.getElementById('GUIEntityParams').style.display= 'none';
				document.getElementById('GUIAttributeParams').style.display= '';
			 }
	},
	filterCaracters: function(v)
	{
		v= v.replace(/[\"\'@#$%&\(\)\[\]\{\}\+\=\�]/ig, '');
		if(!isNaN(v.substring(0,1)))
			v= '_'+v;
		return v;
	},
	AddCommandLine: function()
	{
		var en1= document.getElementById('GUIEntity1').value;
		var cont= document.getElementById('mindEditor').value;
		var str= '';
		if(document.getElementById('GUILikType').value== '0')
		{	//	entity
			var en2= document.getElementById('GUIEntity2').value;
			var link= document.getElementById('GUIEntityLinkType').value;
			var nl= (cont.length > 0)? '\n': '';
			str= nl+ en1 +' has '+link.substring(0,1)+' '+en2;
			if(link.length == 2)
			{
				str+= '\n'+en2 +' has '+link.substring(1,2)+' '+en1;
			}
		}else{	//	attribute
				var attName= document.getElementById('GUIAttributeName').value;
				attName= this.filterCaracters(attName);
				if(attName.indexOf(' ')>=0)
					attName= '"'+attName+'"';
				var str= ((cont.length > 0)? '\n': '')+en1 + ' has ' + attName;
				str+= ':' + document.getElementById('GUIAttributeType').value;
				str+= '('+ ((document.getElementById('GUIAttributeSize').value.replace(/ /g)=='')? '16': document.getElementById('GUIAttributeSize').value);
				if(document.getElementById('GUIAttributeDefault').value.replace(/ /g, '') != '')
					str+= ", '"+ document.getElementById('GUIAttributeDefault').value +"'";
				if(document.getElementById('GUIAttributeNotNull').checked)
					str+= ', not null';
				if(document.getElementById('GUIAttributeUnique').checked)
					str+= ', unique';
				str+= ')';
				if(document.getElementById('GUIAttributeComment').value.replace(/ /g, '') != '')
					str+= ' //'+document.getElementById('GUIAttributeComment').value;
			 }
		if(document.all)
			str= cont+str;
		document.getElementById('mindEditor').value+= str;
		document.getElementById('GUIEntity1').value= '';
		document.getElementById('GUIEntity2').value= '';
		document.getElementById('GUIEntityLinkType').value= '';
		document.getElementById('GUIAttributeComment').value= '';
		document.getElementById('GUIAttributeName').value= '';
		document.getElementById('GUIAttributeType').value= '';
		document.getElementById('GUIAttributeSize').value= '';
		document.getElementById('GUIAttributeDefault').value= '';
		document.getElementById('GUIAttributeNotNull').value= '';
		document.getElementById('GUIAttributeUnique').value= '';
		document.getElementById('GUIEntity1').focus();
		//showLines();
	},
	HideTools: function(){
		if(document.getElementById('MindEditorFooterTools'))
			$('#MindEditorFooterTools').hide();
	},
	ShowTools: function(){
		if(document.getElementById('MindEditorFooterTools'))
			$('#MindEditorFooterTools').show();
	},
	Typing: function (value, event){
		this.charNumber= value.length;
		var oldLnNmro= this.lineNumber;
		
		if(this.lineNumber != (this.lineNumber= value.split('\n').length))
		{
			var lineNumberColumn= document.getElementById('mindEditor_lineNumberColumn');
			var useAjax= false;
			//lineNumberColumn.innerHTML= '';
			
			// the next blocks (ugly, I agree) were creted to increase the performance when the difference of the line number is too high
			if(this.lineNumber < oldLnNmro)
			{
				if(this.lineNumber < 50)
				{
					var str= '';
					for(var i=1; i<=this.lineNumber; i++)
					{ str+= i+'\n'; }
					lineNumberColumn.innerHTML= str;
				}else{
						if(oldLnNmro - this.lineNumber < 200)
						{
							var ln= lineNumberColumn.innerHTML.split('\n');
							for(var i=oldLnNmro; i>=this.lineNumber; i--)
							{
								ln.pop();
							}
							lineNumberColumn.innerHTML= ln.join('\n');
							lineNumberColumn.innerHTML+= '\n';
						}else
							useAjax= true;
					 }
			}else{
					if(this.lineNumber - oldLnNmro < 200)
					{
						for(var i=oldLnNmro+1; i<=this.lineNumber; i++)
							lineNumberColumn.innerHTML+= i+'\n';
						//lineNumberColumn.innerHTML+= '\n'+i;
					}else
						useAjax= true;
				 }
			if(useAjax)
			{
				// here, let's use Ajax simply to force this operation to be async whereas there are many lines of difference
				$.ajax({
					url: Mind.Properties.path+'/lines_loader.php',
					type: 'POST',
					data: 'lns='+ this.lineNumber,
					success: function(ret){
						lineNumberColumn.innerHTML= ret;
					}
				});
			}
		}
		Mind.Project.Change();
	},
	SetUserConfig : function(){
		// Tamanho da fonte
		Mind.MindEditor.SetSize(Mind.Theme.userConfig.fontSize);
		//Marca como selecionado a op��o armazenada
		if(document.getElementById("font_size_select"))
		{
			var comboFontSize = document.getElementById("font_size_select");
			var options = comboFontSize.getElementsByTagName("OPTION");
			for(var i=0;i<options.length;i++)
				options[i].selected = options[i].innerHTML == Mind.Theme.userConfig.fontSize ? true : false;
		}
		// Negrito			
		Mind.MindEditor.SetBold(Mind.Theme.userConfig.bold);
		
		// Cor do texto
		document.getElementById('mindEditor').style.color = Mind.Theme.userConfig.fontColor;
		
		// Cor de Fundo
		document.getElementById('bgToChangeOnEditor').style.backgroundColor = Mind.Theme.userConfig.backgroundColor;			
		
		// Ajusta a posi��o do painel esquerdo		
		Mind.Panel["left"].Size(Mind.Theme.userConfig.leftPanelPos);					
		Mind.Panel["right"].Size(parseInt(document.body.clientWidth) - parseInt(Mind.Theme.userConfig.rightPanelPos));
		Mind.Panel["bottom"].Size(parseInt(document.body.clientHeight) - parseInt(Mind.Theme.userConfig.bottomPanelPos));
		Mind.Panel['bottom'].Adjust();
		Mind.Panel["center"].Adjust();				
	},
	SetFull : function(){		
		
		if(this.fullMode)
		  {
			Mind.Panels.Close();
			this.fullMode = false;
			$("#mindEditorFullScreenButton").attr("src",Mind.Properties.imagesPath +'/bt_full_close.png');
		  }else {
					Mind.Panels.Open();
					$("#mindEditorFullScreenButton").attr("src",Mind.Properties.imagesPath +'/bt_full_editor.png');
					this.fullMode = true;
				}
	},
	ShowCodeEditor: function(event){
		var e= document.getElementById('mindEditorTool');
		if(e.style.display != 'none') // hide
		{
			$('#mindEditorTool [guia=true]').css('display', 'none');
			var h= e.offsetHeight;
			setTimeout(function(){
									$(e).animate({
												top:e.offsetTop + e.offsetHeight - 2,
												height:1
												 }, function(){
																e.style.display= 'none';
																e.style.height= h;
															  });
								 }, 200);
			return;
		}
		e.style.display= '';
		var h= e.offsetHeight;
		var c= document.getElementById('mindEditor').value;
		c= c.match(/(^|\n)+\$.*:/gi);
		if(c)
		{
			var st= document.getElementById('mindEditorTool_Attribute_subType');
			st.innerHTML= '<option></option>';
			for(var i=0; i<c.length; i++)
			{
				c[i]= c[i].substring(0, c[i].length-1);
				st.innerHTML+= "<option value='"+c[i]+"'>"+c[i]+"</option>";
			}
		}
		e.style.left= Mind.Panel['center'].htmlElement.offsetLeft + Mind.Panel['center'].htmlElement.offsetWidth - e.offsetWidth -22;
		var t= Mind.Panel['center'].htmlElement.offsetHeight - 17 - e.offsetHeight;
		e.style.top= Mind.Panel['center'].htmlElement.offsetHeight - 17;
		e.style.height= '1px';
		$(e).animate({
						top:t,
						height:h
					 }, function(){ $('#mindEditorTool [guia=true]').css('display', 'block'); });
		$(e).draggable({
			containment:document.body
		});
	},
	AddEntityLine: function(){
		var rel= document.getElementById('mindEditorTool_Entities_Rel').value;
		var l= document.getElementById('mindEditorTool_Entities_left').value;
		var r= document.getElementById('mindEditorTool_Entities_right').value;
		var str= (document.getElementById('mindEditor').value == '')? '': '\n';
		if(l == '' || r == '')
			return;
		if(rel == '1/1')
		{
			str+= l+ ' '+ Mind.Project.attributes['verb']+ ' 1 '+ r+ '.\n';
			str+= r+ ' '+ Mind.Project.attributes['verb']+ ' 1 '+ l;
		}else if(rel == 'n/n')
			 {
				str+= l+ ' '+ Mind.Project.attributes['verb']+ ' n '+ r+ '.\n';
				str+= r+ ' '+ Mind.Project.attributes['verb']+ ' n '+ l;
			 }else{
					str+= l+ ' '+ Mind.Project.attributes['verb']+ ' '+ rel.substring(2,3)+ ' '+ r;
				  }
		document.getElementById('mindEditor').value+= str+'.';
		Mind.MindEditor.Typing(document.getElementById('mindEditor').value, false);
		document.getElementById('mindEditorToolForm').reset();
		document.getElementById('mindEditorTool_Entities_left').focus();
	},
	AddAttributeLine: function(){
		var entity= document.getElementById('mindEditorTool_Attribute_entity').value;
		if(entity == '')
			return;
		var att= document.getElementById('mindEditorTool_Attribute_att').value;
		var subType= document.getElementById('mindEditorTool_Attribute_subType').value;
		var str= (document.getElementById('mindEditor').value == '')? '': '\n';
		
		if(subType.replace(/ /g, '') != '')
			str+= entity+ ' '+ Mind.Project.attributes['verb']+ ' '+ att+ ':'+ subType.replace(/^\$/, '')+ '()';
		else{
				var type= document.getElementById('mindEditorTool_Attribute_type').value;
				var weight= document.getElementById('mindEditorTool_Attribute_weight').value;
				var notNull= document.getElementById('mindEditorTool_Attribute_notNull').checked;
				var comment= document.getElementById('mindEditorTool_Attribute_comment').value;
				var def= document.getElementById('mindEditorTool_subtype_default').value;
				var mask= document.getElementById('mindEditorTool_Attribute_mask').value;
				var options= document.getElementById('mindEditorTool_Attribute_options').value;
				str+= entity+ ' '+ Mind.Project.attributes['verb']+ ' '+ att+':'+type+'(';
				var parm= '';
				weight= (weight)? weight: '16';
				if(weight.replace(/ /g, '') != '')
					parm+= ((parm.length>0)? ', ': '')+ weight;
				if(mask.replace(/ /g, '') != '')
					parm+= ((parm.length>0)? ', ': '')+ '['+ mask+ ']';
				if(notNull)
					parm+= ((parm.length>0)? ', ': '')+ 'notnull';
				
				if(options.replace(/ /g, '') != '')
				{
					parm+= ((parm.length>0)? ', ': '')+ '{';
					options= options.split('\n');
					var op= '';
					for(var i=0; i<options.length; i++)
					{
						op+= ((op.length>0)? '|': '')+ options[i];
					}
					parm+= op+ '}';
				}
				
				if(def != '')
					parm+= ', "'+def.replace(/"/g, '\\"')+'"';
				
				str+= parm;
				str+= ')';
				if(comment.replace(/ /g, '') != '')
				str+= ' // '+ comment;
			}
		document.getElementById('mindEditor').value+= str+'.';
		Mind.MindEditor.Typing(document.getElementById('mindEditor').value, false);
		document.getElementById('mindEditorToolForm').reset();
		document.getElementById('mindEditorTool_Attribute_entity').focus();
	},
	AddsubtypeLine: function(){
		var subType= document.getElementById('mindEditorTool_subtype_name').value;
		if(subType == '')
			return;
		var type= document.getElementById('mindEditorTool_subtype_type').value;
		var weight= document.getElementById('mindEditorTool_subtype_weight').value;
		var notNull= document.getElementById('mindEditorTool_subtype_notNull').checked;
		var comment= document.getElementById('mindEditorTool_subtype_comment').value;
		var mask= document.getElementById('mindEditorTool_subtype_mask').value;
		var options= document.getElementById('mindEditorTool_subtype_options').value;
		var str= '';
		str+= '$'+subType +':'+ type+'(';
		var parm= '';
		if(weight.replace(/ /g, '') != '')
			parm+= ((parm.length>0)? ', ': '')+ weight;
		if(mask.replace(/ /g, '') != '')
			parm+= ((parm.length>0)? ', ': '')+ '['+ mask+ ']';
		if(notNull)
			parm+= ((parm.length>0)? ', ': '')+ 'notnull';
		
		if(options.replace(/ /g, '') != '')
		{
			parm+= ((parm.length>0)? ', ': '')+ '{';
			options= options.split('\n');
			var op= '';
			for(var i=0; i<options.length; i++)
			{
				op+= ((op.length>0)? '|': '')+ options[i];
			}
			parm+= op+ '}';
		}
		
		document.getElementById('mindEditorTool_Attribute_subType').innerHTML+= "<option value='$"+subType+"'>$"+subType+"</option>";
		
		str+= parm;
		str+= ')';
		if(comment.replace(/ /g, '') != '')
		str+= ' // '+ comment;
		
		document.getElementById('mindEditor').value= str+ '.\n'+ document.getElementById('mindEditor').value;
		Mind.MindEditor.Typing(document.getElementById('mindEditor').value, false);
		document.getElementById('mindEditorToolForm').reset();
		document.getElementById('mindEditorTool_subtype_name').focus();
	},
	ShowSearchPanel: function(event){
		/*
		document.getElementById('mindEditorSearch').style.display = '';
		document.getElementById('mindEditorSearch').style.bottom = event.clientY;
		document.getElementById('mindEditorSearch').style.left = event.clientX - 100;
		document.getElementById('mindEditorSearch').style.zIndex= '999999';
		*/
		Mind.Dialog.ShowData(document.getElementById('mindEditorSearch').innerHTML, 'Search', false, false, false, Mind.MindEditor.ClearSearch);
	},
	Search: function(o){
		//var filter= document.getElementById('mindEditorSearch_filter').value;
		var filter= o.parentNode.getElementsByTagName('input')[0].value;
		var legend= o.parentNode.getElementsByTagName('DIV')[0].getElementsByTagName('span');
		var fullMatch= 0;
		var partialMatch= 0;
		
		if(filter.replace(/ /g, '').length > 2)
		{
			//alert('searching for '+filter);
			var code= document.getElementById('mindEditor').value.split('\n');
			o= document.getElementById('mindEditor_lineNumberColumnArea');
			o.innerHTML= '';
			var regEx= new RegExp('^((.)* )?'+filter+'[\n|\r| |\;|\.]{1,}', 'i');
			for(var i= 0, j= code.length; i<j; i++)
			{
				if(regEx.test(code[i]+' '))
				{
					o.innerHTML+= '<div style="background-image:url('+Mind.Properties.imagesPath+'/blue_marker.png); width:9px; height:9px;"><!-- --></div>';
					fullMatch++;
				}else{
						if(code[i].indexOf(filter) != -1)
						{
							partialMatch++;
							o.innerHTML+= '<div style="background-image:url('+Mind.Properties.imagesPath+'/light_blue_marker.png); width:9px; height:9px;"><!-- --></div>';
						}else{
							 	o.innerHTML+= '<br/>';
							 }
					 }
			}
		};
		
		legend[0].innerHTML= fullMatch+' matches';
		legend[1].innerHTML= partialMatch+' matches';
	},
	Code: function(newCode, b){
		if(b)
			document.getElementById('mindEditor').value= newCode;
		else
			document.getElementById('mindEditor').value+= newCode;
		this.Typing(document.getElementById('mindEditor').value);
	},
	ClearSearch: function(){
		document.getElementById('mindEditor_lineNumberColumnArea').innerHTML= '<br/>';
	}
};
/*
var x= new RegExp('^( )?outro(\n| |\;|\.){1,}', 'i');
alert(x.test('aaaoutro pode haver aboutrocd'));
*/

