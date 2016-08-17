<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?>
<!-- FILE -->
<div id="menu_file" class="menu">
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'580\',\'560\',\'New Project \',\'midle\',\'new_project.php\',\'form\')'}">New Project</a>
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'600\',\'170\',\'Open Project \',\'midle\',\'open_project.php\',\'form\')'}">Open Project</a>
	<a href="#" class="{action: 'Mind.Project.Save();'}" disabled='true' projectDependence='true'>Save Project</a> 
	<a href="#" class="{action: 'Mind.Project.Close();'}"  disabled='true' projectDependence='true'>Close Project</a> 
	<a rel="separator"> </a>
	<a href="#" class="{action: 'Mind.Project.Export()'}" disabled='true' projectDependence='true'>Export</a> 
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}">Import</a> 
	<a rel="separator"> </a>
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'300\',\'360\',\'Properties \',\'midle\',\'properties.php\')'}">Properties</a> 
	<a rel="separator"> </a>
	<a href="#" class="{action: 'document.location.href=\'exit.php\''}">Exit</a>
</div>
<!-- DISPLAY -->
<div id="menu_display" class="menu">
	<a href="#" class="{action: '(Mind.Panel[\'left\'].opened)? Mind.Panel[\'left\'].Close():Mind.Panel[\'left\'].Open();'}">Projects pane</a>
	<a href="#" class="{action: '(Mind.Panel[\'bottom\'].opened)? Mind.Panel[\'bottom\'].Close(): Mind.Panel[\'bottom\'].Open();'}">Output pane</a>
	<a href="#" class="{action: '(Mind.Panel[\'right\'].opened)?Mind.Panel[\'right\'].Close():Mind.Panel[\'right\'].Open();'}">MindApplications pane</a>
	<a href="#" class="{action: 'Mind.MindEditor.ShowTools(); Mind.Panel[\'bottom\'].Adjust();'}" disabled='true' projectDependence='true'>MindEditor Tools</a>
	<a href="#" class="{action: 'document.getElementById(\'mindEditorFullScreenButton\').click();'}" disabled='true' projectDependence='true'>MindEditor Full</a>
	<a rel="separator"> </a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}" disabled='true' projectDependence='true'>ER Diagram</a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}" disabled='true' projectDependence='true'>SQL-DDL</a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}" disabled='true' projectDependence='true'>Data Dictionary</a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}" disabled='true' projectDependence='true'>Details</a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}" disabled='true' projectDependence='true'>Notes</a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}">Issues</a>
</div>
<!-- TOOLS -->
<div id="menu_tools" class="menu">
	<a href="#" class="{action: 'Mind.Project.Run()'}" disabled='true' projectDependence='true'>Run/Simulate</a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}" disabled='true' projectDependence='true'>Debug</a>
	<a rel="separator"> </a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}" disabled='true' projectDependence='true'>Generate Version</a>
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
							<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'500\',\'400\',\'Manage Projects \',\'midle\',\'manage_project.php\',\'form\')'}">Projects</a>
							<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'430\',\'320\',\'Manage Users \',\'midle\',\'manage_user.php\',\'manage\')'}">Users</a>
							<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'430\',\'320\',\'Manage Languages \',\'midle\',\'manage_language.php\',\'form\')'}">Languages</a>
							<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'430\',\'320\',\'Manage DBMSs \',\'midle\',\'manage_dbms.php\',\'form\')'}">DBMSs</a>
							<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'430\',\'320\',\'Manage Plugins \',\'midle\',\'manage_plugin.php\',\'manage\')'}">Plugins</a>
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
	<a href="#" class="{action: ''}">No Plugins yet</a>
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
	<a href="../help_pt/index.php" target='_quot' class="{action: ''}">Topics</a>
	<a href="http://thewebmind.org/index.php#docs" target='_quot' class="{action: ''}">Documentation</a>
	<a href="http://thewebmind.org/index.php#faq" class="{action: ''}">FAQ</a>
	<a href="http://thewebmind.org/index.php#forum" class="{action: ''}">Forum</a>
	<a href="http://groups.google.com/group/thewebmind" class="{action: ''}">Google Groups</a>
	<a rel="separator"> </a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}">About</a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}">Credits</a>
	<a href="#" class="{action: 'document.title=(\'menu_1.3\')'}">Licenses</a>
	<a rel="separator"> </a>
	<a href="http://thewebmind.org" target='_quot' class="{}">WebSite</a>
</div>
<div id="submenu_options" class="menu">
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'500\',\'340\',\'My Personal Data \',\'midle\',\'personal_data.php\',\'form\')'}">My Personal Data</a>
	<a href="#" class="{action: 'Mind.Dialog.OpenModal(true,\'500\',\'340\',\'Options \',\'midle\',\'options.php\',\'form\')'}">Mind Options</a>
</div>