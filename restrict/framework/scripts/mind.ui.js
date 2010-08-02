/*!
 * Mind.UI JavaScript Library
 *
 * Copyright (c) 2009 theWebMind.org
 * http://thewebmind.org
 *
 * Date: 2009-06-15 19:34:21 
 */
 
var Mind = {};
Mind.Window = window;

Mind.Recent = {
	current : {
		itens : new Array()
	},
	length: 0,
	Add : function(name){
		if(!Mind.Recent.current.itens)
			this.Load();
		var nAr= Array();
		nAr.push(name);
		
		if(Mind.Recent.current.itens)
			for(var i=0, l= Mind.Recent.current.itens.length; i<l && i<10; i++)
			{
				if(Mind.Recent.current.itens[i] != name)
					nAr.push(Mind.Recent.current.itens[i]);
			}
		Mind.Recent.current.itens= nAr;
		gravaCookie('Mind.Recent',JSON.stringify(Mind.Recent.current.itens));
		return;
	},
	GetRecents : function(){
		var cookie = leCookie('Mind.Recent');
		cookie = cookie!=null? cookie: false;
		if(cookie){
			return JSON.parse(cookie);
		}else
			return false;
	},
	Init:function(){
		Mind.Recent.current.itens= false;
		Mind.Recent.current.itens = Array();
		var recents = Mind.Recent.GetRecents();
		Mind.Recent.current.itens = recents;
		return recents;
	},
	Load : function(){
		if(Mind.Recent.current.itens)
			return;
		Mind.Recent.current.itens= false;
		Mind.Recent.current.itens = Array();
		var recents = Mind.Recent.GetRecents();
		if(!recents)
			Mind.Recent.current.itens = Array();
		else
			Mind.Recent.current.itens = recents;
		Mind.Recent.current.itens = recents.length;
		
		Mind.Recent.AddToMenu();
	},
	AddToMenu : function(){
		var projects = Mind.Recent.current.itens;
		if (!projects)
			return false;
		var length = projects.length;
		document.getElementById("submenu_recent").innerHTML = "";
		var a;
		for(var i=0;i<length;i++){
			a= document.createElement('A');
			a.href= '#';
			a.className= "{action: \"Mind.Project.Load('"+projects[i]+"')\"}";
			a.innerHTML= projects[i];
			$("#submenu_recent").append(a);
		}
		if(length>0)
			$("#mind_file_menu").removeAttr('disabled');
	}
};

Mind.Progress = {
	container : new Array(),
	total : 20,
	Init : function(){
		$("#mind_progressbar").progressbar({
			value: this.total
		});		
	},
	Increment : function(q,action){
		action = action ? action : "";
		q = q ? q : 8;
		if(action=="loadProject"){
			$("#mind_progressbar").progressbar( 'value' , 95);
			setTimeout(function(){
				$("#mind_progressbar").progressbar( 'value' , 100);				
				Mind.Progress.total = 100;
			},1000);						
		}
		if(this.total < 90){
			this.total=this.total+q;
			$("#mind_progressbar").progressbar( 'value' , this.total);
			setTimeout(function(){
				Mind.Progress.Increment();
			},200);
		}
		if(Mind.Progress.total >= 90){
			setTimeout(function(){
				$("#mind_progressbar").progressbar("value",100);				
			},1000);
			setTimeout(function(){
				$("#mind_progressbar").progressbar( 'destroy' );
				$("#welcome_mind").html("Welcome!");
			},1000);
			setTimeout(function(){
				Mind.Commom.BlockerFadeOut();
			},1000);
		}
	}
};

Mind.Commom = {
	BlockerFadeOut :  function(){
		setTimeout(function(){
			$('#blocker').fadeOut("slow",function(){
				$('#blocker').html("<br>");
				$('#blocker').css("backgroundColor","");
			});					
		}, 800);
	}
};
Mind.Properties = {
	path : "",
	scriptsPath : "",
	pluginPath : "",
	showDeveloperMenu : ""
};

