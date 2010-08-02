<?php
	include('../../config/mind.php');
	include('../../'.$_MIND['framework']);
	include('../../'.$_MIND['header']);
?><?php
	if($_POST)
	{
		$p= new Plugin();
		$p->populate($_POST);
		$p->save();
		$ret= $_MIND['fw']->output();
		if(trim($ret)!='')
		{
			echo "Mind.Dialog.ShowModalMessage('".$ret."', 'error')";
		}else{
				echo "parent.Mind.Dialog.CloseModal();";
			 }
		//print_r($_POST);
		exit;
	}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>"
	  style='padding:0px;
			 margin:0px;'
			 target='_quot'
		method='POST'>
	<div class='pluginAddd'>
		<div id="tabs"
			 style='height: 98%;'>
			<ul>
				<li><a href="#tabs-1">Info</a></li>
				<li><a href="#tabs-2">Conf</a></li>
			</ul>
			<div id="tabs-1">
				<table>
					<tr>
						<td>
							Name
						</td>
						<td>
							<input type='text'
								   name='name'
								   class='iptText'
								   required='true'
								   label='Name'>
						</td>
					</tr>
					<tr>
						<td>
							Date
						</td>
						<td>
							<input type='text'
								   name='date'
								   class='iptText'>
						</td>
					</tr>
					<tr>
						<td>
							Author
						</td>
						<td>
							<input type='text'
								   name='author'
								   class='iptText'
								   required='true'
								   label='Author'>
						</td>
					</tr>
					<tr>
						<td>
							Link
						</td>
						<td>
							<input type='text'
								   name='link'
								   class='iptText'
								   required='true'
								   label='Link'>
						</td>
					</tr>
					<tr>
						<td>
							Detail
						</td>
						<td>
							<textarea name='detail'
									  class='textArea'></textarea>
						</td>
					</tr>
				</table>
			</div>
			<div id="tabs-2">
				<table>
					<tr>
						<td colspan='2'>
							Open
						</td>
					</tr>
					<tr>
						<td style='padding-left: 25px;'>
							As
						</td>
						<td>
							<select name='openAs'>
								<option value='modal'>
									Modal
								</option>
								<option value='popup'>
									Popup
								</option>
								<option value='leftPanel'>
									Left Panel
								</option>
								<option value='rightPanel'>
									Right Panel
								</option>
								<option value='bottomPanel'>
									Bottom Panel
								</option>
								<option value='outputTab'>
									Output tab
								</option>
								<option value='bodyPanel'>
									Body Panel
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<td style='padding-left: 25px;'>
							When:
						</td>
						<td>
							<select name='openEvent'>
								<option value='called'>
									called
								</option>
								<option value='onload'>
									theWebMind is loaded
								</option>
								<option value='openingAProject'>
									any project is opened
								</option>
								<option value='output'>
									an output is called
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<td style='padding-left: 25px;'>
							Status:
						</td>
						<td>
							<select name='status'>
								<option value='1'>
									Active
								</option>
								<option value='0'>
									Inactive
								</option>
							</select>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<input type='submit'>
</form>
<script>
	$(function() {
		$("#tabs").tabs();
	});
</script>