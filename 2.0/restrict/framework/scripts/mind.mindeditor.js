Mind.MindEditor= {
	ActivedColorPicker: false,
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
			Mind.Theme.userConfig.fontSize = s+'px';
		}
	},
	Print: function(){
		alert('Here, it should print the code');
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
			//setTimeout(function(){$('#colorPickerWrapper').hide()}, 500);
			//setTimeout(function(){document.getElementById('colorPickerWrapper').style.display='none'}, 500);
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
		v= v.replace(/[\"\'@#$%&\(\)\[\]\{\}\+\=\§]/ig, '');
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
			if(this.lineNumber < oldLnNmro)
			{
				var ln= lineNumberColumn.innerHTML.split(/<br>/i);
				for(var i=oldLnNmro; i>this.lineNumber; i--)
				{
					ln.pop();
				}
				lineNumberColumn.innerHTML= ln.join('<br>');
			}else{
					oldLnNmro++;
					for(var i=oldLnNmro; i<this.lineNumber+1; i++)
						lineNumberColumn.innerHTML+= '<br>'+i;
				 }
		}
		Mind.Project.Change();
	},
	SetUserConfig : function(){
		// Tamanho da fonte
		Mind.MindEditor.SetSize(Mind.Theme.userConfig.fontSize);
		//Marca como selecionado a opção armazenada
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
		
		// Ajusta a posição do painel esquerdo
		//$("#vertical_resizable_bar_left").css("left",Mind.Theme.userConfig.leftPanelPos);
		//Mind.Panel["left"].Adjust();
		Mind.Panel["left"].Size(Mind.Theme.userConfig.leftPanelPos);					
		Mind.Panel["right"].Size(parseInt(document.body.clientWidth) - parseInt(Mind.Theme.userConfig.rightPanelPos));
		Mind.Panel["bottom"].Size(parseInt(document.body.clientHeight) - parseInt(Mind.Theme.userConfig.bottomPanelPos));
		Mind.Panel['bottom'].Adjust();
		Mind.Panel["center"].Adjust();
		// Ajusta a posição do painel direito		
		//$("#vertical_resizable_bar_right").css("left",Mind.Theme.userConfig.rightPanelPos);				
		//document.getElementById("mind_layout_right").style.width = parseInt(document.body.clientWidth) - parseInt(Mind.Theme.userConfig.rightPanelPos) + "px";
		
		
	}
};