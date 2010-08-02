<?php
	if($_POST)
	{
		session_start();
		require('adodb5/adodb.inc.php');
		$dbdriver= $_POST['dataBase'];
		$db= ADONewConnection($dbdriver);
		$db->debug = false;
		$server= $_POST['dbAddress1'];
		$user= $_POST['userRoot1'];
		$password= $_POST['userRootPwd1'];
		$database= $_POST['dbName1'];
		
		if($dbdriver == 'mssql')
		{
			$db = &ADONewConnection("ado_mssql");
			$myDSN="PROVIDER=MSDASQL;DRIVER={SQL Server};". "SERVER=$server;DATABASE=$database;UID=$user;PWD=$password;";
				if($db->Connect($myDSN)){
					$f= true;
				}
		}else{
				if(@$db->Connect($server, $user, $password, $database))
				{
					$f= true;
				}
			 }
		
		echo "<body><div id='content'>";
         if(!isset($f))
		 {
			$ok= false;
			echo "Problem when trying to connect to the data base!<br/>Please, verify if the given information matches.";
		 }else{
				 $ok= true;
				 $verb= $_SESSION['current']['defaultVerb'];
				 $notNull= $_SESSION['current']['defaultNotNull'];
				 $relations= "	/* RELATIONS */\n";
				 
				// "Thansks MS"
				if($dbdriver == 'mssql')
				{
					$tables = Array();
					$db->SetFetchMode(ADODB_FETCH_NUM);
					$tables = &$db->Execute("SELECT *  FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE <> 'SYSTEM TABLE'");
				}else{
						$tables= $db->MetaTables();
					 }
				 foreach($tables as $tb)
				 {
					$avoid= Array();
					$fields= $db->MetaColumns($tb);
					
					$fks= $db->MetaForeignKeys($tb);
					if(is_array($fks))
					{
						reset($fks);
						while($fk= current($fks))
						{
							$relations.= key($fks).' '.$verb.' n '.$tb.".
";
							$fk= explode('=', $fk[0]);
							$avoid[$fk[0]]= true;
							next($fks);
						}
					}
					foreach($fields as $f)
					{
						if($f->primary_key == 1 || isset($avoid[$f->name])) // will NOT add either primary keys or foreign keys
							continue;
						echo $tb." ".$verb." ";
						$f->type= preg_replace('/[0-9]/', '', $f->type);
						$f->type= preg_replace('/bpchar/', 'char', $f->type);
						
						$echo=  $f->name.':'.$f->type.'('.$f->max_length;
						
						if($f->not_null == 1)
							$echo.= ', '.$notNull;
						if($f->has_default == 1)
						{
							if (substr($f->default_value, 0, 1) == "'")
							{
								$f->default_value= preg_replace('/::[a-z0-9 -_]+/i', '', $f->default_value);
								$f->default_value= preg_replace('/(^\')|(\'$)/', '', $f->default_value);
							}else{
										$f->default_value= 'Exec:'.preg_replace('/::[a-z0-9 -_]+/i', '', $f->default_value);
								 }
							$echo.= ', "'.preg_replace('/"/', '\"', $f->default_value).'"';
						}
							
						$echo.= ').';
						echo $echo;
						echo '
';
					}
				 }
				 echo $relations;
			}
		 ?>
				</div>
			</body>
			<script>
				top.document.getElementById('DBMapping_result').innerHTML= "<pre>"+document.getElementById('content').innerHTML+"</pre>";
				<?php
					if($ok)
					{
						echo "top.document.getElementById('DBMapping_buttonAccept').disabled= false";
					}else
						echo "top.document.getElementById('DBMapping_buttonAccept').disabled= true";
				?>
			</script>
		 <?php
		exit;
	}
?><div style='padding:0px;margin:0px;'
		autoresize='true'>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>"
		  method='post'
		  target='hFrame'
		  id="DBMappingForm">
		<fieldset>
			<legend>
				<b>
					Please, provide the database data
				</b>
			</legend>
			<table>
				<tr>
					<td>
						DGBMS
					</td>
					<td colspan='3'>
						<select name='dataBase' onchange=''>
							<option value='postgres'>
								PostgreSQL
							</option>
							<option value='mssql'>
								MSSQL
							</option>
							<option value='oracle'>
								Oracle
							</option>
							<option value='mysql'>
								MySQL
							</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Address
					</td>
					<td>
						<input type='text'
							   name="dbAddress1"
							   class='iptText'
							   label="Data Base's Name"
							   value='localhost'>
					</td>
					<td>
						<!--Port-->
					</td>
					<td>
						<!--
						<input type='text'
							   name="port1"
							   class='iptText'
							   maxlength='4'
							   style='width:50px;'
							   label="Data Base's Port">
						-->
					</td>
				</tr>
				<tr>
					<td>
						DB Name
					</td>
					<td>
						<input type='text'
							   name="dbName1"
							   class='iptText'
							   label='Data Base Name'>
					</td>
					<td>
						
					</td>
					<td><br/>
					</td>
				</tr>
				<tr>
					<td>
						Root User
					</td>
					<td>
						<input type='text'
							   name="userRoot1"
							   class='iptText'
							   value='root'>
					</td>
					<td>
						Password
					</td>
					<td>
						<input type='password'
							   name="userRootPwd1"
							   class='iptPwd'>
					</td>
				</tr>
			</table>
			<center>
				<div id='DBMapping_warning'
					   style='background-color:#ffc;
								border:solid 1px #f99;
								color: #f00;
								padding:3px;
								text-align:left;
								display:block;
								width:700px;
								float:left;
								padding-left:0px;'>
					<ul type='disc' style='margin:0px;'><li>This plugin is under construction. Use it by your own responsability. Please report bugs or any suggestion to TheWebMind Community.</li></ul>
				</div><br/>
			</center>
		</fieldset>
		<center>
			<br/>
			<input type='submit'
				   class='ui-state-default ui-corner-all'
				   value='Map it'
				   id='DBMapping_MapIt'/>
			<input type='button'
				   class='ui-state-default ui-corner-all'
				   value='Ping Server'
				   id='DBMapping_Ping'/>
		</center>
	</form>
	<center>
		<div id='DBMapping_result'
				style='border:solid 1px #99c;
						 width:700px;
						 margin-top:10px;
						 height:220px;
						 padding:4px;
						 white-space:no-wrap;
						 overflow:auto;
						 text-align:left;'>
			<br/>
		</div>
		<input type='button'
				 value='Accept and use it'
				 disabled
				 style='margin-top:10px;'
				 id='DBMapping_buttonAccept' />
	</center>
	<iframe name='hFrame' style='display:none;'></iframe>
</div>
<script>
	$(document).ready(function(){
		$('#DBMapping_Ping').bind('click', function(){
			var x= Mind.Plugins.DBMapping.Post('ping.php',
															$('#DBMappingForm').serialize()
															);
			alert(x);
		});
		$('#DBMapping_buttonAccept').bind('click', function(){
			Mind.MindEditor.Code(document.getElementById('DBMapping_result').innerHTML.replace(/^<pre>/, '').replace(/<\/pre>$/, ''));
			Mind.Dialog.CloseModal();
		});
		Mind.Plugins.DBMapping.Init();
	});
	
	function DBMapping_showWarning(v)
	{
		if(v=='mysql')
			$('#DBMapping_warning').fadeIn('slow');
		else
			$('#DBMapping_warning').fadeOut('slow');
	}
</script>
