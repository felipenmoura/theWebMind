<?php
	include('../../../config/mind.php');
	include('../../../'.$_MIND['framework']);
	include('../../../'.$_MIND['header']);
	
	if($_POST)
	{
		while(strpos($_POST['PluginMaker_name'], '..')>-1)
			$_POST['PluginMaker_name']= str_replace('..', '.', $_POST['PluginMaker_name']);
		$_POST['PluginMaker_name']= $_MIND['fw']->fixName($_POST['PluginMaker_name']);
		if(trim(str_replace('.', '', $_POST['PluginMaker_name'])) == '')
		{
			?>
			<script>
				alert('Your plugin must have a valid name');
			</script>
			<?php
			exit;
		}
		$dir= '../../'.$_POST['PluginMaker_name'];
		if(!file_exists($dir))
			if(!@mkdir($dir))
			{
				echo "Permission denied! I need to be able to create files inside &lt;MindDirectory>/restrict/plugins<br/>";
				exit;
			}
		if(!file_exists($dir.'/data'))
			mkdir($dir.'/data');
		$conf= $_MIND['fw']->mkXML($dir.'/conf.xml');
		$info= $_MIND['fw']->mkXML($dir.'/info.xml');
		
		# configuring the conf file
		$conf->addChild('version');
		$conf->version['value']= $_POST['PluginMaker_version'];
		$conf->addChild('extraconffile');
		$conf->extraconffile['value']= 'conf_file.php';
		$conf->addChild('openas');
		$conf->openas['value']= $_POST['PluginMaker_opanAs'];
		$conf->addChild('runat');
		$conf->runat['value']= $_POST['PluginMaker_runAt'];
		$conf->addChild('useicon');
		move_uploaded_file($_FILES["PluginMaker_icon"]["tmp_name"], $dir.'/data/'.$_FILES['PluginMaker_icon']['name']);
		
		if(isset($_FILES['PluginMaker_icon'])
			&&
			($_FILES["PluginMaker_icon"]["type"] == "image/jpeg")
			||
			($_FILES["PluginMaker_icon"]["type"] == "image/png")
			||
			($_FILES["PluginMaker_icon"]["type"] == "image/gif")
			)
			$conf->useicon['value']= $_FILES['PluginMaker_icon']['name'];
		else
			$conf->useicon['value']= 'false';
		
		$conf->addChild('dependsonproject');
		$conf->dependsonproject['value']= (isset($_POST['PluginMaker_dependdOnProject']) && $_POST['PluginMaker_dependdOnProject'] == 'yes')? 'true': 'false';
		
		$_MIND['fw']->saveXML($conf, $dir.'/conf.xml');
		
		# configuring the info file
		$info->addChild('name');
		$info->name['value']= $_POST['PluginMaker_name'];
		$info->addChild('date');
		$info->date['value']= date('m/Y');
		$info->addChild('link');
		$info->link['value']= $_POST['PluginMaker_link'];
		$info->addChild('description');
		$info->description['value']= $_POST['PluginMaker_description'];
		$info->addChild('authors');
		$info->authors->addChild('author');
		$info->authors->author['value']= 'TheWebMind PluginMaker';
		$info->authors->author['email']= 'felipe@thewebmind.org';
		for($i=0, $j=sizeof($_POST['PluginMaker_authorName']); $i<$j; $i++)
		{
			if(trim($_POST['PluginMaker_authorName'][$i]) != '' && trim($_POST['PluginMaker_authorEmail'][$i]) != '')
			{
				$info->authors->addChild('author');
				$info->authors->author[$i+1]['value']= $_POST['PluginMaker_authorName'][$i];
				$info->authors->author[$i+1]['email']= $_POST['PluginMaker_authorEmail'][$i];
			}
		}
		
		$_MIND['fw']->saveXML($info, $dir.'/info.xml');
		
		fopen($dir.'/data/face.php', 'w+');
		fopen($dir.'/data/conf_file.php', 'w+');
		$js= fopen($dir.'/data/'.$_POST['PluginMaker_name'].'.js', 'w+');
		$jsContent= <<<jsContent
Mind.Plugins.PLUGINNAMEHERE= {
	/* required methods */
	Run: function(){
		//this.Save('', '');
	},
	Load: function(){
		
	},
	/***************************/
	/*
		here, you may add any other methods
	*/
	Init: function(){
		
	}
}
/*
	function Save(file_pach, content [, flag]):boolean;
	Return: true if successed, false if any error happens
	Parameters:
		file_path: the address. If the directory of dile doesn't exist, it will be created
		content: the content to be saved into the file
		flag: use true to concatenate its content, false to overwrite the file if it exists
	
	function Unlink(file_pach):boolean;
	Return: true in case of success, false otherwise
	Parameters:
		file_path: the address, including the directory of the file, will be deleted
		
	function MkDir(dir_pach):boolean;
	Return: true in case of success, false otherwise
	Parameters:
		dir_pach: the address, including the parent directory, where to create the new directory
		
	function List(dir_pach):ObjectCollection;
	Return: ObjectCollection[
				    Object[
						name:the name of file or directory,
						type:directory or file,
						address:the absolute address of the file
					  ]
				  ]
	Parameters:
		dir_pach: address of the parent directory to list the files
 	
	function LoadFile(file_pach):Object;
	Return: Object[
				name:the name of the file,
				address:full path of the file,
				size:files's size,
				content:the file content,
				lastChange:time of the last change
 		       ]
	Parameters:
		file_path: the address of the file to be loaded
 	
	function Post(file_pach, post_data):String;
	Return: the content loaded from the posted page
	Parameters:
		file_path: the address of the file to be posted
               post_data: Object with name and value of each data to be posted,
       Example:
               yourPluginName.Post(
                                      'user.php',
		                       		  {name:'Felipe', age:24}
             		           );
*/
jsContent;
		$jsContent= str_replace('PLUGINNAMEHERE', $_POST['PluginMaker_name'], $jsContent);
		fwrite($js, $jsContent);
		
		chmod($dir, 0777);
		?>
		<script>
			alert('Your addon has been created.\nYou can see its files accessing <theWebMind>/restrict/plugins/<?php echo $_POST['PluginMaker_name']; ?>\nAs soon you restart theWebMind, you will see it already working');
		</script>
		<?php
		exit;
	}
