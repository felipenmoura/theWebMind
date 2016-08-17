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
			},1000)
			setTimeout(function(){
				Mind.Commom.BlockerFadeOut();
			},1000);
		}
	}
}

Mind.Commom = {
	BlockerFadeOut :  function(){
		setTimeout(function(){
			$('#blocker').fadeOut("slow",function(){
				$('#blocker').html("<br>");
				$('#blocker').css("backgroundColor","");
			});					
		}, 800);
	}
}
Mind.Properties = {
	path : "",
	scriptsPath : "",
	showDeveloperMenu : ""
}

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
				async: false
			}).responseText;		
			$("#mind_dialog_content").html(html);				
		}else{
			$("#mind_dialog").html("");
			$("#mind_dialog_iframe").css("display","block");
			$("#mind_dialog_iframe").attr("src",Mind.Properties.path + "/" + source);
		}	
	},
	
	OpenModal : function(ajax,width,height,title,position,source,type,callBack,buttons){		
		buttons = buttons? false : true;
		var callBack = callBack ? callBack : function(){};
		if(ajax){
			$("#mind_dialog_iframe").css("display","none");
			if(type!="manage"){
				var html = $.ajax({
					url: Mind.Properties.path + "/" + source,
					async: false
				}).responseText;				
				$("#mind_dialog_content").html(html);				
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
				}
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
							}											
							$("#list_panel").append(tempDiv);
							$("#content_panel").append(tempContentDiv);
						}
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
					}
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
			modal : true,
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
		$("#mind_dialog").dialog( 'close' );
	},
	
	ShowData : function(obj,title, w, h){
		var tempDialog = document.createElement("DIV");
		tempDialog.setAttribute("className","mind-data");
		var d = new Date();		
		tempDialog.id = "temp_dialog_" + d.getMilliseconds();
		document.body.appendChild(tempDialog);		
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
			buttons: {
						'Ok': function() {
							$(this).dialog('close');
						}
					}
		});
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
		title = title ? title : ""; message = message ? message : "";
		timeout = timeout ? timeout : 1000; onClose = onClose ? onClose : null;
		$.growlUI(title,message,timeout,onClose);
	},
	
	CloseMessage : function(){
		$.unblockUI();
	}
}
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
			Update: function (objList){
				var str= '';
				if(objList)
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
				}
				else
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
		Mind.Panel["right"] = {
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

		Mind.Panel["center"] = {
			htmlElement : document.getElementById('mind_layout_center'),
			Content : document.getElementById('mind_layout_center').getElementsByTagName('DIV')[0],
			Open : function(){
			},
			Close : function(){
			},
			Adjust : function(){
				this.htmlElement.style.left = Mind.Panel["left"].htmlElement.offsetWidth + 5;
				this.htmlElement.getElementsByTagName('DIV')[0].style.width= document.body.clientWidth- (Mind.Panel["left"].htmlElement.offsetWidth + Mind.Panel["right"].htmlElement.offsetWidth) - 5;
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
		
		Mind.Panel["bottom"] = {
			fullSize : false,
			htmlElement : document.getElementById("mind_layout_bottom"),
			Content : document.getElementById("mind_layout_bottom").getElementsByTagName('DIV')[0],
			DefaultSize : 150,
			closed:false,
			opened:true,
			Open : function(){
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
			Close : function(){
				
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
			Adjust : function(){				
				$("#"+Mind.Panel['bottom'].htmlElement.id+" [autoresize=true]").css('height', Mind.Panel['bottom'].htmlElement.offsetHeight-42);
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
																					Mind.Panel["right"].htmlElement.offsetWidth+
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
				//if(event.clientX < document.body.clientWidth - Mind.Panel["right"].htmlElement.offsetWidth-200)
				//{
					//window.status= ;
					if(document.body.clientWidth - (event.clientX + Mind.Panel["right"].htmlElement.offsetWidth) < 200)
					{
						Mind.Panel["left"].Size(document.body.clientWidth - (Mind.Panel["right"].htmlElement.offsetWidth + 201));
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
						Mind.Panel["right"].Size(5);
						Mind.Panel["center"].Adjust();
						return false;
					}
					var x= document.body.clientWidth - event.clientX;
					
					if(Mind.Panel["center"].htmlElement.offsetLeft+200 >event.clientX)
					{
						Mind.Panel["right"].Size((document.body.offsetWidth - (201 + Mind.Panel["center"].htmlElement.offsetLeft)));
						Mind.Panel["center"].Adjust();
						return;
					}
					
					if(Mind.Panel["right"].htmlElement.style.display=='none')
						Mind.Panel["right"].htmlElement.style.display= '';
					
					//	window.status= Mind.Panel["center"].htmlElement.offsetLeft+' + '+Mind.Panel["center"].htmlElement.offsetWidth+' - '+document.body.clientWidth+' = '+( document.body.offsetWidth - (201 + Mind.Panel["center"].htmlElement.offsetLeft));
					
					
					Mind.Panel["right"].htmlElement.style.width = x-5;
					Mind.Panel["right"].htmlElement.style.left = document.body.clientWidth-x+5;
					//Mind.Panel["right"].htmlElement.getElementsByTagName('DIV')[0].style.width = x;
					Mind.Panel["right"].Content.style.width = x-5;
					Mind.Panel["right"].Content.style.left = document.body.clientWidth-x+5;
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
				Mind.Panel["right"].Adjust(event);
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
						Mind.Panel["right"].htmlElement.style.height= y;
						Mind.Panel["right"].Content.style.height= y;*/
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
		alert("Hide the left Panel")
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
		if(!Mind.Panel["bottom"].fullSize){				
			$("#horizontal_resizable_bar").css("top","25px");
			Mind.Panel["bottom"].htmlElement.style.top = "28px";		
			$("#mind_layout_bottom").css("height",document.body.clientHeight);
			Mind.Panel['bottom'].Size(document.body.clientHeight);
			Mind.Panel['bottom'].Adjust();
			Mind.Panel["bottom"].fullSize = true;
		}else{
			Mind.Panel['bottom'].Size(150);
			Mind.Panel['bottom'].Adjust();
			Mind.Panel["left"].Adjust();
			Mind.Panel["right"].Adjust();
			Mind.Panel["bottom"].fullSize = false;
			Mind.Panel['bottom'].Open();
		}		
	}
}

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
}
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
					//Mind.Components[x]= JSON.parse(retObj[x]);
					Mind.Components[x]= retObj[x];
				}
				//Mind.ProjectPane.Update(retObj['projectList']);
			}
		return retObj;
	},
	Load: function(){		
		var args= new Array();
		//alert(typeof arguments[arguments.length-1]);
		for(var i=0; i<arguments.length-1; i++)
		{
			// Mind.Components[arguments[i]]= {
				// Load: arguments[arguments.length-1]
			// }			
			args.push(arguments[i]);
				//args+= ((args.length>0)? ',':'')+arguments[i];
			
		}
		var args = JSON.stringify(args);
		$.ajax({
			type: "POST",
			url: Mind.Properties.path+'/components.php',
			data:'component='+args,
			success: arguments[arguments.length-1]
		});
	}
}
Mind.Window.onresize = function(event){
	Mind.Panels.Reset();
}

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
		window.onbeforeunload = function () { Mind.Theme.Save(); }
		
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
		}
		$("body").attr(bodyAttributes);
	}
}

