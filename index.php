<?php
	include('restrict/config/mind.php');
	include('restrict/'.$_MIND['framework']);
	
	$_MIND['rootDir']= 'restrict/'.$_MIND['rootDir'];
	
	if(sizeof(($users= @$_MIND['fw']->getUsersList())) == 0)
	{
		$u= new User();
		$u->name('Administrator');
		$u->login('admin');
		$u->pwd('theWebMind.org');
		$u->position('admin');
		$u->status(1);
		$u->email('contato@thewebmind.org');
		
		if($u->save())
		{
			$info= "<div style='width:250px;
								color:#444;
								background-color:#eec;
								border:solid 2px #669;
								padding: 7px;
								text-align:center;'
						 id='selfDestroyDiv'>
						Oh! I see this is your first time around here.<br/>To you to start working with the system, the user <b><i>admin</i></b> has been created, with the password <b><i>theWebMind.org</i></b><br/>
						I advise you to change this password as soon as possible. This message will self destruct in <br><b><span id='selfDestroyCounter'>60</span></b>
					</div><br/>";
		}else
			{
				echo "Error: Please, set permissions to your PHP server, to change files in:
					<ul>
						<li>
							restrict/".$_MIND['userDir']."
						</li>
						<li>
							restrict/".$_MIND['publishDir']."
						</li>
					</ul>";
				exit;
			}
		
		/*exit;
		
		$usersFile= 'restrict/config/usr.xml';
		$xml = simplexml_load_file($usersFile);
		$flag = 0;
		
		for($i=0;$i<sizeof($xml->user);$i++)
		{
			if($_POST['login'] == $xml->user[$i]['login'])
			{
				?>
					<script>
						alert('Error: This user already exists!');
					</script>
				<?php
				exit;
			}
		}
		$child= $xml->addChild('user');
		$child['code']	= $i;
		$child['name']	= $_POST['name'];
		$child['email']	= $_POST['login'];
		$child['login']	= $_POST['login'];
		$child['pwd']	= md5($_POST['password']);
		$child['type']	= 'normal';
		$child['acceptEmails']= $_POST['emailMessages'];
		$child['status']= '1';
		if(@file_put_contents($usersFile, $xml->asXML()))
		{	
			?>
				<script>
					alert("Message: Ok, you've just been subscribed on TheWebMiNd.");
					parent.document.getElementById('contentHere').innerHTML= parent.c;
				</script>
			<?php
		}else{
				?>
					<script>
						alert("Error: A problem occured when creating the profile. Very your permissions!");
					</script>
				<?php
			 }
		exit;
		*/
	}else
		$info= false;
?>
<html>
	<head>
		<title>
			theWebMind
		</title>
		<script src="restrict/framework/scripts/jquery.js"></script>
		<script src="restrict/framework/scripts/coockies.js"></script>
		<script>
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
				width:780px;
				height:598px;
				border:solid 1px #236f77;
			}
			#contentHere
			{
				padding-top:120px;
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
				margin-top:140px;
				*margin-top:110px; /* IE Sucks*/
				margin-left:7px;
				padding-top:7px;
				padding-left:12px;
				width: 510px;
				border-top: solid 2px #999;
			}
		</style>
	</head>
	<body scroll='no' onload="">
		<table style='width: 100%; height: 100%;'>
			<tr>
				<td style='text-align: center;'>
					<center>
						<div class='login_background'>
							<div style='padding-left:70px;
										padding-top:70px;
										text-align: left;'>
								<span class='title'>
									theWebMind(s) 2.0
								</span><br>
								<div style='border-bottom:solid 2px #999;
											 width:140px;'>
									Beta Version - 2009
								</div><br><br>
							</div>
							<div id='contentHere'>
										<form onsubmit='return validate()'
											  name='sub'
											  action='login.php'
											  method='post'
											  target='hidden_frame'>
											  <?php
												if(isset($info))
												{
													echo $info;
												}
											  ?>
											<table>
												<tr>
													<td>
														Login
													</td>
													<td>
														<input type='text'
															   name='login'
															   class='iptText'
															   id='name'>
													</td>
												</tr>
												<tr>
													<td>
														Password
													</td>
													<td>
														<input  type='password'
																name='pass'
															    class='iptText'
																id='pwd'
																onfocus="this.select();">
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
								<span onclick="this.parentNode.innerHTML= c" style='cursor: default;'>
								</span>
							</div>
							<div style='text-align:left;'>
								<div class='footer'>
									<table cellpadding='0'
										   cellspacing='0'>
										<tr>
											<td>
												<a target='_quot'
													href='http://thewebmind.org'>
													<img src='images/mini_logo.jpg'
														 align='left'
														 style='border:none;'/> 
												</a>
											</td>											
											<td>
												<!-- INICIO FORMULARIO BOTAO PAGSEGURO -->
													<form target="pagseguro" action="https://pagseguro.uol.com.br/security/webpagamentos/webdoacao.aspx" method="post"
														  style='margin: 0px;
																 padding: 0px;
																 padding-left: 3px;'>
														<input type="hidden" name="email_cobranca" value="felipenmoura@gmail.com">
														<input type="hidden" name="moeda" value="BRL">
														<input type="image" src="https://pagseguro.uol.com.br/Security/Imagens/FacaSuaDoacao.gif" name="submit" alt="Pague com PagSeguro - é rápido, grátis e seguro!">
													</form>
												<!-- FINAL FORMULARIO BOTAO PAGSEGURO -->
											</td>
											<td>
												<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
													<input type="hidden" name="cmd" value="_s-xclick">
													<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
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
		<!--<iframe src="http://settings.messenger.live.com/Conversation/IMMe.aspx?invitee=37151c9c105cff94@apps.messenger.live.com&mkt=en-US&useTheme=true&themeName=gray&foreColor=676769&backColor=DBDBDB&linkColor=444444&borderColor=8D8D8D&buttonForeColor=99CC33&buttonBackColor=676769&buttonBorderColor=99CC33&buttonDisabledColor=F1F1F1&headerForeColor=729527&headerBackColor=B2B2B2&menuForeColor=676769&menuBackColor=BBBBBB&chatForeColor=99CC33&chatBackColor=EAEAEA&chatDisabledColor=B2B2B2&chatErrorColor=760502&chatLabelColor=6E6C6C" width="300" height="300" style="border: solid 1px black; width: 300px; height: 300px;" frameborder="0"></iframe>-->
		<iframe name="hidden_frame" style="width: 100%; height: 500px; display:none;"></iframe>
		<?php
			if(isset($info))
			{
				?>
					<script>
						setTimeout(function(){
												var vSelfDestroyCounter= setInterval(function(){
																		if(document.getElementById('selfDestroyCounter').innerHTML == '0')
																		{
																			clearInterval(vSelfDestroyCounter);
																			document.getElementById('selfDestroyDiv').parentNode.removeChild(document.getElementById('selfDestroyDiv'));
																			return;
																		}
																		document.getElementById('selfDestroyCounter').innerHTML= document.getElementById('selfDestroyCounter').innerHTML-1;
																	  }, 1000);
											 }, 2000);
					</script>
				<?php
			}
		?>
	</body>
</html>