/*!
* Mind.Dialog
*/
Mind.Dialog = {
	Open : function(ajax,width,height,title,position,source,type){
		var elementDialog = document.createElement("DIV");
		elementDialog.id = "mind_dialog";
		elementDialog.setAttribute("class","mind_dialog");
		elementDialog.setAttribute("className","mind_dialog");
			var elementDialogMessage = document.createElement("DIV");
			elementDialogMessage.id = "mind_dialog_message";
			elementDialogMessage.setAttribute("class","mind-message");
			elementDialogMessage.setAttribute("className","mind-message");
			var elementDialogContent = document.createElement("DIV");
			elementDialogContent.id = "mind_dialog_content";
			var elementDialogIframe = document.createElement("IFRAME");
			elementDialogIframe.id = "mind_dialog_iframe";
			elementDialogIframe.setAttribute("class","iframe");
			elementDialogIframe.setAttribute("className","iframe");
		elementDialog.appendChild(elementDialogMessage);
		elementDialog.appendChild(elementDialogContent);
		elementDialog.appendChild(elementDialogIframe);
		document.body.appendChild(elementDialog);
		if(ajax){
			$("#mind_dialog_iframe").css("display","none");
			var html = $.ajax({
				url: Mind.Properties.path + "/" + source,
				async: false,
				error : function(XMLHttpRequest, textStatus, errorThrown){
					Mind.AjaxHandler.Capture(XMLHttpRequest);
				}
			}).responseText;
			$(elementDialogContent).html(html);

		}else{
			$("#mind_dialog").html("");
			$("#mind_dialog_iframe").css("display","block");
			$("#mind_dialog_iframe").attr("src",Mind.Properties.path + "/" + source);
		}	
		$(elementDialog).dialog({
			title: title,
			position: position,
			width:width,
			height:height,
			minWidth: '25',
			modal : false,
			minHeight: '25',
			closeOnEscape: true,
			hide:'bounce',
			show:'scale',
			close: function(event, ui){
				$("#mind_dialog_content").html("<img src='images/load.gif'><i>Loading...</i>");
				$("#mind_dialog_message").css("display","none");
				$(elementDialog).dialog('destroy');
			}
		});
	},
	
	OpenModal : function(ajax,width,height,title,position,source,type,callBack,buttons,noresizable){
		//buttons = buttons? true : false;
		var callBack = callBack ? callBack : function(){};
		$("#mind_dialog_content").html('Loading...');
		if(ajax){
			$("#mind_dialog_iframe").css("display","none");
			if(type!="manage"){
				var html = $.ajax({
					url: Mind.Properties.path + "/" + source,
					async: false,
					error : function(XMLHttpRequest, textStatus, errorThrown){
						Mind.AjaxHandler.Capture(XMLHttpRequest);
					}
				}).responseText;				
				setTimeout(function(){
					$("#mind_dialog_content").html(html);
				}, 1000);
			}
		}else{
			$("#mind_dialog").html("");
			$("#mind_dialog_iframe").css("display","block");
			$("#mind_dialog_iframe").attr("src",Mind.Properties.path + "/" +source);
		}	
		
		switch(type){
			case "form":
				var bt = {
					'Ok': function(){
						var requireds = 0;
						var form = $("#mind_dialog").find("form");						
						jQuery.each(form.find("input"), function(){							
							if(this.getAttribute("required") && this.value=="")
							{
								requireds +=1;
								$(this).bind("keypress",function(){
									$(this).css("border","solid 1px #666");
									$(this).parent().parent().find("td:first").find("span").remove();
								});
								if($(this).parent().parent().find("td:first").find("span").html() == null)
								{
									$(this).css("border","solid 1px red");
									$(this).parent().parent().find("td:first").prepend("<span style='color:red;font-size:12px'>*</span>");
								}								
							}
						});
						
						jQuery.each(form.find("select"), function(){
							if(this.getAttribute("required") && this.value=="")
							{
								requireds +=1;
								$(this).bind("click",function(){
									$(this).css("border","solid 1px #666");
									$(this).parent().parent().find("td:first").find("span").remove();
								});
								if($(this).parent().parent().find("td:first").find("span").html() == null)
								{
									$(this).css("border","solid 1px red");
									$(this).parent().parent().find("td:first").prepend("<span style='color:red;font-size:12px'>*</span>");
								}
							}
						});
						
						if(requireds==0){
							var formdata = form.serialize();
							 $.ajax({
								type: "POST",
								url: form.attr('action'),
								data: formdata,
								success: function(msg){
									eval(msg);
								},
								error : function(XMLHttpRequest, textStatus, errorThrown){
									Mind.AjaxHandler.Capture(XMLHttpRequest);
								}
							 });
						}else{
							return false;
							//dMind.Dialog.ShowModalMessage(message,"error");
						}
					},
					Cancel: function() {
						$(this).dialog('close');
					}
				};
			break;
			case "manage" :
				$("#mind_dialog_content").html("");
				var jsonData = false;
				var contentList = "";
				var listPanel = document.createElement("DIV");
				listPanel.id = "list_panel";
				var contentPanel = document.createElement("DIV");
				contentPanel.id = "content_panel";
				
				$("#mind_dialog_content").append(listPanel);
				$("#mind_dialog_content").append(contentPanel);
				$("#list_panel").addClass("manageList");
				$("#content_panel").addClass("manageContent");				
				$.ajax({
				   type: "POST",
				   url: Mind.Properties.path + "/" + source,
				   data: "action=list",
				   success: function(content){
						jsonData = JSON.parse(content);
						objList = jsonData.list;					
						for(var i=0;i< objList.length;i++){							
							var tempDiv = document.createElement("DIV");
							tempDiv.innerHTML = objList[i].name;
							$(tempDiv).addClass("manageListContent");
							tempDiv.setAttribute("code",objList[i].code);
							tempContentDiv = document.createElement("DIV");
							tempContentDiv.id = "content_" + objList[i].code;														
							$(tempContentDiv).addClass("manageListContentOpened");
														
							for(var x in objList[i]['info'])
							{
								tempContentDiv.innerHTML+= x+': '+objList[i]['info'][x]+'<br>';
							}
							for(var x in objList[i])
							{								
								tempContentDiv.innerHTML+= x+': '+objList[i][x]+'<br>';
							}
							
							tempDiv.onclick = function(){								
								$("#content_panel").children().css("display","none");
								$("#list_panel").children().removeClass("manageListContentSelected");
								$(this).addClass("manageListContentSelected");
								document.getElementById("content_" + this.getAttribute("code")).style.display = "block";								
							};
							$("#list_panel").append(tempDiv);
							$("#content_panel").append(tempContentDiv);
						}
				   },
				   error : function(XMLHttpRequest, textStatus, errorThrown){
						Mind.AjaxHandler.Capture(XMLHttpRequest);
					}
				}).responseText;
				
				if(buttons){
					var bt = {
						'Ok': function() {
							alert("a");
						},
						Cancel: function() {
							$(this).dialog('close');
						}
					};
				}else{
					bt = null;
				}
			break;
		}
		$("#mind_dialog").dialog({
			title: title,
			position: position,
			width:width,
			height:height,
			minWidth: '25',
			modal: true,
			resizable: !noresizable,
			minHeight: '25',
			closeOnEscape: true,
			hide:'bounce',
			show:'scale',
			open: function(event, ui){
				callBack;
			},
			close: function(event, ui){
				$("#mind_dialog_content").html("<img src='images/load.gif'><i>Loading...</i>");
				$("#mind_dialog_message").css("display","none");
				$("#mind_dialog").dialog('destroy');
			},
			buttons: buttons ? bt : null
		});
	},
	
	CloseModal : function(){
		//alert($("#mind_dialog").html());
		$("#mind_dialog").dialog( 'close' );
	},
	
	ShowData : function(obj,title, w, h, b, callback){
		var tempDialog = document.createElement("DIV");
		tempDialog.setAttribute("className","mind-data");
		var d = new Date();		
		tempDialog.id = "temp_dialog_" + d.getMilliseconds();
		document.body.appendChild(tempDialog);
		b= !b;
		if(b)
			b=  {
					'Ok': function(){
						$(this).dialog('close');
						if(callback)
							callback();
					}
				};
				
		$(tempDialog).html(obj);
		$(tempDialog).dialog({
			title: title ? title : "Mind Data",
			position: "middle",
			width: w||'400',
			stack: true, 
			height: h||'145',
			resizable: true,
			modal : false,
			closeOnEscape: true,
			close: function(event, ui){					
				$(this).dialog('destroy');				
			},
			buttons: b
		});
		return $(tempDialog);
	},
	CloseData: function(o){
		o.dialog('close');
		o.dialog('destroy');
	},
	
	ShowError : function(obj){	
		$("#mind_alert").html(obj.title + "<hr>" + obj.message + (obj.tip ? "<b><br><br>Tip:</b>" + obj.tip : ""));
		$("#mind_alert").dialog({			
			title: obj.type,
			position: 'midle',
			width:'400',
			height:'145',	
			resizable: false,
			modal : true,
			closeOnEscape: true,
			close: function(event, ui){								
				$(this).dialog('destroy');
			},
			buttons: {
						'Ok': function() {
							$(this).dialog('close');
						}
					}
		});
	},
	
	ShowAlert : function(message,title){
		$("#mind_alert").html(message);
		$("#mind_alert").dialog({			
			title: title ? title : "Mind Alert",
			position: 'midle',
			width:'400',
			height:'230',	
			resizable: false,
			modal : true,
			closeOnEscape: true,
			close: function(event, ui){								
				$(this).dialog('destroy');
			},
			buttons: {
						'Ok': function() {
							$(this).dialog('close');
						}
					}
		});
	},

	ShowModalMessage : function(message,type){
		$("#mind_dialog_message").css("display","block");
		$("#mind_dialog_message").addClass("mind-message-"+ type);
		$("#mind_dialog_message").html('<div onclick="$(\'#mind_dialog_message\').fadeOut()" class="button-message" id="button_modal_message"></div>' + message);		
		setTimeout(function(){$("#mind_dialog_message").fadeOut();},8000);
	},
	
	ShowMessage : function(title, message, timeout, onClose){
		title = title ? title : "";
		message = message ? message : "";
//		timeout = timeout ? timeout : 100000;
		onClose = onClose ? onClose : null;
		$.growlUI(title,message,timeout,onClose);
		if(timeout) // aqui
			setTimeout(function(){Mind.Dialog.CloseMessage();}, timeout);
	},
	
	CloseMessage : function(){
		$.unblockUI();
	}
};
//setTimeout(function(){Mind.Dialog.ShowMessage('TESTE', false, false, false);}, 6000);
/*		PANELS		*/
Mind.Panel = new Array();
Mind.Panels = {
	Init : function(){		// called onload
		Mind.Panel["left"] = {
			htmlElement : document.getElementById("mind_layout_left"),
			Content: document.getElementById("mind_layout_left").getElementsByTagName('DIV')[0],
			DefaultSize:200,
			CurrentSize:200,
			closed:false,
			opened:true,
			Open : function(){
				$(this.htmlElement).animate({width:this.CurrentSize});
				$(Mind.Panel['center'].htmlElement).animate({left:this.CurrentSize});
				$(".vertical-resizable-bar-left").animate({left:this.CurrentSize}, function(){Mind.Panel['center'].Adjust();Mind.Theme.userConfig.leftPanelPos = $("#vertical_resizable_bar_left").css("left");});				
				this.closed= false;
				this.opened= true;
			},
			/*Hide : function(){
				this.CurrentSize= this.htmlElement.offsetWidth;
				this.Size(1);
			},*/
			Close : function(){
				this.CurrentSize= this.htmlElement.offsetWidth;
				this.closed= true;
				this.opened= false;
				$(this.htmlElement).animate({width:0});
				$(Mind.Panel['center'].htmlElement).animate({left:5});
				$(".vertical-resizable-bar-left").animate({left:0}, function(){Mind.Panel['center'].Adjust();Mind.Theme.userConfig.leftPanelPos = $("#vertical_resizable_bar_left").css("left");});
			},
			ShowAttributes: function(o, table){
				var o= o.parentNode.getElementsByTagName('DIV')[0];
				if(o.style.display == 'none')
				{
					var str= '';
					for(att in Mind.Project.knowledge.tables[table].attributes)
					{
						var img= 'losango.gif';
						if(Mind.Project.knowledge.tables[table].attributes[att].pk)
							img= 'little_key.gif';
						str+= "<div style=''><span onclick='$($(this.parentNode).find(\"div\")[0]).toggle();'><nobr>&nbsp; | <img src='"+Mind.Properties.imagesPath+"/"+img+"'> "+att+'</nobr></span>';
							str+= "<div style='display:none;'><nobr>";
								str+='&nbsp; | | -<b>Name: </b>'+Mind.Project.knowledge.tables[table].attributes[att].name+'<br/>';
								str+='&nbsp; | | -<b>Type: </b>'+Mind.Project.knowledge.tables[table].attributes[att].type;
									if(Mind.Project.knowledge.tables[table].attributes[att].size != 0)
										str+= '('+Mind.Project.knowledge.tables[table].attributes[att].size+')<br/>';
									else
										str+= '<br/>';
								str+='&nbsp; | | -<b>Required: </b>'+(Mind.Project.knowledge.tables[table].attributes[att].required? 'Yes': 'No') +'<br/>';
								str+='&nbsp; | | -<b>Comment</b>: '+(Mind.Project.knowledge.tables[table].attributes[att].comment || '')+'<br/>';
								str+='&nbsp; | | -<b>Default</b>: '+Mind.Project.knowledge.tables[table].attributes[att].defaultValue+'<br/>';
								str+='&nbsp; | | -<b>Mask: </b>'+(Mind.Project.knowledge.tables[table].attributes[att].mask || 'none')+'<br/>';
								str+='&nbsp; | | -<b>Options: </b><br/>';
								if(Mind.Project.knowledge.tables[table].attributes[att].options
									&&
								   Mind.Project.knowledge.tables[table].attributes[att].options.length > 0)
								{
									for(var c= 0, j= Mind.Project.knowledge.tables[table].attributes[att].options.length; c<j; c++)
									{
										str+= '&nbsp; | | &nbsp;-><nobr>'+Mind.Project.knowledge.tables[table].attributes[att].options[c][0];
										str+= '= '+Mind.Project.knowledge.tables[table].attributes[att].options[c][1]+'</nobr><br/>';
									}
								}
							str+= "</nobr></div>";
						str+= '</div>';
					}
					for(var i=0; i < Mind.Project.knowledge.tables[table].foreignKeys.length; i++)
					{
						str+= "<div style=''><span onclick='$($(this.parentNode).find(\"div\")[0]).toggle();'><nobr>&nbsp; | <img src='"+Mind.Properties.imagesPath+"/gray_losango.gif'> "+Mind.Project.knowledge.tables[table].foreignKeys[i][0] +'</nobr></span>';
							str+= "<div style='display:none;'><nobr>";
								str+= "&nbsp; | | - Foreign Key: "+Mind.Project.knowledge.tables[table].foreignKeys[i][0]+'<br/>';
								str+= "&nbsp; | | - References: "+Mind.Project.knowledge.tables[table].foreignKeys[i][1]+'<br/>';
							str+= "</nobr></div>";
						str+= '</div>';
					}
					o.innerHTML= str;
					o.style.display= '';
				}else{
						o.innerHTML= ' ';
						o.style.display= 'none';
					 }
			},
			Update: function (objList, flag){
				var str= '';
				if(objList && !flag)
				{
					str+= "<div class='projectListContainer'>";
					for(var x in objList)
					{
						str+= "<div class='projectListItemLine' projectName="+objList[x].name+" onmouseover='this.className=\"projectListItemLineOver\"' onmouseout='this.className=\"projectListItemLine\"' ondblclick=\"Mind.Project.Load('"+objList[x].name+"')\">";
						str+= "<div class='projectListItem'>"+objList[x].name+"</div><div class='projectListButtons'>";
						str+= "<img src='"+Mind.Properties.imagesPath+"/bt_prop_over.gif' onmouseover='showtip(this, event, \"Properties\")' class='projectListItemProp'  onclick=\"Mind.Project.Properties('"+objList[x].name+"')\">";
						str+= "<img src='"+Mind.Properties.imagesPath+"/bt_backup_over.gif' onmouseover='showtip(this, event, \"Load Project\")' onclick=\"Mind.Project.Load('"+objList[x].name+"')\">";
						str+= "</div></div>";
					}
					str+= "</div>";
				}else if(objList)
					 {
						str+= "<div class='projectListContainer'>";
						str+= "<div class='projectListItemLine'>";
						str+= Mind.Project.attributes.name;
						str+= "</div></div>";
						str+= "<div style='margin-left:4px; color:#aaa;  font-size:11px; line-height:18px;'>";
						var i=0;
						
						for(var table in objList.tables)
						{
							str+= "<div> <span onclick='Mind.Panel[\"left\"].ShowAttributes(this, \""+table+"\")' >";
								str+= "<img src='"+Mind.Properties.imagesPath+"/entity.png' align='left'> "+table+'</span>';
								str+= "<div style='display:none;'> ";
								str+= "</div>";
							str+= "</div>";
						}
						if(objList.tables.length > 0)
						{
							str+='<hr style="width:100%;" />';
							str+= 'Files';
						}
						str+= "</div>";
					 }else
						str= '<i>No Projects yet</i>';
				
				this.Content.innerHTML= str;
			},
			Adjust : function(event){				
				$(".vertical-resizable-bar-left").css("height", Mind.Panel["bottom"].htmlElement.offsetTop - 30);
				this.htmlElement.style.height = Mind.Panel["bottom"].htmlElement.offsetTop - 30;
				this.Content.style.height = Mind.Panel["bottom"].htmlElement.offsetTop - 30;
			},
			Size : function(nS){
				this.htmlElement.style.width= nS;
				this.Content.style.width= nS;
				$(".vertical-resizable-bar-left").css("left", nS);
			},
			Reset : function (){
				this.Size(this.DefaultSize);
				var h= document.body.clientHeight-Mind.Panel["bottom"].htmlElement.offsetHeight-30;
				this.htmlElement.style.height= h;
				this.Content.style.height= h;
				$(".vertical-resizable-bar-left").css("height", h);
				Mind.Panel['bottom'].Adjust();
			}
		};
		Mind.Panel['right'] = {
			htmlElement : document.getElementById("mind_layout_right"),
			Content : document.getElementById("mind_layout_right").getElementsByTagName('DIV')[0],
			DefaultSize : 200,
			closed:false,
			opened:true,
			Open : function(){
				$(this.htmlElement).animate({width:this.CurrentSize,left:document.body.clientWidth-this.CurrentSize});
				$(".vertical-resizable-bar-right").animate({left:document.body.clientWidth-this.CurrentSize},function(){Mind.Theme.userConfig.rightPanelPos = $("#vertical_resizable_bar_right").css("left");});
				$(Mind.Panel['center'].htmlElement).animate({width:(document.body.clientWidth- this.CurrentSize)}, function(){Mind.Panel['center'].Adjust();});				
				this.closed= false;
				this.opened= true;
			},			
			Close : function(){
				this.CurrentSize= this.htmlElement.offsetWidth;
				$(this.htmlElement).animate({width:0,left:document.body.clientWidth-5});
				$(".vertical-resizable-bar-right").animate({left:document.body.clientWidth-5},function(){$(".vertical-resizable-bar-right").animate({left:document.body.clientWidth-this.CurrentSize},function(){Mind.Theme.userConfig.rightPanelPos = $("#vertical_resizable_bar_right").css("left");});});
				$(Mind.Panel['center'].htmlElement).animate({width:(document.body.clientWidth-Mind.Panel['center'].htmlElement.offsetLeft)}, function(){Mind.Panel['center'].Adjust();});
				Mind.Panel['center'].Adjust();
				this.closed= true;
				this.opened= false;
			},
			Update: function (str){
				if(str)
					this.Content.innerHTML= str;
				else
					this.Content.innerHTML= "<div class='nothing'><br></div>";
			},
			Adjust : function(event){
				$(".vertical-resizable-bar-right").css("height", Mind.Panel["bottom"].htmlElement.offsetTop - 30);
				this.htmlElement.style.height = Mind.Panel["bottom"].htmlElement.offsetTop - 30;
				this.Content.style.height = Mind.Panel["bottom"].htmlElement.offsetTop - 30;
			},
			Size : function(nS){
				this.htmlElement.style.width= nS;
				this.htmlElement.style.left= document.body.clientWidth - nS;
				this.Content.style.left= (document.body.clientWidth - nS);
				this.Content.style.width= nS;
				$(".vertical-resizable-bar-right").css("left", document.body.clientWidth - nS -5);
			},
			Reset : function (){
				this.Size(this.DefaultSize);
				var h= document.body.clientHeight-Mind.Panel["bottom"].htmlElement.offsetHeight-30;
				this.htmlElement.style.height= h;
				this.Content.style.height= h;
				$(".vertical-resizable-bar-right").css("height", h);
			}
		};

		Mind.Panel['center'] = {
			htmlElement : document.getElementById('mind_layout_center'),
			Content : document.getElementById('mind_layout_center').getElementsByTagName('DIV')[0],
			Open : function(){
			},
			Close : function(){
			},
			Adjust : function(){
				this.htmlElement.style.left = Mind.Panel["left"].htmlElement.offsetWidth + 5;
				this.htmlElement.getElementsByTagName('DIV')[0].style.width= document.body.clientWidth- (Mind.Panel["left"].htmlElement.offsetWidth + Mind.Panel['right'].htmlElement.offsetWidth) - 5;
				this.htmlElement.style.width= this.htmlElement.getElementsByTagName('DIV')[0].offsetWidth;
				this.htmlElement.getElementsByTagName('DIV')[0].style.height= document.body.clientHeight- Mind.Panel["bottom"].htmlElement.offsetHeight-30;
				this.htmlElement.style.height= this.htmlElement.getElementsByTagName('DIV')[0].offsetHeight;
				//document.getElementById('layout').style.width= "200px";//document.body.clientWidth;
			},
			Update: function (str){
				this.Content.innerHTML= str;
			},
			Size : function(nS){
				this.htmlElement.style.width= nS;
				this.htmlElement.style.left= document.body.clientWidth - nS;
				this.Content.style.left= document.body.clientWidth - nS;
				this.Content.style.width= nS;
				$(".vertical-resizable-bar-right").css("left", document.body.clientWidth - nS);
			},
			Reset : function (){
			}
		};
		
		Mind.Panel['bottom'] = {
			fullSize: false,
			htmlElement: document.getElementById("mind_layout_bottom"),
			Content: document.getElementById("mind_layout_bottom").getElementsByTagName('DIV')[0],
			DefaultSize: 150,
			closed:false,
			opened:true,
			Open: function(){
				$(this.htmlElement).animate({height:this.CurrentSize,top:document.body.clientHeight-this.CurrentSize});
				$(".horizontal-resizable-bar").animate({top:document.body.clientHeight-this.CurrentSize});
				$(Mind.Panel['center'].htmlElement).animate({height:(document.body.clientHeight-this.CurrentSize)}, function(){
					//Mind.Panel['left'].Adjust();
					//Mind.Panel['right'].Adjust();
					//Mind.Panel['center'].Adjust();
					Mind.Panel['bottom'].Adjust();
					Mind.Theme.userConfig.bottomPanelPos = $(".horizontal-resizable-bar").css("top");
				});
				this.closed= false;
				this.opened= true;
			},
			Focus: function (tab){
				tab= '#outputPanel_'+tab;
				var exp= '[href='+ tab+ ']';				
				$(exp).trigger('click');
			},
			Close: function(){
				
				this.CurrentSize= this.htmlElement.offsetHeight;
				var x= document.body.clientHeight-5;
				$(this.htmlElement).animate({height:0,top:x});
				$(".horizontal-resizable-bar").animate({top:x});
				$(Mind.Panel['center'].htmlElement).animate({height:(x)}, function(){
					Mind.Panel['bottom'].Adjust();
					Mind.Theme.userConfig.bottomPanelPos = $(".horizontal-resizable-bar").css("top");
				});
				this.closed= true;
				this.opened= false;
			},
			Update: function (str){
				this.Content.innerHTML= str;
			},
			Adjust: function(){				
				/* $("#"+Mind.Panel['bottom'].htmlElement.id+" [autoresize]").css('height', (
																							(this.getAttribute('autoresize')!='true')? this.getAttribute('autoresize'): Mind.Panel['bottom'].htmlElement.offsetHeight-42
																						 )
																			  );*/
				$("#"+Mind.Panel['bottom'].htmlElement.id+" [autoresize]").each(function(){
					this.style.height= Mind.Panel['bottom'].htmlElement.offsetHeight-42;
					if(this.getAttribute('autoresize') != 'true')
						this.style.height= parseInt(this.style.height) - parseInt(this.getAttribute('autoresize'));
				});
				if(document.getElementById('mindEditor_Container'))
				{
					document.getElementById('mindEditor_Container').style.height= ((document.body.clientHeight - Mind.Panel['bottom'].htmlElement.offsetHeight) -
																					document.getElementById('MindEditorFooterTools').offsetHeight)-60;
					document.getElementById('mindEditor').style.height= '100%';
				}
				Mind.Panel['left'].Adjust();
				Mind.Panel['right'].Adjust();
				Mind.Panel['center'].Adjust();
			},
			Size : function(nS){
				this.htmlElement.style.height= nS;
				this.htmlElement.style.top= document.body.clientHeight - nS;
				this.Content.style.top= document.body.clientHeight - nS;
				this.Content.style.height= nS;
				$(".horizontal-resizable-bar").css("top", document.body.clientHeight - nS);
			},
			Reset : function (){
				this.Size(this.DefaultSize);
			}
		};
		
		/*Mind.Panel["center"].htmlElement.getElementsByTagName('DIV')[0].style.width= document.body.clientWidth-(
																					Mind.Panel["left"].htmlElement.offsetWidth +
																					Mind.Panel['right'].htmlElement.offsetWidth+
																					10
																				);*/
		$(".vertical-resizable-bar-left").bind("mousedown", function(){
			//$('#blocker').show();
			$(document).bind("mousemove",function(event){
				if(event.clientX < 0)
				{
					Mind.Panel["left"].Size(0);
					return false;
				}
				//if(event.clientX < document.body.clientWidth - Mind.Panel['right'].htmlElement.offsetWidth-200)
				//{
					//window.status= ;
					if(document.body.clientWidth - (event.clientX + Mind.Panel['right'].htmlElement.offsetWidth) < 200)
					{
						Mind.Panel["left"].Size(document.body.clientWidth - (Mind.Panel['right'].htmlElement.offsetWidth + 201));
						Mind.Panel["center"].Adjust();
						return;
					}
					
					if(Mind.Panel["left"].htmlElement.style.display=='none')
						Mind.Panel["left"].htmlElement.style.display= '';
					Mind.Panel["left"].htmlElement.style.width = event.clientX;
					Mind.Panel["left"].htmlElement.getElementsByTagName('DIV')[0].style.width = event.clientX;
					$("#vertical_resizable_bar_left").css("left",event.clientX);
					$('#blocker').css("display","block");
					$('#blocker').css("cursor","w-resize");
					Mind.Panel["center"].Adjust();
				//}
			});
			$(document).bind("mouseup", function(){
				//Mind.Theme.userConfig.backgroundColor
				Mind.Theme.userConfig.leftPanelPos = $("#vertical_resizable_bar_left").css("left");
				$(this).unbind("mousemove");
				$(this).unbind("mouseup");
				$('#blocker').css("display","none");
				$('#blocker').css("cursor","");
			});
			
		});

		$(".vertical-resizable-bar-right").bind("mousedown", function(){			
			$(document).bind("mousemove",function(event){
				//if(event.clientX > Mind.Panel["left"].htmlElement.offsetWidth+200)
				//{
					if(event.clientX>document.body.clientWidth-5)
					{
						Mind.Panel['right'].Size(5);
						Mind.Panel["center"].Adjust();
						return false;
					}
					var x= document.body.clientWidth - event.clientX;
					
					if(Mind.Panel["center"].htmlElement.offsetLeft+200 >event.clientX)
					{
						Mind.Panel['right'].Size((document.body.offsetWidth - (201 + Mind.Panel["center"].htmlElement.offsetLeft)));
						Mind.Panel["center"].Adjust();
						return;
					}
					
					if(Mind.Panel['right'].htmlElement.style.display=='none')
						Mind.Panel['right'].htmlElement.style.display= '';
					
					//	window.status= Mind.Panel["center"].htmlElement.offsetLeft+' + '+Mind.Panel["center"].htmlElement.offsetWidth+' - '+document.body.clientWidth+' = '+( document.body.offsetWidth - (201 + Mind.Panel["center"].htmlElement.offsetLeft));
					
					
					Mind.Panel['right'].htmlElement.style.width = x-5;
					Mind.Panel['right'].htmlElement.style.left = document.body.clientWidth-x+5;
					//Mind.Panel['right'].htmlElement.getElementsByTagName('DIV')[0].style.width = x;
					Mind.Panel['right'].Content.style.width = x-5;
					Mind.Panel['right'].Content.style.left = document.body.clientWidth-x+5;
					$("#vertical_resizable_bar_right").css("left",event.clientX);											
					$('#blocker').css("display","block");
					$('#blocker').css("cursor","w-resize");
					Mind.Panel["center"].Adjust();
				//}
			});
			$(document).bind("mouseup", function(){
				Mind.Theme.userConfig.rightPanelPos = $("#vertical_resizable_bar_right").css("left");
				$(this).unbind("mousemove");
				$(this).unbind("mouseup");
				$('#blocker').css("display","none");
				$('#blocker').css("cursor","");
			});
		});		
		$(".horizontal-resizable-bar").bind("mousedown", function(){
			$(document).bind("mousemove",function(event){
				Mind.Panel['right'].Adjust(event);
				Mind.Panel["left"].Adjust(event);
				if(event.clientY > document.body.clientHeight-5)
				{
					Mind.Panel["bottom"].Size(5);
					Mind.Panel["center"].Adjust(event);
					return;
				}
				if(event.clientY > 200)
				{
					if(Mind.Panel["bottom"].htmlElement.style.display=='none')
						Mind.Panel["bottom"].htmlElement.style.display= '';
					var x= document.body.clientHeight - event.clientY;
					Mind.Panel["bottom"].htmlElement.style.height = x;
					Mind.Panel["bottom"].htmlElement.getElementsByTagName('DIV')[0].style.height = x;
					$("#horizontal_resizable_bar").css("top",event.clientY);
					Mind.Panel["bottom"].htmlElement.style.top = event.clientY;
					$('#blocker').css("display","block");
					$('#blocker').css("cursor","n-resize");
				}else{
						/*Mind.Panel["left"].htmlElement.style.height= y;
						Mind.Panel["left"].Content.style.height= y;
						Mind.Panel['right'].htmlElement.style.height= y;
						Mind.Panel['right'].Content.style.height= y;*/
						Mind.Panel["bottom"].Size(document.body.clientHeight - 201);
					 }
				Mind.Panel["center"].Adjust();
			});
			$(document).bind("mouseup", function(){
				Mind.Theme.userConfig.bottomPanelPos = $(".horizontal-resizable-bar").css("top");
				$(this).unbind("mousemove");
				$(this).unbind("mouseup");
				$('#blocker').css("display","none");
				$('#blocker').css("cursor","");
				Mind.Panel['bottom'].Adjust();
			});
		});
		
		$(".vertical-resizable-bar-left .barBt").click(function(){			
			if(Mind.Panel['left'].htmlElement.offsetWidth == 0) 
				Mind.Panel['left'].Open();
			else
				Mind.Panel['left'].Close();
		});
		$(".vertical-resizable-bar-right .barBt").click(function(){			
			if(Mind.Panel['right'].htmlElement.offsetWidth == 0) 
				Mind.Panel['right'].Open();
			else
				Mind.Panel['right'].Close();
		});
		$(".horizontal-resizable-bar .barBt").click(function(){			
			if(Mind.Panel['bottom'].htmlElement.offsetHeight == 0) 
				Mind.Panel['bottom'].Open();
			else
				Mind.Panel['bottom'].Close();
		});
		Mind.Panels.Reset();
		//Mind.Panel["center"].Adjust();
	},
	
	HideLefPanel : function(){
		alert("Hide the left Panel");
	},
	Open : function(panel){
		if(panel)
			Mind.Panel[panel].Open();
		else{
				Mind.Panel['left'].Open();
				Mind.Panel['right'].Open();
				Mind.Panel['bottom'].Open();
			}
	},
	Close : function(panel){
		if(panel)
			Mind.Panel[panel].Close();
		else{
				Mind.Panel['left'].Close();
				Mind.Panel['right'].Close();
				Mind.Panel['bottom'].Close();
			}
	},
	Reset : function (){
		Mind.Panel['bottom'].Reset();
		Mind.Panel['left'].Reset();
		Mind.Panel['right'].Reset();
		Mind.Panel["center"].Adjust();
	},
	SetFull : function(){		
		if($("#horizontal_resizable_bar").css("top") != "0px"){
			$("#horizontal_resizable_bar").css("top","0px");
			Mind.Panel["bottom"].htmlElement.style.top = "28px";		
			$("#mind_layout_bottom").css("height",document.body.clientHeight);
			Mind.Panel['bottom'].Size(document.body.clientHeight);
			Mind.Panel['bottom'].Adjust();			
		}else{
			Mind.Panel['bottom'].Size(150);
			Mind.Panel['bottom'].Adjust();			
		}		
	}
};

