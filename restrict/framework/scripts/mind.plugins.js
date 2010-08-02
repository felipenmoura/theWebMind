Mind.Plugins= {
	// mindEvent
	tabPlugins:Array(),
	useIcon:Array(),
	runOnRunning:Array(),
	runOnLoading:Array(),
	runOnReporting:Array(),
	runOnOpening:Array(),
	
	/* extended methods */
	Save: function(file, content, flag){
		var ret;
		Mind.Component.Ask( {
								componentName:'pluginsSave',
								data: {
										pName: this.name,
										file:file,
										content:content,
										flag:flag
									  }
							},
							function (comps){
								comps= Mind.Component.Parse(comps);
								ret= comps.pluginsLoadFile;
							}
						 );
		return ret;
	},
	LoadFile:function(file){
		var ret;
		Mind.Component.Ask( {
								componentName:'pluginsLoadFile',
								data: {pName: this.name, file:file}
							},
							function (comps){
								comps= Mind.Component.Parse(comps);
								ret= comps.pluginsLoadFile;
							}
						 );
		return ret;
	},
	Post: function(file, o){
		var ret;
		var x= $.ajax({
			async:false,
			type:'post',
			url: Mind.Properties.pluginPath+'/'+this.name+'/data/'+file,
			data:o,
			success: function(data, XMLHTTPRequest){
				return XMLHTTPRequest;
			}
		});
		return x.responseText;
	},
	Unlink:function(file){
		var ret;
		Mind.Component.Ask( {
								componentName:'pluginsUnlinkThis',
								data: {pName: this.name, file:file}
							},
							function (comps){
								comps= Mind.Component.Parse(comps);
								ret= comps.pluginsUnlinkThis;
							}
						 );
		return ret;
	},
	MkDir:function(dir){
		var ret;
		Mind.Component.Ask( {
								componentName:'pluginsMkDir',
								data: {pName: this.name, dir:dir}
							},
							function (comps){
								comps= Mind.Component.Parse(comps);
								ret= comps.pluginsMkDir;
							}
						 );
		return ret;
	},
	List:function(dir){
		var ret;
		Mind.Component.Ask( {
								componentName:'pluginList',
								data: {pName: this.name, dir:dir}
							},
							function (comps){
								comps= Mind.Component.Parse(comps);
								ret= comps.pluginList;
							}
						 );
		return ret;
	},
	/**/
	
	
	LoadTabPlugins: function(){
		for(var i=0; i<Mind.Plugins.tabPlugins.length; i++)
		{
			if(!Mind.Plugins[Mind.Plugins.tabPlugins[i]].mindLoaded)
			{
				setTimeout(Mind.Plugins.LoadTabPlugins, 1000);
				return;
			}
		}
		var o= document.getElementById('outputPanelPaneBody');
		if(!o)
		{
			return;
		}
		var p= o.parentNode;
		var tmp= '';
		for(var i=0; i<Mind.Plugins.tabPlugins.length; i++)
		{
			$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").tabs('add',
																	 Mind.Properties.pluginPath+'/'+Mind.Plugins.tabPlugins[i]+'/data/face.php',
																	 Mind.Plugins.tabPlugins[i]);
			tmp= p.getElementsByTagName('DIV');
			o.appendChild(tmp[tmp.length-1]);
			Mind.Plugins[Mind.Plugins.tabPlugins[i]].Load();
		}
	},
	LoadIcons: function(){
		var div= document.createElement('DIV');
			div.className= 'pluginIconButton';
		var p= document.getElementById('pluginsIcons');
		var tmp= '';
		for(var i=0; i<Mind.Plugins.useIcon.length; i++)
		{
			tmp= div.cloneNode(false);
			if(Mind.Plugins[Mind.Plugins.useIcon[i]].mindType == 'tab' || Mind.Plugins[Mind.Plugins.useIcon[i]].mindType == 'none' || Mind.Plugins[Mind.Plugins.useIcon[i]].mindType == 'window')
				modal= false;
			else
				modal= true;
			tmp.innerHTML= "<img src='"+Mind.Properties.pluginPath+'/'+Mind.Plugins.useIcon[i]+'/data/'+
							Mind.Plugins[Mind.Plugins.useIcon[i]].useIcon + "' "+
							((modal)? 'onclick="Mind.Plugins.OpenModal(\''+Mind.Plugins.useIcon[i]+'\');"': (Mind.Plugins[Mind.Plugins.useIcon[i]].mindType == 'window') ? 'onclick="Mind.Plugins.OpenWindow(\''+Mind.Plugins.useIcon[i]+'\');"' : 'onclick="Mind.Plugins.'+Mind.Plugins.useIcon[i]+'.Run();"')+
							" title='"+Mind.Plugins.useIcon[i]+"'/>";
			p.appendChild(tmp);
		}
	},
	OpenModal: function(p){
		var url= '../../'+Mind.Properties.pluginPath+'/'+p+'/data/face.php';
		Mind.Dialog.OpenModal(true, '770', '523', p, 'midle', url, 'form', function(){},false,false);
	},
	OpenWindow: function(p){
		var url= '../../'+Mind.Properties.pluginPath+'/'+p+'/data/face.php';
		Mind.Dialog.Open(true, 500, 360, p, 'midle', url);
	},
	Load: function ()
	{
		Mind.Component.Load("plugins",
			function(comps){
				comps= Mind.Component.Parse(comps);
				comps= comps['plugins'];
				var subMenuPlugins= document.getElementById('menu_pluginsItens');
				if(comps.length > 0)
				{
					subMenuPlugins.innerHTML= '';
					var tmpEl;
					for(var x in comps)
					{
						if(Mind.Plugins[comps[x].name])
							continue;
						tmpEl= document.createElement('A');
						tmpEl.href= '#';
						
						if(comps[x].conf.openAs == 'modal')
							tmpEl.setAttribute('class', "{action: 'Mind.Plugins.OpenModal(\""+comps[x].name+"\")'}");
						else if(comps[x].conf.openAs == 'window')
								tmpEl.setAttribute('class', "{action: 'Mind.Plugins.OpenWindow(\""+comps[x].name+"\")'}");
							 else
								tmpEl.setAttribute('class', "{action: 'Mind.Plugins."+comps[x].name+".Run()'}");
						tmpEl.innerHTML= comps[x].name;
						if(comps[x].conf.dependsOnProject)
						{
							tmpEl.setAttribute('projectDependence', 'true');
							if(!Mind.Project.attributes)
								tmpEl.setAttribute('disabled', 'true');
						}
						subMenuPlugins.appendChild(tmpEl);
						
						Mind.Plugins[comps[x].name]= {mindLoaded:false};
						
						$.getScript(Mind.Properties.pluginPath+'/'+comps[x].name+'/data/'+comps[x].name+'.js', function(){
							
							Mind.Plugins[comps[x].name].name	= comps[x].name;
							Mind.Plugins[comps[x].name].Save	= Mind.Plugins.Save;
							Mind.Plugins[comps[x].name].LoadFile= Mind.Plugins.LoadFile;
							Mind.Plugins[comps[x].name].Unlink	= Mind.Plugins.Unlink;
							Mind.Plugins[comps[x].name].MkDir	= Mind.Plugins.MkDir;
							Mind.Plugins[comps[x].name].List	= Mind.Plugins.List;
							Mind.Plugins[comps[x].name].Post	= Mind.Plugins.Post;
							Mind.Plugins[comps[x].name].src		= Mind.Properties.pluginPath+'/'+comps[x].name+'/data/';
						
							Mind.Plugins[comps[x].name].mindConf= comps[x].conf;
							Mind.Plugins[comps[x].name].mindLoaded= true;
							
							if(Mind.Plugins[comps[x].name].mindConf.openAs != 'tab')
								Mind.Plugins[comps[x].name].Load();
							if(Mind.Plugins[comps[x].name].mindConf.runAt == 'load')
								Mind.Plugins[comps[x].name].Run();
							
						}, true); // altera��o na pr�pria biblioteca jQuery para que funcionasse
						/*
							acrescentado um booleano que indica se o carregamento do script deve ser ass�ncrono ou n�o, por padrao, se nao mandar nada, ser� assincrono.
							linhas: 3335 e 3340
						*/
						
						if(comps[x].conf.openAs == 'tab')
						{
							//$("#"+Mind.Panel['bottom'].htmlElement.id+" .tabs").add('add', Mind.Properties.pluginPath+'/'+comps[x].name+'/data/face.php', comps[x].name);
							Mind.Plugins.tabPlugins.push(comps[x].name);
						}
						if(comps[x].conf.useIcon != 'false')
						{
							Mind.Plugins.useIcon.push(comps[x].name);
							Mind.Plugins[comps[x].name].useIcon = comps[x].conf.useIcon;
							Mind.Plugins[comps[x].name].mindType = comps[x].conf.openAs;
						}
						Mind.Plugins[comps[x].name].mindEvent = comps[x].conf.runAt;
						switch(comps[x].conf.runAt)
						{
							case 'load':
							{
								Mind.Plugins.runOnLoading.push(comps[x].name);
								break;
							}
							case 'run':
							{
								Mind.Plugins.runOnRunning.push(comps[x].name);
								break;
							}
							case 'open':
							{
								Mind.Plugins.runOnOpening.push(comps[x].name);
								break;
							}
							case 'report':
							{
								Mind.Plugins.runOnReporting.push(comps[x].name);
								break;
							}
						}
					}
					Mind.Plugins.LoadIcons();
					Mind.Plugins.LoadTabPlugins(); // add those tabs for each plugin that uses it
				};
			});
	},
	OnOpening:function(){
		for(var i=0; i<Mind.Plugins.runOnOpening.length; i++)
		{
			if(!Mind.Plugins[Mind.Plugins.runOnOpening[i]].mindLoaded)
			{
				setTimeout(Mind.Plugins.OnOpening, 1000);
				return;
			}
		}
		for(i=0; i<Mind.Plugins.runOnOpening.length; i++)
		{
			Mind.Plugins[Mind.Plugins.runOnOpening[i]].Run();
		}
	},
	OnRunning:function(){
		for(i=0; i<Mind.Plugins.runOnRunning.length; i++)
		{
			Mind.Plugins[Mind.Plugins.runOnRunning[i]].Run();
		}
	},
	OnReporting:function(){
		for(i=0; i<Mind.Plugins.runOnReporting.length; i++)
		{
			Mind.Plugins[Mind.Plugins.runOnReporting[i]].Run();
		}
	}
}
