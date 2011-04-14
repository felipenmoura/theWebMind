<?php

    require('mind3rd/env/setup/Setup.php');

    $installationOk= Setup::checkRequirements();
    
    if(isset($_GET['setupGo']) && $_GET['setupGo'] && $installationOk)
    {
        echo "<pre>";
        Setup::install();
        echo "</pre>";
        exit;
    }
    
    echo "<p><img src='docs/ide/images/".((Setup::$phpVsOk)? 'o':'f').".png' /> PHP 5.3+<br/>";
    echo "<p><img src='docs/ide/images/".((Setup::$sqliteOk)? 'o':'f').".png' /> SQLite3 support<br/>";
    echo "<p><img src='docs/ide/images/".((Setup::$readLlineOk)? 'o':'w').".png' /> ReadLine";
    if(!Setup::$readLlineOk)
    {
        $tip=  "If you will not use the application trhough command line, but only via HTTP<br/>";
        $tip.= "this lib is not required. Once the application is installed you can access it using the HTTP protocol";
        echo "<div style='background-color:yellow;border:solid 1px brown'>$tip</div>";
    }
    echo "<p><img src='docs/ide/images/".((Setup::$projectsDir)? 'o':'w').".png' /> Write permissions in '".getcwd()."/mind3rd/projects/' to the user '".trim(shell_exec('whoami'))."'<br/>";
    echo "<p><img src='docs/ide/images/".((Setup::$sqliteDir)? 'o':'f').".png' /> Write permissions in '".getcwd()."/mind3rd/SQLite/' to the user '".trim(shell_exec('whoami'))."'<br/>";
    echo "<p><img src='docs/ide/images/".((Setup::$apiDir)? 'o':'f').".png' /> Write permissions in '".getcwd()."/mind3rd/API/' to the user '".trim(shell_exec('whoami'))."'<br/>";
    
    if(Setup::getSO() == 'WIN')
    {
        ///////////////
    }
    
    echo "<input type='button'
                 value='Verify again'
                 onclick='self.location.href=self.location.href' />";
    if(Setup::$installationOk)
    {
        echo "<input type='button'
                     value='Install'
                     onclick='self.location.href=self.location.href+\"?setupGo=true\"' />";
    }
    
    
    //echo PATH_SEPARATOR;