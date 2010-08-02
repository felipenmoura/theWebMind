<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	$p= $_GET['p'];
	
	if(Project::hasProject($p))
	{
		if(isset($_GET['projectdirtoshow']))
		{
			$original_dir= $_MIND['publishDir'].'/'.$p.'/root/';
			$userDir= $_MIND['publishDir'].'/'.$p.'/root/';
		}else{
				$userDir= $_MIND['userDir'].'/'.$_SESSION['user']['login'].'/temp/'.$p.'/root/';
				$original_dir= $userDir;
			 }
		
		if(isset($_GET['zip']))
		{
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$p.'.zip"'); 
			header('Content-Transfer-Encoding: binary');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Pragma: no-cache');

			$path= str_replace(basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_FILENAME']);
			$from= 'cd '.$path.'; cd '.$_MIND['rootDir'].'; cd '.$original_dir.'../;';
			$ret= exec($from.' zip -r mind/'.$p.'.zip root/');
			
			readfile($_MIND['rootDir'].$original_dir.'../mind/'.$p.'.zip');
			exit;
		}
		if(isset($_GET['f']))
		{
			if(file_exists($_GET['f']))
			{
				?>
				<html>
					<head>
						<!-- SyntaxHighlighter -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shCore.js"></script><!-- jQuery -->
						
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushPhp.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushAS3.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushColdFusion.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushBash.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushCpp.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushCSharp.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushCss.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushJava.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushJavaFX.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushJScript.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushPerl.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushPlain.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushPowerShell.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushPython.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushRuby.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushScala.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushSql.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushVb.js"></script><!-- jQuery -->
						<script type="text/javascript" src="../scripts/SyntaxHighlighter/shBrushXml.js"></script><!-- jQuery -->
						
						<!-- SyntaxHighlighter -->
						<link rel="stylesheet" type="text/css" href="../styles/SyntaxHighlighter/shCore.css"/>
						<link rel="stylesheet" type="text/css" href="../styles/SyntaxHighlighter/shThemeRDark.css"/>
						<style type='text/css'>
							body, td, li
							{
								font-family: Tahoma, Sans-Serif;
								color:#fff;
							}
						</style>
						<script>
							//SyntaxHighlighter.config.clipboardSwf = '/pub/sh/2.1.364/scripts/clipboard.swf';
							SyntaxHighlighter.defaults['smart-tabs'] = false;
							SyntaxHighlighter.defaults['wrap-lines'] = false;
							SyntaxHighlighter.all();
							function showContent()
							{
								parent.document.getElementById('showCodeFrame').style.display= '';
								parent.document.getElementById('showFileContentLoader').style.display= 'none';
							}
						</script>
					</head>
					<body leftmargin='0' topmargin='0' bottommargin='0' rightmargin='0'
						  style='margin:0px; padding:0px;background-color:#1B2426;'
						  onload="showContent()">
						<div style='border-bottom:solid 1px #666;
									background-color:#e0e0e0;
									cursor:default;
									width:100%;
									color:#000;
									font-weight:bold;
									font-size:12px;'>
							<nobr><?php echo preg_replace('/^.*'.$_GET['p'].'\/root\//', '', $_GET['f']); ?></nobr>
						</div>
						<?php
							$ext= strtolower(preg_replace('/^.*\./', '', $_GET['f']));
							
							if($ext == 'phtml')
								$ext= 'html';
								
							if($ext == 'inc') // I should tell you ... do NOT use .inc files ... even though, if you wan to... ok
								$ext= 'php';
							if($ext == 'txt' || $ext == 'json')
								$ext= 'plain';
								
							if($ext == 'ini')
								$ext= 'bash/shell';
								
							switch($ext)
							{
								case 'swf':
								{
									?>
									<table style='width:100%; height:100%; background-color:#ddd; background-repeat:repeat; background-image:url(<?php echo $_MIND['rootDir'].$_MIND['imageDir']; ?>/back_der.gif);'>
										<tr>
											<td>
												<center>
													<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
															codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0">
														<param name="movie"
															   value="<?php echo $_GET['f']; ?>">
														<param name="quality"
															   value="high">
														<embed src="<?php echo $_GET['f']; ?>"
															   quality="high"
															   pluginspage="http://www.macromedia.com/go/getflashplayer"
															   type="application/x-shockwave-flash">
														</embed>
													</object>
												</center>
											</td>
										</tr>
									</table>
									<?php
									break;
								}
								case 'png':
								case 'gif':
								case 'jpg':
								case 'bmp':
								{
									?>
										<table style='width:100%; height:100%; background-color:#ddd; background-repeat:repeat; background-image:url(<?php echo $_MIND['rootDir'].$_MIND['imageDir']; ?>/back_der.gif);'>
											<tr>
												<td>
													<center>
														<img src="<?php echo $_GET['f']; ?>"/>
													</center>
												</td>
											</tr>
										</table>
									<?php
									break;
								}
								case 'php':
								case 'phtml':
								case 'html':
								case 'ini':
								case 'js':
								case 'java':
								case 'jfx':
								case 'javafx':
								case 'plain':
								case 'bash/shell':
								case 'css':
								case 'xml':
								case 'xhtml':
								case 'xslt':
								case 'cpp':
								case 'sql':
								case 'jsp':
								case 'cpp':
								case 'c':
								case 'py':
								case 'pl':
								case 'pearl':
								case 'rails':
								case 'ror':
								case 'ruby':
								case 'vb':
								case 'vbnet':
								case 'as3':
								case 'actionscript3':
								case 'cs':
								case 'csharp':
								case 'aspx':
								case 'asp':
								{
									?>
										<pre class='brush:<?php echo $ext; ?>;' style='margin:0px; padding:0px;'><?php echo preg_replace('/\t/', '    ', trim(htmlentities(file_get_contents($_GET['f'])))); ?></pre>
									<?php
									break;
								}
								default:{
									?>
										<div style='width:100%;
													height:100%;
													background-color:#ccc;'>
											This file cannot be rendered.<br/>
											But you can still download it and open with a specific software clicking 
											<a href="<?php echo $_GET['f']; ?>" target='_quot'>here</a>.
										</div>
									<?php
								}
							}
						?>
					</body>
				</html>
				<?php
			}
			exit;
		}
		
		function throughDir($dir)
		{
			GLOBAL $_MIND;
			GLOBAL $userDir;
			GLOBAL $projDir;
			GLOBAL $curVersion;
			
			$d = dir($dir);
			$tmpDir;
			
			while (false !== ($entry = $d->read()))
			{
				if(substr($entry, 0, 1) != '.')
				{
					$tmpDir= str_replace($userDir, '', $dir.'/'.$entry);
					$pos= sizeof(explode('/', $tmpDir)) -4;
					if(is_dir($dir.'/'.$entry))
					{
						$id= "files_explorer_".$pos.basename($tmpDir).rand(0,9999);
						echo "<div onclick=\"$('#".$id."').toggle('slow');\" style='white-space:no-wrap;'><span style='color:#ddd;'>".str_repeat("&nbsp;|&nbsp;", $pos)."</span><nobr><img src='".$_MIND['imageDir']."/icon_folder.gif' width='16'/> ".basename($tmpDir).'</nobr></div>';
						echo "<div style='display:none;' id='".$id."'>";
							throughDir($dir.'/'.$entry);
						echo "</div>";
					}else{
							echo "<nobr><span style='color:#ddd;'>".str_repeat("&nbsp;|&nbsp;", $pos).'</span>';
							echo "<img src='".$_MIND['imageDir']."/icon_file.gif' width='16'/>";
							echo '<span style="color:#666;" onclick="showCurrentCodeOf(\''.$dir.'/'.$entry.'\')">'.$entry.'</span></nobr><br/>';
						 }
					
				}
			}
			$d->close();
		}
	?>
		<div style="width:210px;
					height:99%;
					overflow:auto;
					border:solid 1px #333;
					margin-right:2px;
					float:left;"
			 id="listFilesDiv">
			<a href='<?php echo $original_dir; ?>'
			   target='_quot'
			   style='text-decoration:none;padding:0px; margin:0px;'>
				<div style='border:solid 1px #666;
							background-color:#e0e0e0;
							cursor:pointer;
							height:20px;
							margin:1px;'
					 onmouseover="this.style.backgroundColor= '#f0f0f0'"
					 onmouseout="this.style.backgroundColor= '#e0e0e0'">
					See it Running
				</div>
			</a>
			<a href='<?php echo $_SERVER['PHP_SELF'].'?zip=1&p='.$p; ?>'
			   target='downloadFrame'
			   style='text-decoration:none;padding:0px; margin:0px;'>
				<div style='border:solid 1px #666;
							background-color:#e0e0e0;
							cursor:pointer;
							height:20px;
							margin:1px;'
					 onmouseover="this.style.backgroundColor= '#f0f0f0'"
					 onmouseout="this.style.backgroundColor= '#e0e0e0'">
					Download current
				</div>
			</a>
			<a href="JavaScript: $('#addressHereTreeDiv').toggle(); void(0);"
			   style='text-decoration:none;padding:0px; margin:0px;'>
				<div style='border:solid 1px #666;
							background-color:#e0e0e0;
							cursor:pointer;
							height:20px;
							margin:1px;'
					 onmouseover="this.style.backgroundColor= '#f0f0f0'"
					 onmouseout="this.style.backgroundColor= '#e0e0e0'">
					Files Tree
				</div>
			</a>
			<div style='padding:2px;'
				 id='addressHereTreeDiv'>
			<?php
				throughDir($_MIND['rootDir'].$userDir);
			?>
			</div>
		</div>
		<div id='showCodePanel'
			 style='overflow:hidden;
					height:99%;
					border:solid 1px #333;
					margin-right:2px;'>
			<img src="<?php echo $_MIND['imageDir']; ?>/load.gif"
				 style='position:absolute;
						display:none;'
				 id='showFileContentLoader'>
			<iframe style='width:100%;height:100%;border:none;'
					frameborder='0'
					id='showCodeFrame'
					src="about:blanc">
			</iframe>
		</div>
		<iframe id='downloadFrame'
				name='downloadFrame'
				style='display:none;'>
		</iframe>
		<script>
			
			var showCurrentCodeOf= function(file){
				//document.getElementById('showCodePanel').innerHTML= "Loading...";
				document.getElementById('showFileContentLoader').style.display= '';
				document.getElementById('showCodeFrame').style.display= 'none';
				document.getElementById('showCodeFrame').src= Mind.Properties.path+'/address_here.php?p='+ Mind.Project.attributes.name+'&f='+file;
				return;
			}
			
			$(document).ready(function(){
				$('#listFilesDiv').resizable({ handles: 'e' });
			});
		</script>
		<?php
	}
?>