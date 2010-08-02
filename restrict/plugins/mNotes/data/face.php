<div style='padding:0px;margin:0px;'
		autoresize='true'>
	<div style='width:90%;margin-left:5%;'>
		<b>Add a Note</b><br/>
		<center>
			<textarea id='noteToSave'
							 style='width:100%;'></textarea>
		</center>
		<div style='text-align:right;'>
			<input type='button'
					   value="Save Note"
					   id='notesButtonSave'/>
		</div>
		<div id='savedNotes'>
			<br/>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#notesButtonSave').bind('click', function(){
			var note= document.getElementById('noteToSave').value;
			var dir= Mind.Project.attributes.name;
			var dt= new Date();
			note= "<br/>--- "+ dt.toGMTString()+' ----------------------------------<br/>'+note+'<br/>';
			
			Mind.Plugins.mNotes.Save(dir+'/notes.txt', note, true); // saves the content of the new note, appending it to the file notes.txt
			Mind.Plugins.mNotes.Init();
			document.getElementById('noteToSave').value= '';
		});
		
		Mind.Plugins.mNotes.Init();
	});
</script>