Mind.ProjectPane= {
	Update:function (objList){
		var str= '';
		for(var x in objList)
		{
			str+= "<div class='projectListItemLine' onmouseover='this.className=\"projectListItemLineOver\"' onmouseout='this.className=\"projectListItemLine\"'>";
			str+= "<div class='projectListItem'>"+objList[x].name+"</div><div class='projectListButtons'>";
			str+= "<img src='"+Mind.Properties.imagesPath+"/bt_prop_over.gif' onmouseover='showtip(this, event, \"Properties\")'>";
			str+= "<img src='"+Mind.Properties.imagesPath+"/bt_backup_over.gif' onmouseover='showtip(this, event, \"Load Project\")'>";
			str+= "</div></div>";
		}
		document.getElementById('projectsPane').innerHTML= str;
	}
};
Mind.Temp= Array();
Mind.Components= Array();
Mind.Component= {
	onLoad: function(){return this;},
	Parse: function(msg){
		if(msg=='false')
			Mind.Dialog.ShowAlert('Error when looking for the component(s)!');
		else{
				var retObj= JSON.parse(msg);
				var ret= '';
				for(var x in retObj)
				{
					retObj[x]= JSON.parse(retObj[x]);
					Mind.Components[x]= retObj[x];
				}
			}
		return retObj;
	},
	Load: function(){		
		var args= new Array();
		for(var i=0; i<arguments.length-1; i++){
			args.push(arguments[i]);
		}
		var args = JSON.stringify(args);
		$.ajax({
			type: "POST",
			url: Mind.Properties.path+'/components.php',
			data:'component='+args,
			success: arguments[arguments.length-1],
			error : function(XMLHttpRequest, textStatus, errorThrown){
				Mind.AjaxHandler.Capture(XMLHttpRequest);
			}
		});
	},
	Ask: function(){		
		var args= new Array();
		for(var i=0; i<arguments.length-1; i++){
			args.push(arguments[i]);
		}
		var args = JSON.stringify(args);
		$.ajax({
			type: "POST",
			async:false,
			url: Mind.Properties.path+'/components.php',
			data:'component='+args,
			success: arguments[arguments.length-1],
			error : function(XMLHttpRequest, textStatus, errorThrown){
				Mind.AjaxHandler.Capture(XMLHttpRequest);
			}
		});
	}
};
Mind.Window.onresize = function(event){
	Mind.Panels.Reset();
};

