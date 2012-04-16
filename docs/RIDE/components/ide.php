<?php
    if(!Service::isAutorized())
        die('invalid access');
?>
<div id="appBody">
    <div id="menuPanel">
        <nav id="menuList">
            <ul>
               <li tabindex="0">
                   File
                   <ul>
                      <li>New project</li>
                      <li>Open</li>
                      <li class="depends-on-project">Save</li>
                      <li class="depends-on-project">Save as...</li>
                      <li class="separator"></li>
                      <li>Import</li>
                      <li class="depends-on-project">Export</li>
                      <li class="separator"></li>
                      <li>Exit</li>
                   </ul>
               </li>

               <li tabindex="0">
                   Edit
                   <ul>
                      <li class="depends-on-project">Project properties</li>
                      <li>User properties</li>
                      <li>User preferences</li>
                      <li class="separator"></li>
                      <li>Manage projects</li>
                      <li class="depends-on-admin">Manage users</li>
                   </ul>
               </li>

               <li tabindex="0">
                   View
                   <ul>
                      <li>Left Panel</li>
                      <li>Output Panel</li>
                   </ul>
               </li>

               <li tabindex="0">
                   Tools
                   <ul>
                      <li class="depends-on-project">Analyze project</li>
                      <li class="depends-on-project">Update project</li>
                      <li class="depends-on-project">Commit project</li>
                      <li class="depends-on-project">Generate...</li>
                   </ul>
               </li>

               <li tabindex="0">
                   Help
                   <ul>
                      <li>Documentation</li>
                      <li>User Interface</li>
                      <li>User group</li>
                      <li class="separator"></li>
                      <li>Report a bug</li>
                      <li>Contribute</li>
                   </ul>
               </li>
            </ul>
        </nav>
    </div>
    <div id="toolsPanel">
        <br/>
    </div>
    <div id="editorPanel">
        <div id="editor"></div>
    </div>
    <div id="consolePanel">
        <br/>
    </div>
</div>