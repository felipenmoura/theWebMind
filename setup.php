<?php
    /**
     * This file is part of TheWebMind 3rd generation.
     * 
     * @author Felipe Nascimento de Moura <felipenmoura@gmail.com>
     * @license licenses/mind3rd.license
     * 
     * This file will perform the instalation of the system through the browser.
     * The source code will still get more beautiful, the focus now was to set
     * it to work in bouth Windows and Unix based Operational Systems.
     * 
     * For now, Windows does not have support for readline lib with PHP, what
     * forces us to use it *only* via HTTP on Windows.
     */
    require('mind3rd/env/setup/Setup.php');

    $installationOk= Setup::checkRequirements();
    
    if(isset($_GET['setupGo']) && $_GET['setupGo'] && $installationOk)
    {
        echo "<pre>";
        Setup::install();
        echo "</pre>";
        exit;
    }
    
    echo "<form action='setup.php?setupGo=true' method='POST'>";
    echo "<p><img src='docs/ide/images/".((Setup::$phpVsOk)? 'o':'f').".png' /> PHP 5.3+<br/>";
    echo "<p><img src='docs/ide/images/".((Setup::$sqliteOk)? 'o':'f').".png' /> SQLite3 support<br/>";
    
    /*
    if(!Setup::$readLlineOk)
    {
        if(Setup::getSO() != 'WIN')
        {
            echo "<p><img src='docs/ide/images/".((Setup::$readLlineOk)? 'o':'w').".png' /> ReadLine";
            $tip=  "If you will not use the application through command line, but only via HTTP<br/>";
            $tip.= "this lib is not required. Once the application is installed you can access it using the HTTP protocol";
            echo "<div style='background-color:yellow;border:solid 1px brown'>$tip</div>";
        }else{
            echo "<br/>Sorry. Windows does not support the readline library to be used through command line.";
            echo "<br/>Although, you can use it via HTTP in your browser.";
        }
    }*/
    echo "<p><img src='docs/ide/images/".((Setup::$projectsDir)? 'o':'w').".png' /> Write permissions in '".getcwd()."/mind3rd/projects/' to the user '".trim(shell_exec('whoami'))."'<br/>";
    echo "<p><img src='docs/ide/images/".((Setup::$sqliteDir)? 'o':'f').".png' /> Write permissions in '".getcwd()."/mind3rd/SQLite/' to the user '".trim(shell_exec('whoami'))."'<br/>";
    echo "<p><img src='docs/ide/images/".((Setup::$apiDir)? 'o':'f').".png' /> Write permissions in '".getcwd()."/mind3rd/API/' to the user '".trim(shell_exec('whoami'))."'<br/>";
    
    if(Setup::databaseAlreadyExists())
    {
        echo "<p><img src='docs/ide/images/w.png' /> Database already exists! It will *NOT* be replaced or even touched.<br/>
              If you are trying to reinstall the system, consider removing the file <br/><i>mind3rd/SQLite/mind</i>(the database itself...all the old projects will be lost, them) and then
              try to re-installing the application.<br/><br/>";
    }
    echo "Admin's password: <input type=password name='adminEmail' /><br/>";
    echo "Admin's e-mail: <input type=text name='adminPWD' /><br/>";
    echo "<input type='button'
                 value='Verify again'
                 onclick='self.location.href=self.location.href' />";
    if(Setup::$installationOk)
    {
        echo "<input type='submit'
                     value='Install' />";
    }
    echo "<hr/>";
    if(Setup::getSO() == 'WIN')
    {
        echo "NOTE: In windows, the system works only via HTTP, not accepting commands from 
              command line/console.";
    }else{
            echo "NOTE: Installing using this interface, you will be able to access the system
                  only via HTTP or, using command line straight from the directory where
                  the system is and then, running <i>mind</i>.
                  If you want to use the program simply typing <i>mind</i> from any directory in your console, 
                  you can perform this installation from your console typing, in the mind3rd's directory:<br/>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>sudo php mind install</i>";
         }