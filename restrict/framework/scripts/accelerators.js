/*
	nre objects
		jQuery.accelerator
	new attributes in jQuery.event:
		altKey	   -> returns true or false
		dispatcher -> the object that dispatched the event
*/ 
var _jQAcceleratorLastCtrl= 0;
var _jQAcceleratorActive= false;
jQuery.acceleratorItemAccess= function(event)
{
	event= document.all? window.event: event;
	if(window.event)
	{
		var kCode= event.keyCode;
		var k= String.fromCharCode(kCode);
	}
	if(event.which)
	{
		var kCode= event.which;
		var k= String.fromCharCode(kCode);
	}
	
	var ctrl= event.ctrlKey;
	var alt= event.altKey;
	jQuery.event.dispatcher= document.all? event.srcElement:event.target;
	if(jQuery.event.dispatcher.tagName == 'INPUT' || jQuery.event.dispatcher.tagName == 'TEXTAREA' || jQuery.event.dispatcher.tagName == 'SELECT')
	{
		return true;
	}
	jQuery.accelerator.onKeyDown(jQuery.event.dispatcher, event);
	if(alt && kCode!= 16)
	{
		var ac= $('[accelerator='+k.toUpperCase()+']');
		if(ac.length>0)
		{
			$(ac[0]).trigger('click');
			jQuery.accelerator.onCall(ac[0], event);
			$('#acceleratorMessageToolTip').fadeOut('fast');
		}
		jQuery.event.cancel(event);
		return false;
	}

	if(kCode== 18)
	{
		var ac= $('[accelerator]');
		if(ac.length>0)
		{
			var c= '';
			for(var i=0; i<ac.length; i++)
			{
				var a= ac[i].getAttribute('accelerator'); 
				c+= '<b>'+a.toUpperCase()+'</b> - '+((document.all)? ac[i].outerText.replace(/[\n\t\r]/g, ' '):ac[i].textContent.replace(/[\n\t\r]/g, ' '))+'<br>';
			}
			if(!document.getElementById('acceleratorMessageToolTip'))
			{
				var d= document.createElement('div');
				var obj= jQuery.accelerator.toolTip;
				if(obj.className!=null)
					d.className= obj.className;
				else{
						d.style.backgroundColor= '#ff9';
						d.style.textAlign= 'left';
						d.style.border= 'solid 1px #f99';
						d.style.color= 'black';
					}
				d.style.position= 'absolute';
				d.style.zIndex= '999999';
				document.body.appendChild(d);
				d.id= 'acceleratorMessageToolTip';
				$('#acceleratorMessageToolTip').html(c);
				if(obj.vertical == 'top')
					d.style.top= '0px';
				else if(obj.vertical == 'center')
					d.style.top= (document.body.clientHeight/2) - (d.offsetHeight/2);
				else
					d.style.top= document.body.clientHeight - d.offsetHeight;
				if(obj.horizontal == 'right')
					d.style.right= '0px';
				else if(obj.horizontal == 'center')
					d.style.left= (document.body.clientWidth/2) - (d.offsetWidth/2);
				else
					d.style.left= '0px';
			}
			$('#acceleratorMessageToolTip').fadeIn('fast');
		}
	}
}
jQuery.acceleratorItemLeave= function (event)
{
	event= document.all? window.event: event;
	jQuery.event.dispatcher= document.all? event.srcElement:event.target;
	jQuery.accelerator.onKeyUp(jQuery.event.dispatcher, event);
	$('#acceleratorMessageToolTip').fadeOut('fast');
	return;
	/* 
	event= document.all? window.event: event;
	if(window.event)
	{
		kCode= event.keyCode;
		k= String.fromCharCode(kCode);
	}
	if(event.which)
	{
		kCode= event.which;
		k= String.fromCharCode(kCode);
	}
	jQuery.accelerator.onKeyUp(jQuery.event.dispatcher, event);
	if(_jQAcceleratorActive!=false)
	{
		$(_jQAcceleratorActive).trigger('mouseout');
		_jQAcceleratorActive= false;
		jQuery.event.cancel(event);
		return false;
	} */
}