Mind.AjaxHandler = {
	AjaxErrors : new Array(),
	counterErrors : 0,
	Capture : function(XMLHttpRequest){
		Mind.AjaxHandler.counterErrors++;
		$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('enable', 0).tabs('enable', 1).tabs('enable', 2).tabs('enable', 3).tabs('enable', 4).tabs('enable', 5);
		$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('option', 'selected', 4);
		Mind.Dialog.CloseModal();
		Mind.Dialog.CloseMessage();
		for(i in XMLHttpRequest){
			switch(i){
				case "status":
				case "readyState":
				case "responseText":
				case "statusText":
					Mind.AjaxHandler.AjaxErrors[i]=XMLHttpRequest[i];
				break;
			}
		}
		var date = new Date();
		var content = "<div style='background:white;border:solid 1px gray;padding:8px;margin-bottom:4px;font-faily:tahoma;font-size:11px'>" +
		"<div class='ui-corner-all' style='width:200px;background:#fae48d;border:solid 1px gray;padding:2px'>" + date.toGMTString() + "</div>" + 
		"<div style='padding:5px;'>ReadyState:<span style='color:red'> " + Mind.AjaxHandler.AjaxErrors["readyState"] + "</span></div>" +
		"<div style='padding:5px;'>Status: <span style='color:red'>"  + Mind.AjaxHandler.AjaxErrors["status"] + "</span></div>" + 
		"<div style='padding:5px;'>StatusText: <span style='color:red'>"  + Mind.AjaxHandler.AjaxErrors["statusText"] + "</span></div>" +
		"<div style='padding:5px;'>ResponseText:" + Mind.AjaxHandler.AjaxErrors["responseText"] + "</div></div>";
		var error = Mind.AjaxHandler.counterErrors == 1 ? Mind.AjaxHandler.counterErrors+" Error": Mind.AjaxHandler.counterErrors+" Errors";
		document.getElementById("outputPanel_DebugTab_Label").innerHTML = "Debug <span style='font-weight:bold;color:red;font-size:12px;font-family:tahoma;'>("+error+")</span><span style='position:relative;top:5px;cursor:pointer' id='clear_console_ico'>&nbsp;&nbsp;<img border='0' title='Clear Console' src='"+Mind.Properties.imagesPath+"/clear_console.png'></span>";
		$("#clear_console_ico").bind("click",function(){
			document.getElementById("outputPanel_DebugTab_Label").innerHTML = "Debug";
			document.getElementById("outputPanel_DebugTab").innerHTML = "";
			Mind.AjaxHandler.counterErrors = 0;
		});
		if(document.getElementById("outputPanel_DebugTab"))
		{
			if(document.getElementById("outputPanel_DebugTab").innerHTML== 'No Errors')
				document.getElementById("outputPanel_DebugTab").innerHTML= '';
			document.getElementById("outputPanel_DebugTab").innerHTML += content;
		}
		Mind.Plugins.OnReporting();
	}
};

