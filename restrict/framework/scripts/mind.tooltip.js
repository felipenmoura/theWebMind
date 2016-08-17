Mind.ToolTip= {
	Show: function(event, str){
		if(Mind.ToolTip.htmlElement.style.display!= 'none')
			return;
		event= event|| window.event;
		Mind.ToolTip.htmlElement.style.left= event.clientX+15;
		Mind.ToolTip.htmlElement.style.top= event.clientY-10;
		$(Mind.ToolTip.htmlElement).fadeIn('slow');
		Mind.ToolTip.htmlElement.innerHTML= str;
		/*Mind.ToolTip.htmlElement.onmouseout= function(){
			Mind.ToolTip.Hide();
		};*/
	},
	Hide: function(){
		$(Mind.ToolTip.htmlElement).fadeOut('slow');
	},
	Init: function(){
		Mind.ToolTip.htmlElement= document.createElement('DIV');
		Mind.ToolTip.htmlElement.className= 'mindToolTip';
		Mind.ToolTip.htmlElement.style.left= '150px';
		Mind.ToolTip.htmlElement.style.top= '150px';
		Mind.ToolTip.htmlElement.innerHTML= "abc";
		Mind.ToolTip.htmlElement.style.display= 'none';
		document.body.appendChild(Mind.ToolTip.htmlElement);
	}
}