Mind.Theme = {
	userConfig : {
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
	Save : function(){
		gravaCookie('Mind.Theme.Configuration', JSON.stringify(Mind.Theme.userConfig));
	},	
	GetUserConfig : function(){
		var cookie = leCookie('Mind.Theme.Configuration');
		cookie = cookie!=null? cookie: false;
		return JSON.parse(cookie);
	},
	Load : function(){
		var config = Mind.Theme.GetUserConfig();
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
}
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
						Mind.Dialog.OpenModal(true,'500','400','Manage Projects ','midle','manage_project.php','form',function(){
					},false);
				}
			}
		}
	}
}

Mind.View = {};
Mind.View.Project = {
	Manage : function(){
		// Save
		(function(){
			Save = function(name){				
				 $.ajax({
					type: "POST",
					url: "framework/components/manage_project.php",
					data: "action=saveProject&code="+name,
					success: function(msg){
						alert(msg);
					}
				});
			}
		})();
		
		//Remove
		(function(){
			Remove = function(name){
				 $.ajax({
					type: "POST",
					url: "framework/components/manage_project.php",
					data: "action=removeProject&code="+name,
					success: function(msg){
						alert(msg);
					}
				});
			}
		})();
		$(".list_projects").bind("click",function(){
			$(".list_projects").removeClass("list_selected");
			$(".list_projects").css("color","#000000");
			$(this).css("color","#ffffff");
			$(this).addClass("list_selected");
			
				 $.ajax({
					type: "POST",
					url: "framework/components/manage_project.php",
					data: "action=getProject&code="+$(this).attr("id"),
					success: function(msg){						
						var project = JSON.parse(msg);
						var content = "<form action='framework/components/manage_project.php' id='form_project_manage'>";
						content +=  "<table>";
						content += "<tr><td> Name:</td><td> <input type='text' name='name' value='"+project.name+"' /></td></tr>";
						content += "<tr><td> Language:</td><td> <input type='text' name='lang' value='"+project.lang+"'></td></tr>";
						content += "<tr><td> Dbms:</td><td> <input type='text' name='dnbms' value='"+project.dbms+"'></td></tr>";
						content += "<tr><td> Description:</td><td> <input type='text' name='description' value='"+project.description+"'></td></tr>";
						content += "<tr><td>Owner:</td><td> <input type='text' name='owner' value='"+project.owner+"'></td></tr>";
						content += "<tr><td> Date:</td><td> <input type='text' name='date' value='"+project.date+"'></td></tr>";
						content += "<tr><td> Version:</td><td> <input type='text' name='version' value='"+project.version+"'></td></tr></table>";
						content += "</form>";
						content += "</table><tr>";
						content += "<td><button onclick='Remove(\""+project.name+"\")' onmouseover='$(this).addClass(\"ui-state-hover\")' onmouseout='$(this).removeClass(\"ui-state-hover\")' class='ui-state-default ui-corner-all' type='button'>Remove this Project</button></td><td>&nbsp;</td>";
						content += "<td><button onclick='Save(\""+project.name+"\")' onmouseover='$(this).addClass(\"ui-state-hover\")' onmouseout='$(this).removeClass(\"ui-state-hover\")' class='ui-state-default ui-corner-all' type='button'>Save</button></td></tr></table>";
						$(".projet_content_list").html(content);
					}
				});							
		});
		$(".list_projects").eq(0).trigger("click");
	}
}