Mind.jsError= {
	Init: function(){
		window.onerror= Mind.jsError.Capture;
	},
	Capture: function(errMsg, errFile, errLine){
		if(!errFile && errLine == 0)
			return false;
		Mind.AjaxHandler.counterErrors++;
		$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('enable', 0).tabs('enable', 1).tabs('enable', 2).tabs('enable', 3).tabs('enable', 4).tabs('enable', 5);
		$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('option', 'selected', 4);
		Mind.Dialog.CloseModal();
		Mind.Dialog.CloseMessage();
		
		var date = new Date();
		var content = "<div style='background:white;border:solid 1px gray;padding:8px;margin-bottom:4px;font-faily:tahoma;font-size:11px'>" +
		"<div class='ui-corner-all' style='width:200px;background:#fae48d;border:solid 1px gray;padding:2px'>" + date.toGMTString() + "</div>" + 
		"<div style='padding:5px;'>Error Code:<span style='color:red'> 0</span></div>" +
		"<div style='padding:5px;'>Status: <span style='color:red'>"  + 'Internal client Error' + "</span></div>" + 
		"<div style='padding:5px;'>Error Message: <span style='color:red'>"  + errMsg + "</span></div>" +
		"<div style='padding:5px;'>File: "  + errFile + "</div>" +
		"<div style='padding:5px;'>At Line: " + errLine + "</div></div>";
		var error = Mind.AjaxHandler.counterErrors == 1 ? Mind.AjaxHandler.counterErrors+" Error": Mind.AjaxHandler.counterErrors+" Errors";
		document.getElementById("outputPanel_DebugTab_Label").innerHTML = "Debug <span style='font-weight:bold;color:red;font-size:12px;font-family:tahoma;'>("+error+")</span><span style='position:relative;top:5px;cursor:pointer' id='clear_console_ico'>&nbsp;&nbsp;<img border='0' title='Clear Console' src='"+Mind.Properties.imagesPath+"/clear_console.png'></span>";
		$("#clear_console_ico").bind("click",function(){
			document.getElementById("outputPanel_DebugTab_Label").innerHTML = "Debug";
			document.getElementById("outputPanel_DebugTab").innerHTML = "";
			Mind.AjaxHandler.counterErrors = 0;
		});
		if(document.getElementById("outputPanel_DebugTab"))
		{
			if(document.getElementById("outputPanel_DebugTab").innerHTML== 'No Errors')
				document.getElementById("outputPanel_DebugTab").innerHTML= '';
			document.getElementById("outputPanel_DebugTab").innerHTML += content;
		}
		Mind.Plugins.OnReporting();
	}
};

