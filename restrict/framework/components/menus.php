<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?>
<!-- FILE -->
<div id="menu_file" class="menu">
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'580\',\'560\',\'New Project \',\'midle\',\'new_project.php\',\'form\',function(){},true)'}" shortCut="ctrl+n">New Project</a>
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'600\',\'170\',\'Open Project \',\'midle\',\'open_project.php\',\'form\', function(){},true)'}" shortCut="ctrl+o">Open Project</a>
	<a href="#" class="{menu: 'submenu_recent'}" id="mind_file_menu" disabled='true' shortCut="">Open Recent</a>
	<a href="#" class="{action: 'Mind.Project.Save();'}" disabled='true' projectDependence='true' shortCut="ctrl+s">Save Project</a> 
	<a href="#" class="{action: 'Mind.Project.Close();'}"  disabled='true' projectDependence='true' shortCut="ctrl+w">Close Project</a> 
	<a rel="separator"> </a>
	<a href="#" class="{action: 'Mind.View.User.New();'}">New User</a> 
	<a rel="separator"> </a>
	<a href="#" class="{action: 'Mind.Project.Export()'}" disabled='true' projectDependence='true' shortCut="ctrl+e">Export</a> 
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'600\',\'170\',\'Import \',\'midle\',\'import.php\',false)'}">Import</a> 
	<a rel="separator"> </a>
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'405\',\'400\',\'Properties \',\'midle\',\'properties.php\')'}">Properties</a> 
	<a rel="separator"> </a>
	<a href="#" class="{action: 'document.location.href=\'exit.php\''}">Exit</a>
</div>
<!-- DISPLAY -->
<div id="menu_display" class="menu">
	<a href="#" class="{action: '(Mind.Panel[\'left\'].opened)? Mind.Panel[\'left\'].Close():Mind.Panel[\'left\'].Open();'}">Projects pane</a>
	<a href="#" class="{action: '(Mind.Panel[\'bottom\'].opened)? Mind.Panel[\'bottom\'].Close(): Mind.Panel[\'bottom\'].Open();'}">Output pane</a>
	<!--<a href="#" class="{action: '(Mind.Panel[\'right\'].opened)?Mind.Panel[\'right\'].Close():Mind.Panel[\'right\'].Open();'}">MindApplications pane</a>-->
	<a href="#" class="{action: 'Mind.MindEditor.ShowTools(); Mind.Panel[\'bottom\'].Adjust();'}" disabled='true' projectDependence='true'>MindEditor Tools</a>
	<a href="#" class="{action: 'Mind.MindEditor.SetFull()'}" disabled='true' projectDependence='true' shortCut="F6">MindEditor Full</a>
	<a rel="separator"> </a>
	<a href="#" class="{action: 'Mind.Project.SeeCurrentUserFiles()'}" disabled='true' projectDependence='true'  shortCut="F10">Temp Project Files</a>
	<a href="#" class="{action: 'Mind.Project.SeeCurrentProjectFiles()'}" disabled='true' projectDependence='true' >Current Project Files</a>
	<a rel="separator"> </a>
	<a href="#" class="{action: 'Mind.Panel[\'bottom\'].Focus(\'infoConfTab\')'}" disabled='true' projectDependence='true'>About</a>
	<a href="#" class="{action: 'Mind.Panel[\'bottom\'].Focus(\'DDLTab\')'}" disabled='true' projectDependence='true'>SQL-DDL</a>
	<a href="#" class="{action: 'Mind.Panel[\'bottom\'].Focus(\'ERDTab\')'}" disabled='true' projectDependence='true'>ER Diagram</a>
	<a href="#" class="{action: 'Mind.Panel[\'bottom\'].Focus(\'DDTab\')'}" disabled='true' projectDependence='true'>Data Dictionary</a>
</div>
<!-- TOOLS -->
<div id="menu_tools" class="menu">
	<a href="#" class="{action: 'Mind.Project.Run()'}" disabled='true' projectDependence='true' shortCut="F5">Run/Simulate</a>
	<!--<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}" disabled='true' projectDependence='true'>Debug</a>-->
	<a href="#" class="{action: 'Mind.Project.Generate()'}" disabled='true' projectDependence='true' shortCut="F7">Generate</a>
	<a href="#" class="{action: 'Mind.Project.Update()'}" disabled='true' projectDependence='true' shortCut="F8">Update</a>
	<a href="#" class="{action: 'Mind.Project.Commit()'}" disabled='true' projectDependence='true' shortCut="F9">Commit</a>
	<a rel="separator"> </a>
	<a href="#" class="{menu:'submenu_options'}">Options</a>