?>
<div style='padding:0px;margin:0px;'
	 id='PluginMaker_form'>
	 <ul>
		<li><a href="#PluginMaker-info">Info</a></li>
		<li><a href="#PluginMaker-conf">Conf</a></li>
	</ul>
	<form method='POST'
		  enctype="multipart/form-data"
		  action='<?php echo $_SERVER['PHP_SELF']; ?>'
		  target='plgMkerIFrame'
		  id='PluginMakerForm'>
		<table id='PluginMaker-info'>
			<tr>
				<td>
					Pulgin Name
				</td>
				<td>
					<input type='text' name='PluginMaker_name'/>
				</td>
			</tr>
			<tr>
				<td style='vertical-align:top;'>
					Authors
				</td>
				<td>
					<input type='text' name='PluginMaker_authorName[]' value='Name'/>
					<input type='text' name='PluginMaker_authorEmail[]' value='E-mail'/>
					<input  type='button'
							value='+'
							onclick="Mind.Plugins.PluginMaker.AddAuthorRow()"/>
					<div id='PluginMaker_authorsList'>
						
					</div>
				</td>
			</tr>
			<tr>
				<td>
					Link
				</td>
				<td>
					<input type='text' name='PluginMaker_link'/>
				</td>
			</tr>
			<tr>
				<td>
					Description
				</td>
				<td>
					<textarea type='text' name='PluginMaker_description'></textarea>
				</td>
			</tr>
		</table>
		<table id='PluginMaker-conf'>
			<tr>
				<td>
					Version
				</td>
				<td>
					<input type='text' name='PluginMaker_version' style='width:70px;'/>
				</td>
			</tr>
			<tr>
				<td>
					Open as
				</td>
				<td>
					<select name='PluginMaker_opanAs'>
						<option value='none'>
							none
						</option>
						<option value='modal'>
							modal 
						</option>
						<option value='tab'>
							tab
						</option>
						<option value='window'>
							window
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					Run at
				</td>
				<td>
					<select name='PluginMaker_runAt'>
						<option value='none'>
							none
						</option>
						<option value='load'>
							 load
						</option>
						<option value='run'>
							run
						</option>
						<option value='open'>
							open
						</option>
						<option value='report'>
							report
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					Icon (jpg, png or gif)
				</td>
				<td>
					<input type='file' name='PluginMaker_icon' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					Does your Plugin need a project openend to work? 
					<input type='radio' name='PluginMaker_dependsOnProject' value='yes' /> Yes
					<input type='radio' name='PluginMaker_dependsOnProject' value='no' checked='checked' /> No
				</td>
			</tr>
		</table>
		<center>
			<input type='submit'
				   class='ui-state-default ui-corner-all'
				   value='Create'>
		</center>
	</form>
	<iframe name='plgMkerIFrame' style='display:none;'></iframe>
</div>
<script>
	$(document).ready(function(){
		$('#PluginMaker_form').tabs();
	});
	
</script>