Mind.jsError.Init();

Mind.Utils = {
	ObjToPost: function(obj){
		var ret= '';
		for(x in obj)
		{
			if(typeof obj[x] == 'object' || typeof obj[x] == 'array')
			{
				//ret+= 
			}
			ret+= ((ret== '')?'':'&')+x+'='+((obj[x] == '')?' ':obj[x]);
		}
		return ret;
	},
	StripTags: function (strMod){
		if(arguments.length<3) strMod=strMod.replace(/<\/?(?!\!)[^>]*>/gi, '');
		else{
			var IsAllowed=arguments[1];
			var Specified=eval("["+arguments[2]+"]");
			if(IsAllowed){
				var strRegExp='</?(?!(' + Specified.join('|') + '))\b[^>]*>';
				strMod=strMod.replace(new RegExp(strRegExp, 'gi'), '');
			}else{
				var strRegExp='</?(' + Specified.join('|') + ')\b[^>]*>';
				strMod=strMod.replace(new RegExp(strRegExp, 'gi'), '');
			}
		}
		return strMod;
	},
	SetLoad : function(flag,message,type){
		message = message ? message : "Loading...";
		if(type == "full")
			return flag ? Mind.Dialog.ShowMessage("", "<img src='images/loader.gif'>&nbsp;&nbsp;"+message, 10000) : Mind.Dialog.CloseMessage();
		else
			return flag ? Mind.Dialog.ShowMessage("", "<img src='images/loader.gif'>&nbsp;&nbsp;"+message, 10000) : Mind.Dialog.CloseMessage();
	},
	SetDocumentDefaults : function(){
				
		self.focus();
		//window.onbeforeunload = function () { Mind.Theme.Save(); return 'Are you sure you want to exit?' }
		window.onbeforeunload = function () { Mind.Theme.Save(); };
		
		window.onselectstart = function(){
			return (event.srcElement.tagName=='INPUT' || event.srcElement.tagName=='TEXTAREA' || event.srcElement.tagName=='SELECT')? true: false;
		};

		$("body").bind("mousedown",function(event){
			if(typeof event.preventDefault != 'undefined'){
				if(event.target.tagName!='SELECT' && event.target.tagName!='INPUT' && event.target.tagName!='TEXTAREA' && event.target.tagName!='OPTION'){
					if(event.stopPropagation ) { event.stopPropagation(); } else { event.cancelBubble = true; }
					event.preventDefault();
				}
			}
		});
		
		var bodyAttributes = {
			"bgcolor" : "#ffffff",
			"leftmargin" : "0",
			"topmargin" : "0",			
			"bottommargin" : "0",
			"rightmargin" : "0"
		};
		$("body").attr(bodyAttributes);
	}
};

Mind.Theme = {
	userConfig: {
		fontSize : 12,
		bold : 'normal',
		italic : 'normal',	
		fontColor : "#666666",
		backgroundColor : '#ffffff',
		leftPanelPos : '',
		rightPanelPos : '',
		bottomPanelPos : '',
		editorFull : false,
		tabsFull : false
	},
	Reset: function(){
		Mind.Panels.Reset();
		this.GetUserConfig();
		
		this.userConfig= {
						fontSize : 12,
						bold : 'normal',
						italic : 'normal',	
						fontColor : "#666666",
						backgroundColor : '#ffffff',
						leftPanelPos : '',
						rightPanelPos : '',
						bottomPanelPos : '',
						editorFull : false,
						tabsFull : false
					};
		
		document.getElementById('mindEditor').style.fontWeight= 'normal';
		document.getElementById('mindEditor').style.fontStyle= 'normal';
		document.getElementById('mindEditor').style.fontSize= '12px';
		document.getElementById('bgToChangeOnEditor').style.backgroundColor= '#fff';
		document.getElementById('bgToChangeOnEditor').style.color= '#666';
		
		gravaCookie('Mind.Theme.Configuration', JSON.stringify(Mind.Theme.userConfig));
	},
	Save: function(){
		gravaCookie('Mind.Theme.Configuration', JSON.stringify(Mind.Theme.userConfig));
	},	
	GetUserConfig: function(){
		var cookie = leCookie('Mind.Theme.Configuration');
		cookie = cookie!=null? cookie: false;
		if(cookie)
		{
			return JSON.parse(cookie);
		}
	},
	Load: function(){
		var config = Mind.Theme.GetUserConfig();
		if(config){
			Mind.Theme.userConfig.fontSize = parseInt(config.fontSize);
			Mind.Theme.userConfig.bold = config.bold;
			Mind.Theme.userConfig.italic = config.italic;
			Mind.Theme.userConfig.fontColor = config.fontColor;
			Mind.Theme.userConfig.backgroundColor = config.backgroundColor;
			Mind.Theme.userConfig.leftPanelPos = config.leftPanelPos;
			Mind.Theme.userConfig.rightPanelPos = config.rightPanelPos;
			Mind.Theme.userConfig.bottomPanelPos = config.bottomPanelPos;
			Mind.Theme.userConfig.editorFull = config.editorFull;
			Mind.Theme.userConfig.tabsFull = config.tabsFull;
		}
	},
	LoadCurrentTheme: function(){
		var cookie = leCookie('Mind.Theme.theme');
		cookie = cookie!=null? cookie: false;
		if(cookie)
		{
			Mind.Theme.SetTheme(cookie);
		}
	},
	SetTheme: function(theme){
		$(document.getElementsByTagName("head")[0]).find('#curTheme').remove();
		if(theme != 'default')
		{
			$("<link>").attr({"rel":"stylesheet",
							  "type":"text/css",
							  "href": Mind.Properties.stylesPath+"/themes/"+theme+'/'+theme+'.css',
							  "media":"screen",
							  "id": 'curTheme'
							 }).appendTo(document.getElementsByTagName("head")[0]);
			gravaCookie('Mind.Theme.theme', theme);
		}else{
				apagaCookie('Mind.Theme.theme');
			 }
	}
};
Mind.Menus = {
	Init : function(){
		Mind.Menus.Manage.Init();
	}
};
Mind.Menus.Manage = {
	Init : function(){
		Mind.Menus.Manage = {
			projects : {
				Action : function(){
					Mind.Dialog.OpenModal(true,'800','523','Manage Projects','midle','manage_project.php','form',function(){},false,true);
				}
			},
			plugins : {
				Action : function(){
					Mind.Dialog.OpenModal(true,'770','523','Manage Plugins','midle','manage_plugin.php','form',function(){},false,true);
				}
			},
			users : {
				Action : function(){
					Mind.Dialog.OpenModal(true,'770','523','Manage Users','midle','manage_user.php','form',function(){},false,true);
				}
			}
		};
	}
};

Mind.View = {};

