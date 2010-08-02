<?php
	include('restrict/config/mind.php');
	include('restrict/'.$_MIND['framework']);
	
	$_MIND['rootDir']= 'restrict/'.$_MIND['rootDir'];
	
	if(sizeof(($users= @$_MIND['fw']->getUsersList())) == 0)
	{
		$u= new User();
		$u->name('Administrator');
		$u->login('admin');
		$u->pwd('admin');
		$u->position('admin');
		$u->status(1);
		$u->email('contato@thewebmind.org');
		
		if($u->save())
		{
			$info= "<table style='width:100%;
					color:#444;
					background-color:#eec;
					border-bottom:solid 2px #669;
					padding: 7px;
					text-align:justify;
					position:absolute;
					top:0px;
					height:100px;
					bottom:0px;'
			 id='selfDestroyDiv'
			 align='center'>
			<tr>
				<td>
					<img src='images/tip.png'>
				</td>
				<td>
					Oh! I see this is your first time around here.<br/>To you to start working with the system, the user <b><i>admin</i></b> has been created, with the password <b><i>admin</i></b><br/>
					I advise you to change this password as soon as possible.
				</td>
				<td style='padding-top:20px;'>
					<center>
						<input type='image'
							   value='Ok, thanks'
							   src='images/botao_ok.png'
							   onclick='okThanks();'>
					</center>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					This message will self destruct in <b><span id='selfDestroyCounter'>60</span></b>
				</td>
			</tr>
		</table>";
		}else{
				echo "<body style='margin:0px;'>
			<style type='text/css'>
				BODY, TD
				{
					font-family: Tahoma;
					color: #555;
					font-size: 12px;
				}
			</style>
			 <table style='width:100%;
					color:#444;
					background-color:#eec;
					border-bottom:solid 2px #669;
					padding: 7px;
					text-align:justify;
					position:absolute;
					top:0px;
					bottom:0px;'
			 id='selfDestroyDiv'
			 align='center'>
			<tr>
				<td>
					<img src='images/error.png'>
				</td>
				<td>
					<b>Error</b>: An error accurred when trying to access some files.<br/>
					<b>Tip</b>: Please, set permissions to your PHP server, to change AT LEAST these directories:<br/>
					<ul>
						<li>
							restrict/".$_MIND['userDir']."
						</li>
						<li>
							restrict/".$_MIND['publishDir']."
						</li>
					</ul>
				</td>
				<td style='padding-top:20px;'>
					<center>
						<input type='image'
							   src='images/try_again.png'
							   onclick='self.location.href= self.location.href'>
					</center>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					Due to keep theWebMind up to date, we advise you to set these permission to its whole directory
				</td>
			</tr>
		</table>";
				exit;
			 }
	}else
		$info= false;
