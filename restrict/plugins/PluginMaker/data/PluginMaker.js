Mind.Plugins.PluginMaker= {
	/* needed methods */
	Run: function(){
		//this.Save('', '');
	},
	Load: function(){
		
	},
	/***************************/
	Init: function(){
		
	},
	AddAuthorRow: function(){
		$('#PluginMaker_authorsList').append("<div><input type='text' name='PluginMaker_authorName[]'/>&nbsp;"+
												  "<input type='text' name='PluginMaker_authorEmail[]'/></div>");
	}
};