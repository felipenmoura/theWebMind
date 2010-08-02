(function($){
	var initLayout = function() {
		var hash = window.location.hash.replace('#', '');
		var currentTab = $('ul.navigationTabs a')
							.bind('click', showTab)
							.filter('a[rel=' + hash + ']');
		if (currentTab.size() == 0) {
			currentTab = $('ul.navigationTabs a:first');
		}
		showTab.apply(currentTab.get(0));
		$('#colorpickerHolder').ColorPicker({flat: true, color:'666666', onChange:function(nada,rgb){
			//for(x in rgb) alert(x+': '+rgb[x]);
			if(Mind.MindEditor.ActivedColorPicker)
				Mind.MindEditor.ColorPickerChanged(rgb);
		}});
		
	};
	
	var showTab = function(e) {
		var tabIndex = $('ul.navigationTabs a')
							.removeClass('active')
							.index(this);
		$('div.tab').hide().eq(tabIndex).show();
	};
	EYE.register(initLayout, 'init');
})(jQuery)