jQuery.accelerator= function(event)
{
	this.toolTip= function (obj)
	{
		jQuery.accelerator.toolTip= obj;
	};
	this.showMessage= function (obj){
		/*msg: "Use 'double CTRL and hold' to access menu with your keyboard",
		horizontal:'center',
		vertical:'bottom',
		animation:'fadeOut'*/
		var d= document.createElement('div');
		if(obj.className!=null)
			d.className= obj.className;
		else{
				d.style.width= '400px';
				d.style.backgroundColor= '#ff9';
				d.style.textAlign= 'center';
				d.style.border= 'solid 1px #f99';
				d.style.color= 'black';
			}
		//d.style.display= 'none';
		d.style.position= 'absolute';
		d.style.zIndex= '999999';
		document.body.appendChild(d);
		d.innerHTML= obj.msg? obj.msg:"Press and hold alt to access items with your keyboard";
		if(obj.vertical == 'top')
			d.style.top= '0px';
		else if(obj.vertical == 'center')
			d.style.top= (document.body.clientHeight/2) - (d.offsetHeight/2);
		else
			d.style.top= document.body.clientHeight - d.offsetHeight;
		if(obj.horizontal == 'right')
			d.style.right= '0px';
		else if(obj.horizontal == 'center')
			d.style.left= (document.body.clientWidth/2) - (d.offsetWidth/2);
		else
			d.style.left= '0px';
		d.id= 'acceleratorMessageLabel';
		if(obj.delay)
		{
			d.style.display= 'none';
			setTimeout(function (){
				$('#acceleratorMessageLabel').fadeIn('slow');
			}, obj.delay);
		}
		setTimeout(function (){
				$('#acceleratorMessageLabel').fadeOut('slow');
			}, (obj.duration? obj.duration: 8000));
	};
};

$('document').ready(function (){
	jQuery.event.cancel= function (event)
	{
		if(document.all)
		{
			event= window.event;
			event.cancelBubble= true;
			event.returnValue= false;
			event= 0;
			return false;
		}else{
				if (event.stopPropagation)
				{
					event.stopPropagation();
					event.preventDefault();
					event= 0;
					return false;
				}
			 }
		return false;
	};
	$(document).keydown(jQuery.acceleratorItemAccess);
	$(document).keydown(jQuery.accelerator.shortCut);
	$(document).keyup(jQuery.acceleratorItemLeave);
	jQuery.accelerator= new jQuery.accelerator();
	jQuery.accelerator.onKeyDown= function(){};
	jQuery.accelerator.onKeyUp= function(){};
	jQuery.accelerator.onCall= function(){};
	//jQuery.accelerator.showMessage
	//window.status= 'Use double CTRL to access menus';
	//$(document).keypress(jQuery.acceleratorCancel);
	//document.all? this.attachEvent('onkeyup', function (event){alert(event.keyCode)}): this.addEventListener('keyup', function (event){alert(event.keyCode)}, false);
});

jQuery.accelerator.shortCut= function(event){
	//jQuery.accelerator.shortCutsList.push();
	var qr= "shortCut=";
	qr+= event.ctrlKey? 'ctrl+': '';
	qr+= event.shiftKey? 'shift+': '';
	qr+= event.altKey? 'alt+': '';
	
	if(event.keyCode > 111 && event.keyCode < 122)
	{
		//alert('era um F');
		qr+= 'F'+(event.keyCode - 111);
	}else{
			if(qr.indexOf('+') == -1)
				return true;
			qr+= String.fromCharCode(event.keyCode).toLowerCase();
		 }
	//$('['+qr+']').trigger('click');
	//alert(qr);
	var o= $('['+qr+']')[0];
	if(o)
	{
		var x= eval(o.className);
		eval(x);
		jQuery.event.cancel(event);
	}
	//return false;
};
$(window).bind('blur', function (){
	$('#acceleratorMessageToolTip').fadeOut('fast');
});