Mind.View.User = {
	New : function(){
		Mind.Dialog.OpenModal(true,
					  '400',
					  '300',
					  'New User',
					  'midle',
					  'new_user.php',
					  'form',
					  function(){},
					  true,
					  true);
	},
	Manage : function(){
		(function(){
			Save = function(name){
				Mind.Utils.SetLoad(true);
				 $.ajax({
					type: "POST",
					url: "framework/components/manage_user.php?action=saveuser&login="+name,
					data: $("#manage_user_form").serialize(),
					success: function(msg){
					 	if(msg.substring(0,5) == 'Error')
					 		Mind.Dialog.ShowAlert(msg, "Error!");
						Mind.Utils.SetLoad(false);
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						Mind.AjaxHandler.Capture(XMLHttpRequest);
					}
				});
			};
		})();
		$(".list_users").bind("click",
			function(){
			$(".user_content_list").css("visibility","hidden");
			$(".list_users").removeClass("list_selected");
			$(".list_users").css("color","#000000");
			$(this).css("color","#ffffff");
			$(this).addClass("list_selected");
				Mind.Component.Load({
					componentName : "userData",
					data: {name:$(this).attr("userName")}
				},
				'userList',
				'',
				function(comps){
						try
						{
							comps= Mind.Component.Parse(comps);
						}catch(e){
							Mind.Dialog.ShowAlert("Failed loading the list of users. Probably you are not allowed to do this", "Error");
							Mind.Dialog.CloseModal();
							return false;
						}
						var user = comps.userData;
						var content = "<form id='manage_user_form'>";
						content += "<span class='plugin_name'>" + user.login + "</span><br>";
						content += "<table cellspacing='5'>";
						content += "<tr><td><b><i>Name:</i></b><br> <input name='name' value='" + user.name + "' type='text'></td><tr>";
						content += "<tr><td><b><i>Age:</i></b><br><input name='age' value='"+ user.age + "' type='text'></td><tr>";
						content += "<tr><td><b><i>Description:</i></b><br><textarea name='description'>"+ user.description +"</textarea></td><tr>";
						content += "<tr><td><b><i>Position:</i></b><br> <input name='position' value='" + user.position + "' type='text'></td><tr>";
						content += "<tr><td><b><i>E-mail:</i></b><br> <input name='email' value='"+ user.email +"' type='text'></td><tr>";
						content += "<tr><td><b><i>Password:</i></b><br><input name='pwd' value='" + (user.password||'') +"' type='password'></td><tr>";
						content += "<tr><td><button onclick='Save(\""+user.login+"\")' onmouseover='$(this).addClass(\"ui-state-hover\")' onmouseout='$(this).removeClass(\"ui-state-hover\")' class='ui-state-default ui-corner-all' type='button'>Save</button></td>";
						content += "<td colspan='2'><button onclick='Remove(\""+user.login+"\")' onmouseover='$(this).addClass(\"ui-state-hover\")' onmouseout='$(this).removeClass(\"ui-state-hover\")' class='ui-state-default ui-corner-all' type='button'>Remove</button></td></tr></table>";
						content += "</form>";
						$(".user_content_list").html(content);
					});
					$(".user_content_list").css("visibility","visible");
			});
		$(".list_users").eq(0).trigger("click");
	}
};

Mind.View.Plugin = {
	Manage : function(){
	
		// Disable
		(function(){
			Disable = function(name,action){
				action = action || "disable";
				 Mind.Utils.SetLoad(true);
				 $.ajax({
					type: "POST",
					url: "framework/components/manage_plugin.php?action="+action+"Plugin&code="+name,
					success: function(msg){						
						if(msg=="error"){
							$("#mind_manage_plugin_label_message").html("<span style='font-weight:bold;color:red'>Error</span>");
						}else{
							Mind.Menus.Manage.plugins.Action();
							Mind.Utils.SetLoad(false);							
						}
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						Mind.AjaxHandler.Capture(XMLHttpRequest);
					}
				});
			};
		})();
		
		// Enable
		(function(){
			Enable = function(name){
				Disable("."+name,"enable");
			};
		})();
		
		// Uninstall
		(function(){
			Uninstall = function(name){
				if(confirm("Are you sure you want to uninstall this plugin?")){
					 Mind.Utils.SetLoad(true);
					 $.ajax({
						type: "POST",
						url: "framework/components/manage_plugin.php?action=removePlugin&code="+name,
						success: function(msg){						
							if(msg=="error"){
								$("#mind_manage_plugin_label_message").html("<span style='font-weight:bold;color:red'>Error</span>");
							}else{
								Mind.Menus.Manage.plugins.Action();
								Mind.Utils.SetLoad(false);							
							}
						},
						error : function(XMLHttpRequest, textStatus, errorThrown){
							Mind.AjaxHandler.Capture(XMLHttpRequest);
						}
					});
				}
			};
		})();
		
		$(".list_plugins").bind("click",
			function(){
			$(".plugin_content_list").css("visibility","hidden");
			$(".list_plugins").removeClass("list_selected");
			$(".list_plugins").css("color","#000000");
			$(this).css("color","#ffffff");
			$(this).addClass("list_selected");
				
				Mind.Component.Load({
					componentName : "pluginData",
					data: {name:$(this).attr("pluginName")}
				},
				'pluginsList',
				'',
				function(comps){
						var func;
						comps= Mind.Component.Parse(comps);
						var plugin = comps.pluginData;
						var content = "";
						content += "<span class='plugin_name'>" + plugin.name + "</span><br>";
						content += "<table cellspacing='5' style='width:100%;'>";
						content += "<tr><td><b><i>Date:</i></b><br> " + plugin.date + "</td><tr>";
						content += "<tr><td><b><i>Authors:</i></b><br>" + plugin.authors + "</td><tr>";
						content += "<tr><td><b><i>Link:</i></b><br>" + plugin.link +"</td><tr>";
						content += "<tr><td><b><i>Description:</i></b><br>" + plugin.description + "</td><tr>";
						func = plugin.disabled ? "Enable" : "Disable";
						content += "<tr><td><button onclick='"+func+"(\""+plugin.name+"\")' onmouseover='$(this).addClass(\"ui-state-hover\")' onmouseout='$(this).removeClass(\"ui-state-hover\")' class='ui-state-default ui-corner-all' type='button'>"+func+"</button></td>";
						content += "<td colspan='2'><button onclick='Uninstall(\""+plugin.name+"\")' onmouseover='$(this).addClass(\"ui-state-hover\")' onmouseout='$(this).removeClass(\"ui-state-hover\")' class='ui-state-default ui-corner-all' type='button'>Uninstall</button></td></tr></table>";
						$(".plugin_content_list").html(content);
					});
					$(".plugin_content_list").css("visibility","visible");
			});
		$(".list_plugins").eq(0).trigger("click");
	}
};

