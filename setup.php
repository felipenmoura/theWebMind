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
?>
    <style type='text/css'>
        body{
            font-family: Tahoma, Arial;
            background-image: -webkit-linear-gradient(-90deg, #ccc, #fff 30%);
        }
        .report{
            padding: 6px;
            border: solid 2px gray;
            background-color: #eef;
            border-radius: 16px;
            font-weight: bold;
            color: #111;
        }
        .note{
            background-color: #ffffcc;
            margin: 4px;
            padding: 4px 6px 4px 6px;
            border: solid 2px #e78f08;
            border-width: 2px 0px 2px 0px;
        }

        .button{
            float: right;
            width: 200px;
            margin-top: 4px;
            margin: 8px;
            font-size: 24px;
            text-align: center;
            padding: 4px;
            cursor: pointer;

            text-align: center;
            background: #39C;
            color: white;
            text-shadow: 0 0 2px #fff;
            background-image: -webkit-linear-gradient(-45deg, rgba(255,255,255,0), rgba(255,255,255,.1) 60%, rgba(255,255,255,0) 60%);
            background-image:    -moz-linear-gradient(-45deg, rgba(255,255,255,0), rgba(255,255,255,.1) 60%, rgba(255,255,255,0) 60%);
            background-image:      -o-linear-gradient(-45deg, rgba(255,255,255,0), rgba(255,255,255,.1) 60%, rgba(255,255,255,0) 60%);
            background-image:     -ms-linear-gradient(-45deg, rgba(255,255,255,0), rgba(255,255,255,.1) 60%, rgba(255,255,255,0) 60%);
            background-image:         linear-gradient(-45deg, rgba(255,255,255,0), rgba(255,255,255,.1) 60%, rgba(255,255,255,0) 60%);
            border-radius: 5px;
            border: 1px solid #fff;
            box-shadow: 0 0 8px #000;
            font-weight: bold;
        }
        .header{
            margin: 10px;
        }
        h1{
            margin: 0px;
        }
    </style>
    <div class="header">
        <h1>theWebMind</h1>
        <spam>
            This is the setup page for theWebMind.
        </spam>
    </div>
    <?php
    require('mind3rd/env/setup/Setup.php');

    $installationOk= Setup::checkRequirements();

    echo "<form action='setup.php?setupGo=true' method='POST'><div class='report'>";
    if(isset($_GET['setupGo']) && $_GET['setupGo'] && $installationOk)
    {
        echo "<pre>";
        Setup::install();
        echo "</pre></div>";
        echo "<div class='note'>";
        echo "Well, where to go now?<br/>";
        $apiSrc= str_replace('setup.php', '', $_SERVER['HTTP_REFERER']);
        echo "You can check the documentation at <a href='http://thewebmind.org/docs' target='_blanq'>http://thewebmind.org/docs</a><br/>";
        echo "Make post requisitions to the API in your webserver at <a href='".$apiSrc."' target='_quot'>".$apiSrc."</a><br/>";
        echo "And if you downloaded the IDE, you can probably access it at <a href='".$apiSrc."ide'>".$apiSrc."ide</a>";
        echo "</div>";
        exit;
    }
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
    echo "</div>";

    if(Setup::getSO() == 'WIN')
    {
        echo "<div class='note'>NOTE: In windows, the system works only via HTTP, not accepting commands from
              command line/console.</div>";
    }else{
            echo "<div class='note'>NOTE: Installing using this interface, you will be able to access the system
                  only via HTTP or, using command line straight from the directory where
                  the system is and then, running <i>mind</i>.
                  If you want to use the program simply typing <i>mind</i> from any directory in your console,
                  you can perform this installation from your console typing, in the mind3rd's directory:<br/>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>sudo php mind install</i></div>";
         }

    /*echo "Admin's password: <input type=password name='adminEmail' /><br/>";
    echo "Admin's e-mail: <input type=text name='adminPWD' /><br/>";*/
    echo "<input type='button'
                 value='Verify again'
                 onclick='self.location.href=self.location.href'
                 class='button' />";
    if(Setup::$installationOk)
    {
        echo "<input type='submit'
                     value='Install'
                     class='button' />";
    }
    echo "</form>";