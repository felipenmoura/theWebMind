Mind.Component.Load({
	componentName : "projectList",
	data : [{code:"10", name : "teste"}]
	},
	'home',
	'right_panel',
	'console',
	function(comps){
			if(comps.substring(0,5) == 'Mind.')
			{
				eval(comps);
				return;
			}
			comps= Mind.Component.Parse(comps);
			Mind.Panel["left"].Update(comps['projectList']);
			Mind.Panel["center"].Update(comps['home']);
			Mind.Panel["right"].Update(comps['right_panel']);
			//Mind.Panel["bottom"].Update(comps['console']);
			var cookie= leCookie('theWebMind_currentProject');
			cookie= cookie!=null? cookie: false;
			Mind.Project.Load(cookie);
		});