Mind.View.Project = {
	
	New : function(){
		
	},
	Manage : function(){
		// Save
		(function(){
			Save = function(name){
				 Mind.Utils.SetLoad(true);
				 $.ajax({
					type: "POST",
					url: "framework/components/manage_project.php?action=saveProject&code="+name,
					data: $("#form_project_manage").serialize(),
					success: function(msg){						
						if(msg=="error"){
							$("#mind_manage_project_label_message").html("<span style='font-weight:bold;color:red'>Error</span>");
						}else{
							Mind.Utils.SetLoad(false);
							if(msg == Mind.Project.attributes.name)
							{
								Mind.Project.Close();
								Mind.Project.Load(msg);
							}
						}
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						Mind.AjaxHandler.Capture(XMLHttpRequest);
					}
				});
			};
		})();
		
		//Remove
		(function(){
			Remove = function(name){
				 if(confirm("Remove the project " + name + "?")){
					 $.ajax({
						type: "POST",
						url: "framework/components/manage_project.php",
						data: "action=removeProject&code="+name,
						success: function(msg){
							if(msg=="success"){
									  Mind.Component.Load('projectList',
										function (comps){
											comps= Mind.Component.Parse(comps);
											$("#mind_project_manage_"+name).remove();
											Mind.Panel['left'].Update(comps['projectList']);
											$(".projet_content_list").html("No Projects Yet");
											$(".list_projects").eq(0).trigger("click");
											if(Mind.Project.attributes.name==name)
												Mind.Project.Close();
										}
									 );
							}else{
								eval(msg);
							}
							
						}
					});
				}
			};
		})();			
		
		$(".list_projects").bind("click",function(){
			$(".projet_content_list").css("visibility","hidden");
			$(".list_projects").removeClass("list_selected");
			$(".list_projects").css("color","#000000");
			$(this).css("color","#ffffff");
			$(this).addClass("list_selected");
				
				Mind.Component.Load({
				componentName : "projectData",
				data: {name:$(this).attr("projectName")}
				},
				'DBMSsList',
				'languagesList',
				'usersList',
				function(comps){
						comps= Mind.Component.Parse(comps);						
						var project = comps.projectData;						
						//Populate combo Languages
						var langs = "<select name='lang'>";
						var list = comps.languagesList;
						for(var i=0;i<list.length;i++){
							var selected = project.lang == list[i] ? "selected" : "";
							langs += "<option "+selected+" value='"+list[i]+"'>"+list[i]+"</option>";
						}
						langs += "</select>";
						
						//Populate DBMSs
						var dbms = "<select name='dbms'>";
						var list = comps.DBMSsList;
						for(var i=0;i<list.length;i++){
							var selected = project.dbms == list[i] ? "selected" : "";
							dbms += "<option "+selected+" value='"+list[i]+"'>"+list[i]+"</option>";
						}
						dbms += "</select>";
						
						var users = "<select class='iptComboMultiple' multiple='multiple' name='users[]'>";
						var list = comps.usersList;
						
						for(var i=0;i<list.length;i++){							
							var selected = project.users.name == list[i].name ? "selected" : "";
							users += "<option "+selected+" value='"+list[i].name+"'>"+list[i].name+"</option>";
						}						
						users += "</option>";
		
						var content = "<form id='form_project_manage' onsubmit='return false;'>";
						content += "<div id='mind_manage_project_accordion' style='font-size:12px'>";
						content += "<div><a href='#'>Project Information</a></div>";
									content += "<div>";
									content += "<table><tr><td>Project Name:</td><td> <input disabled type='text' name='name' value='"+project.name+"' /></td></tr>";
										content += "<tr><td>Owner:</td><td> <input disabled type='text' type='text' name='owner' value='"+project.owner+"'></td></tr>";
										content += "<tr><td> Date:</td><td> <input disabled type='text' type='text' name='date' value='"+project.date+"'></td></tr>";
										content += "<tr><td> Version:</td><td> <input disabled type='text' type='text' name='version' value='"+project.version+"'></td></tr>";
										content += "<tr><td></td></tr></table>";						
									content += "</div>";
							content += "<div><a href='#'>Project Data</a></div>";
								content += "<div>";
									content += "<table>";
										content += "<tr><td>Mind-Language:</td><td>"+langs+"</td></tr>";
										content += "<tr><td> DBMS:</td><td> "+dbms+"</td></tr>";
										content += "<tr><td> Users:</td><td> "+users+"</td></tr>";
										content += "<tr><td> Description:</td><td> <textarea cols='30' rows='4' name='description'>"+project.description+"</textarea></td></tr>";
										content += "<tr><td></td></tr></table>";
									content += "</div>";
						content += "<div><a href='#'>Database</a></div>";
						content += "<div>";
						content += "<div class='tabs' style='width:500; margin-top:8px;'>";
							content += "<ul>";
								content += "<li><a href='#newProjectForm_tab1'>Development DB</a></li>";
								content += "<li><a href='#newProjectForm_tab2'>Production DB</a></li>";
							content += "</ul>";
							content += "<div id='newProjectForm_tab1'>";
								content += "<table>";
									content += "<tr>";
										content += "<td>Address</td>";
										content += "<td>";
											content += "<input type='text' name='dbAddress1' class='iptText' value='"+project.environment.development.dbAddress+"'>";
										content += "</td>";
										content += "<td>";
											content += "Port";
										content += "</td>";
										content += "<td>";
											content += "<input type='text' name='port1' class='iptText' maxlength='4' value='"+project.environment.development.dbPort+"' style='width:50px;' label='DataBase Port'>";
										content += "</td>";
									content += "</tr>";
									content += "<tr>";
										content += "<td>DB Name</td>";
										content += "<td>";
											content += "<input type='text' name='dbName1' class='iptText' value='"+project.environment.development.dbName+"' label='Data Base Name'></td>";
										content += "<td>";											
										content += "</td>";
										content += "<td><br>";
										content += "</td>";
									content += "</tr>";
									content += "<tr>";
										content += "<td>Root User</td>";
										content += "<td>";
											content += "<input type='text' name='userRoot1' value='"+project.environment.development.rootUser+"' class='iptText'> </td>";
										content += "<td>Password</td>";
										content += "<td>";
											content += "<input type='password' name='userRootPwd1' value='"+project.environment.development.rootUserPwd+"' class='iptPwd'></td>";
									content += "</tr>";
									content += "<tr>";
										content += "<td>User</td>";
										content += "<td>";
											content += "<input type='text' name='user1' class='iptText' value='"+project.environment.development.user+"'> </td>";
										content += "<td> Password</td>";
										content += "<td>";
											content += "<input type='password' name='userPwd1' class='iptPwd' value='"+project.environment.development.userPwd+"'></td>";
									content += "</tr>";
								content += "</table>";
							content += "</div>";
							content += "<div id='newProjectForm_tab2'>";
								content += "<table>";
									content += "<tr>";
										content += "<td>Address</td>";
										content += "<td>";
											content += "<input type='text' name='dbAddress2' class='iptText' label='Data Bases Name' value='"+project.environment.production.dbAddress+"' ></td>";
										content += "<td>Port</td>";
										content += "<td>";
											content += "<input type='text' name='port2' class='iptText' maxlength='4' style='width:50px;' value='"+project.environment.production.dbPort+"'> </td>";
									content += "</tr>";
									content += "<tr>";
										content += "<td> DB Name</td>";
										content += "<td>";
											content += "<input type='text' name='dbName2' class='iptText' value='"+project.environment.production.dbName+"' label='Data Base Name'> </td>";
										content += "<td></td>";
										content += "<td><br>";
										content += "</td>";
									content += "</tr>";
									content += "<tr>";
										content += "<td> Root User</td>";
										content += "<td>";
											content += "<input type='text' name='userRoot2' class='iptText' value='"+project.environment.production.rootUser+"'></td>";
										content += "<td>Password</td>";
										content += "<td>";
											content += "<input type='password' name='userRootPwd2' class='iptPwd' value='"+project.environment.production.rootUserPwd+"' style='width:100px;'></td>";
									content += "</tr>";
									content += "<tr>";
										content += "<td>User</td>";
										content += "<td>";
											content += "<input type='text' name='user2' class='iptText' value='"+project.environment.production.user+"'> </td>";
										content += "<td>Password</td>";
										content += "<td>";
											content += "<input type='password' name='userPwd2' class='iptPwd' value='"+project.environment.production.userPwd+"' ></td>";
									content += "</tr>";
								content += "</table>";
							content += "</div>";
						content += "</div>";
						content += "</div>";
						content += "</div>";
						content += "</form>";
						content += "<table><tr>";
						content += "<td><button onclick='Remove(\""+project.name+"\")' onmouseover='$(this).addClass(\"ui-state-hover\")' onmouseout='$(this).removeClass(\"ui-state-hover\")' class='ui-state-default ui-corner-all' type='button'>Remove this Project</button></td><td>&nbsp;</td>";
						content += "<td><button onclick='Save(\""+project.name+"\")' onmouseover='$(this).addClass(\"ui-state-hover\")' onmouseout='$(this).removeClass(\"ui-state-hover\")' class='ui-state-default ui-corner-all' type='button'>Save</button></td></tr></table>";															
						$(".projet_content_list").html(content);
					});					
					setTimeout(function(){$("#mind_manage_project_accordion").accordion();$("#form_project_manage .tabs").tabs();$(".projet_content_list").css("visibility","visible");},500);
		});
		$(".list_projects").eq(0).trigger("click");
	}
};

Mind.ConfirmUpdate= function(){
	//Mind.tmpShownData= Mind.Dialog.ShowData("<center><br/><br/><img src='"+Mind.Properties.imagesPath+"/load.gif'><br/>Checking for updates</center>",'Mind Updates', false, false, true);
	var url= Mind.Properties.path+'/../../mind_update.php?confirmUpdate=true';
	document.getElementById('mindUpdateImageLoader').style.display= '';
	document.getElementById('tempIframeForUpdate').src= url;
};
Mind.UpdateItSelf= function(){
	
	Mind.tmpShownData= Mind.Dialog.ShowData("<center><br/><br/><img src='"+Mind.Properties.imagesPath+"/load.gif'><br/>Checking for updates</center>",
						 'Mind Updates', false, false, true);
	var url= Mind.Properties.path+'/../../mind_update.php';
	
	setTimeout(function(){
		$.ajax({
				url: url,
				success: function(ret){
					if(document.getElementById('mindUpdateShowDataDiv'))
						document.getElementById('mindUpdateShowDataDiv').parentNode.removeChild(document.getElementById('mindUpdateShowDataDiv'));
					Mind.Dialog.CloseData(Mind.tmpShownData);
					Mind.tmpShownData= Mind.Dialog.ShowData(ret, 'Message', 580, 400, true);
					document.getElementById('currentChangesToTake').innerHTML= document.getElementById('hiddenCurrentChangesToTake').innerHTML;
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					Mind.AjaxHandler.Capture(XMLHttpRequest);
				}
		});
	}, 600);
};

Mind.ImagePreLoader= {
	sources: Array(),
	Call: function(){
		var img= false;
		for(var i= 0; i<this.sources.length; i++)
		{
			//document.body.appendChild((document.createElement('IMG').src= Mind.Properties.imagesPath+'/'+ this.sources[i]).styles.display= 'none');
			img= Mind.Properties.imagesPath+'/'+ this.sources[i];
			this.sources[i]= new Image();
			this.sources[i].src= img;
		};
		this.sources= Array();
	},
	Add: function(url){
		this.sources.push(url);
	},
	Init:function(){
		this.Add('bt_backup_over.gif');
		this.Add('../framework/styles/css_images/loading_bar_back');
		this.Add('bt_export_over.gif');
		this.Add('bt_full_close_over.png');
		this.Add('bt_full_editor_over.png');
		this.Add('bt_play_over.gif');
		this.Add('bt_prop_over.gif');
		this.Add('bt_save_over.gif');
		this.Add('compiler_over.gif');
		this.Add('icon_file.gif');
		this.Add('load.gif');
		this.Add('logo_light.png');
		this.Add('red_close_button_over.png');
		this.Add('unsaved.jpg');
		this.Add('self_referenced.gif');
		this.Add('info.gif');
		this.Add('key.gif');
		this.Add('icon_folder.gif');
		this.Add('fkey.gif');
		this.Add('del.gif');
	}
};

Mind.FeedBack= {
	Open: function(){
		Mind.Dialog.OpenModal(true, '600', '530', 'FeedBack', 'middle', 'feedback.php', 'form');
		//ajax,width,height,title,position,source,type,callBack,buttons,noresizable
	}
};

Mind.Help= {
	Open: function(page){
		Mind.Dialog.OpenModal(true, '700', '530', 'Help', 'middle', 'help.php?page='+(page||'false'), 'form');
	}
};