?>
<html>
	<head>
		<title>
			theWebMind
		</title>
		<link rel="shortcut icon" href="restrict/favico.png"/>
		<script src="restrict/framework/scripts/jquery.js"></script>
		<script src="restrict/framework/scripts/coockies.js"></script>
		<script>
			function okThanks()
			{
				clearInterval(vSelfDestroyCounter);
				$('#selfDestroyDiv').fadeOut('slow', function(){
					document.getElementById('selfDestroyDiv').parentNode.removeChild(document.getElementById('selfDestroyDiv'));
				});
			}
			function validate()
			{
				if(document.getElementById('name').value.replace(/ /g, '') == ''
					||
				   document.getElementById('pwd').value.replace(/ /g, '') == '')
				{					
					$("#mind_login_message").html("Both fields are needed");
					$("#mind_login_message").fadeIn("slow");
					return false;
				}
				if(document.getElementById('rememberMe').checked)
				{
					top.gravaCookie('theWebMind', top.document.getElementById('name').value, new Date(new Date().getTime() + (12*30*24*60*1000)));
				}
				return true;
			}
			$(document).ready(function(){
				document.getElementById('name').focus();
				var logInSavedValue= leCookie('theWebMind');
				if(logInSavedValue)
				{
					document.getElementById('rememberMe').checked= true;
					document.getElementById('name').value= logInSavedValue;
					document.getElementById('pwd').focus();
				}
			});
			function hideCCMsg()
			{
				document.getElementById('capsLockMessage').style.display= 'none';
			}
			function showCCMsg(o)
			{
				var m= document.getElementById('capsLockMessage');
				m.style.display= '';
			}
			function checkCapsLock(e, o)
			{
				var k=0;
				var sk=false;

				// Internet Explorer
				if(document.all)
				{
					k=e.keyCode;
				}else if(document.getElementById) // browsers
					  {
						k=e.which;
					  }
				sk= e.shiftKey;
				// If it is in UpperCase when the shift is NOT pressed, or it is in lowerCase and the shift key is pressed
				if (
					((k > 64 && k < 91) && !sk)
					||
					((k > 96 && k < 123) && sk)
					)
				{
					showCCMsg(o);
					setTimeout(function(){hideCCMsg();}, 3000);
				}else{
						hideCCMsg();
					 }
			}

		</script>
		<style type='text/css'>
			BODY, TD
			{
				font-family: Tahoma;
				color: #999;
				font-size: 12px;
			}
			BODY
			{
				background-color: #fff;
			}
			fieldset, fieldset td
			{
			}
			#theFormToANewAccount, #theFormToANewAccount td
			{
			}
			#mind_login_message
			{
				color:red;
				height:22px;
			}
			.title
			{
				font-size: 20px;
				font-weight: bold;
			}
			.botao
			{
				width: 95px;
				height: 20px;
				background-image: url(images/botao.jpg);
				background-position: center;
				background-repeat: no-repeat;
				background-color: #f0f0f0; /*EEECFB*/
				border: none;
			}
			.login_background
			{
				background-image:url(images/login_background.jpg);
				background-repeat:no-repeat;
				width:658px; /* 780px; */
				height: 490px; /* 598px; */
				border:solid 1px #236f77;
			}
			#contentHere
			{
				padding-top:70px;
			}
			.iptText
			{
				font-family:arial;							
				border:solid 1px #146169;
				background-color: #fff;
				color:#146169;				
			}
			.footer
			{
				margin-top:25px;
				*margin-top:110px; /* IE Sucks*/
				margin-left:7px;
				padding-top:7px;
				padding-left:12px;
				width: 400px;
			}
			#capsLockMessageContainer
			{
				position:absolute;
				left:0px;
				top:0px;
				width:100%;
				height:100%;
			}
			#capsLockMessage
			{
				position:absolute;
				margin-top:22px;
				width:240px;
				height:75px;
				border:solid 1px #666;
				background-color:#ffb;
				color:#333;
				padding:4px;
				text-align:justify;
				-moz-border-radius:5px;
				-webkit-border-radius:5px;
			}
			#selfDestroyCounter
			{
				color:#f00;
			}
			.extra-links a
			{
			}
			.extra-links img
			{
				margin-top:10px;
				border:none;
				width:26px;
				height:26px;
			}
		</style>
	</head>
	<body scroll='no' onload="" leftmargin='0' rightmargin='0' topmargin='0' bottommargin='0'>
		<noscript>
			Oh, common!!! You gotta be kidding!<br/>
			Turn on your javascript to enjoy theWebMind 2.0<br/><br/>
			If your browser does not support javascript, try one of these: Firefox, Chrome, Safari, Opera or even Internet Explorer 7+.
		</noscript>
		
	  <?php
		if(isset($info))
		{
			echo $info;
		}
	  ?>
		<table style='width: 100%; height: 100%;'>
			<tr>
				<td style='text-align: center;'>
					<center>
						<div class='login_background'>
							<div style='padding-left:70px;
										padding-top:70px;
										text-align: left;'>
								<span class='title'>
									TheWebMind 2.0
								</span><br>
								<div>
									Alpha Version - 2010
								</div><br><br>
							</div>
							<div id='contentHere'>
										<form onsubmit='return validate()'
											  name='sub'
											  action='login.php'
											  method='post'
											  target='hidden_frame'>
											<table>
												<tr>
													<td style='color:#fff;'>
														Login
													</td>
													<td>
														<input type='text'
															   name='login'
															   class='iptText'
															   id='name'
															   onKeyPress="checkCapsLock(event, this)">
													</td>
												</tr>
												<tr>
													<td style='color:#fff;'>
														Password
													</td>
													<td>
														<div id="capsLockMessage"
															 style='display:none;'>
															<b>Have you noticed your capsLock is on?</b><br/>
															<sup>
																<i>
																	Please, remember that your password is caseSensitive. In other words, an upperCase letter has a different meaning than a lower.
																</i>
															</sup>
														</div>
														<input  type='password'
																name='pass'
															    class='iptText'
																id='pwd'
																onfocus="this.select();"
																onKeyPress="checkCapsLock(event, this)">
													</td>
												</tr>
												<tr>
													<td colspan='2'
														style='text-align:center;'>
														<input type='checkbox'
															   id='rememberMe'>
														Remember me
													</td>
												</tr>
												<tr>
													<td colspan='2'>
														<center>
															<input type='image'
																   value='Go'
																   src='images/botao_go.png'>
														</center>
													</td>
												</tr>
											</table><br>
											<span id="mind_login_message">
												&nbsp;<br>
											</span>
										</form>
										<table class='extra-links'>
											<tr>
												<td>
													<a href='http://docs.thewebmind.org'
													   target='_quot'>
														<img src='http://www.thewebmind.org/img/ddl.png' title='Documentation' alt='Documentation' />
													</a>
												</td>
												<td>
													<a href='http://groups.google.com.br/group/thewebmind'
													   target='_quot'>
														<img src='http://www.thewebmind.org/img/group.png' title='Discussion Group' alt='Discussion Group' />
													</a>
												</td>
												<td>
													<a href='http://thewebmind.org/contribute'
													   target='_quot'>
														<img src='http://www.thewebmind.org/img/contribute.png' title='Contribute' alt='Contribute' />
													</a>
												</td>
												<td>
													<a href='http://code.google.com/p/webmind/'
													   target='_quot'>
														<img src='http://www.thewebmind.org/img/codes.png' title='Soure (at google code)' alt='Source (at google code)' />
													</a>
												</td>
											</tr>
										</table>
							</div>
							<div style='text-align:left;'>
								<div class='footer'>
									<table cellpadding='0'
										   cellspacing='0'>
										<tr>
											<td style='vertical-align:top;'>
												<a target='_quot'
													href='http://thewebmind.org'>
													<img src='images/logo_top.png'
														 align='left'
														 style='border:none;'/> 
												</a>
											</td>
											<td style='vertical-align:top;'>
												<!-- INICIO FORMULARIO BOTAO PAGSEGURO -->
													<form target="pagseguro" action="https://pagseguro.uol.com.br/security/webpagamentos/webdoacao.aspx" method="post"
														  style='margin: 0px;
																 padding: 0px;
																 padding-left: 3px;'>
														<input type="hidden" name="email_cobranca" value="felipenmoura@gmail.com">
														<input type="hidden" name="moeda" value="BRL">
														<input type="image" src="https://pagseguro.uol.com.br/Security/Imagens/FacaSuaDoacao.gif" name="submit" alt="Pague com PagSeguro - &eacute; r&aacute;pido, gr&aacute;tis e seguro!">
													</form>
												<!-- FINAL FORMULARIO BOTAO PAGSEGURO -->
											</td>
											<td>
												<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
													<input type="hidden" name="cmd" value="_s-xclick">
													<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHLwYJKoZIhvcNAQcEoIIHIDCCBxwCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYCmMH67MnWELW5rfIb0sOOQ2gBQ/xiIIbb4jm1HH3VwcpOV/QW2AwhwvoUcFaAyUeSPUXDqptGsDZRXe/5h0CNzt64RDaWVYBCBPuYwKyFagYqknbAqlTnty3ip2o9MxZz9+oVqsmg1aRPHl89qG5CIx+Ji9tuK54pS5qSVcgpnSDELMAkGBSsOAwIaBQAwgawGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIPs+nku+0p/6AgYipudO9XrkV4MuDuPaAIkXgF9AhBsvrj/ffH6rv0+oUbK+ovGDuYcKA5Ffjqadv4AwHeSFSX/XLS8cWCI1yn3hk/71feb8T31t06jGmM5KwzLt4WlMzaqQKQxfgadJSJ3ujhWXVchPUqo63H2bdb8FH2y67ARfZujWJhIKNeEXt2geaMqQJVyfHoIIDhzCCA4MwggLsoAMCAQICAQAwDQYJKoZIhvcNAQEFBQAwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMB4XDTA0MDIxMzEwMTMxNVoXDTM1MDIxMzEwMTMxNVowgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDBR07d/ETMS1ycjtkpkvjXZe9k+6CieLuLsPumsJ7QC1odNz3sJiCbs2wC0nLE0uLGaEtXynIgRqIddYCHx88pb5HTXv4SZeuv0Rqq4+axW9PLAAATU8w04qqjaSXgbGLP3NmohqM6bV9kZZwZLR/klDaQGo1u9uDb9lr4Yn+rBQIDAQABo4HuMIHrMB0GA1UdDgQWBBSWn3y7xm8XvVk/UtcKG+wQ1mSUazCBuwYDVR0jBIGzMIGwgBSWn3y7xm8XvVk/UtcKG+wQ1mSUa6GBlKSBkTCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb22CAQAwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOBgQCBXzpWmoBa5e9fo6ujionW1hUhPkOBakTr3YCDjbYfvJEiv/2P+IobhOGJr85+XHhN0v4gUkEDI8r2/rNk1m0GA8HKddvTjyGw/XqXa+LSTlDYkqI8OwR8GEYj4efEtcRpRYBxV8KxAW93YDWzFGvruKnnLbDAF6VR5w/cCMn5hzGCAZowggGWAgEBMIGUMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbQIBADAJBgUrDgMCGgUAoF0wGAYJKoZIhvcNAQkDMQsGCSqGSIb3DQEHATAcBgkqhkiG9w0BCQUxDxcNMDkwNjE0MTY0NzM2WjAjBgkqhkiG9w0BCQQxFgQUbiqwew5wkY6zPIo0t0ZGWt6+Lb4wDQYJKoZIhvcNAQEBBQAEgYCna84Xu/zeaPXlqw1ebDejrommfQB5+fgAnXGpy35P+fzqHvst0GTMxDqA3JHMm4KR54q1ZbZAEH76ljoN/8nYQL+xqksBlm16Kfi44Iq44Hunny9jkpnpXIw88CkR6YVAoPyef+c3ZyWoDXGA9JCCyHFhlq8i7gGBLIx0/FAT6Q==-----END PKCS7-----
													">
													<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
													<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
												</form>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</center>
				</td>
			</tr>
		</table>
		<iframe name="hidden_frame" style="display:none;"></iframe>
		<?php
			if(isset($info))
			{
				?>
					<script>
						var vSelfDestroyCounter= false;
						setTimeout(function(){
												vSelfDestroyCounter= setInterval(function(){
																		if(document.getElementById('selfDestroyCounter'))
																		{
																			if(document.getElementById('selfDestroyCounter').innerHTML == '0')
																			{
																				clearInterval(vSelfDestroyCounter);
																				$('#selfDestroyDiv').fadeOut('slow', function(){
																					document.getElementById('selfDestroyDiv').parentNode.removeChild(document.getElementById('selfDestroyDiv'));
																				});
																				return;
																			}
																			document.getElementById('selfDestroyCounter').innerHTML= document.getElementById('selfDestroyCounter').innerHTML-1;
																		}
																	  }, 1000);
											 }, 2000);
					</script>
				<?php
			}
		?>
	</body>
</html>