</div>
<!-- MANAGE -->
<?php
	if($_SESSION['user']['login'] == 'admin')
	{
		?>
			<div id="menu_manager" class="menu">
				<div id="manage_submenu" class="menu">
							<a href="#" class="{action: 'Mind.Menus.Manage.projects.Action()'}">Projects</a>
							<a href="#" class="{action: 'Mind.Menus.Manage.users.Action()'}">Users</a>
							<!--<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'430\',\'320\',\'Manage Languages \',\'midle\',\'manage_language.php\',\'form\')'}">Languages</a>
							<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'430\',\'320\',\'Manage DBMSs \',\'midle\',\'manage_dbms.php\',\'form\')'}">DBMSs</a>-->
							<a href="#" class="{action: 'Mind.Menus.Manage.plugins.Action()'}">Plugins</a>
				</div>
			</div>
		<?php
	}else{
			?>
				<a href="#" class="return false">No options</a>
			<?php
		 }
?>
<!-- PLUGINS -->
<div id="menu_plugins" class="menu">
	<div id='menu_pluginsItens'>
		<a href="#" class="{action: ''}">No Plugins yet</a>
	</div>
	<a rel="separator"> </a>
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'500\',\'340\',\'Manage Plugins \',\'midle\',\'find_plugins.php\')'}">Find Plugins</a>
</div>
<!-- DEVELOPER -->
<?php
	if($_MIND['showDeveloperMenu'])
	{
	?>
		<div id="menu_developer" class="menu">
			<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'500\',\'340\',\'Create Plugins \',\'midle\',\'new_plugin.php\')'}">Create a Plugin</a>
			<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'500\',\'340\',\'Create/Edit Languages \',\'midle\',\'manage_languages.php\')'}">Create/Edit Languages</a>
			<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'500\',\'340\',\'Create a DBMS \',\'midle\',\'new_dbms.php\')'}">Create a DBMS</a>
			<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'500\',\'340\',\'Create a Module \',\'midle\',\'new_module.php\')'}">Create a Module</a>
			<a rel="separator"> </a>
			<a href="http://thewebmind.org/index.php#docs" target='_quot' class="{action: ''}">Developer Documentations</a>
			<a href="http://code.google.com/p/webmind/" target='_quot' class="{action: ''}">Google Codes</a>
		</div>
	<?php
	}
?>
<!-- HELP -->
<div id="menu_help" class="menu">
	<a href="#" target='_quot' class="{action: 'Mind.Help.Open()'}" shortCut="F1">Topics</a>
	<a href="http://docs.thewebmind.org" target='_quot' target='_quot' class="{action: ''}">Documentation</a>
	<a href="http://thewebmind.org/faq" target='_quot' class="{action: ''}">FAQ</a>
	<a href="http://groups.google.com/group/thewebmind" target='_quot' class="{action: ''}">Google Groups</a>
	<a rel="separator"> </a>
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'405\',\'400\',\'Properties \',\'midle\',\'properties.php\')'}">About</a>
	<a href="#" class="{action: ''}">Credits</a>
	<a href="#" class="{action: ''}">Licenses</a>
	<a rel="separator"> </a>
	<a href="#" class="{action: 'Mind.UpdateItSelf()'}">Update</a>
	<a href="#" class="{action: 'Mind.FeedBack.Open()'}">Feedback</a>
	<a href="http://thewebmind.org" target='_quot' class="{}">WebSite</a>
</div>
<div id="submenu_options" class="menu">
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'500\',\'340\',\'My Personal Data \',\'midle\',\'personal_data.php\',\'form\',function(){},true)'}">My Personal Data</a>
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'500\',\'370\',\'Options \',\'midle\',\'options.php\',\'form\', true, true)'}">Mind Options</a>
</div>
<div id="submenu_recent" class="menu">
</div>