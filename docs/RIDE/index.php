<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
        <link rel="shortcut icon" href="images/logo.png" />
        <link href="css/ui-lightness/jquery-ui-1.8.12.custom.css" rel="stylesheet" type="text/css" />
        <link href="css/mind-RIDE.css" rel="stylesheet" type="text/css" />
        
		<script src='scripts/jquery.js'></script>
		<script src='scripts/jquery-ui.js'></script>
		<script src='scripts/mind-RIDE.js'></script>
	</head>
    <body>
        <?php
            require_once('classes/Service.php');
            if(Service::isAutorized())
            {
                include('components/ide.php');
            }else{
                if(isset($_POST['program'])
                   &&
                   $_POST['program']=='auth'
                   &&
                   Service::login())
                {
                    include('components/ide.php');
                }else
                    include('components/login.php');
            }
                
        ?>
    </body>
</html>