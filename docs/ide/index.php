<script src='scripts/jquery.js'></script>
<body>
	<div id='result' style='white-space:pre;'></div>
	<br/>
	<input type='button' value='autenticate' onclick="autenticate()"/>
	<input type='button' value='run test' onclick="runTest()"/>
	<input type='button' value='run info' onclick="runInfo()"/>
	<input type='button' value='show projects' onclick="showProjects()"/>
	<input type='button' value='show users' onclick="showUsers()"/>
	<input type='button' value='analyze project x' onclick="analyze()"/>
	<input type='button' value='analyze project y' onclick="analyzeY()"/>
	<input type='button' value='logoff' onclick="logoff()"/>
</body>
<script>
	function autenticate(){
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program:'auth',
				login:"admin",
				pwd:'admin'
			},
			success: function(ret){
				document.getElementById('result').innerHTML= ret
			}
		});
	}
	function runTest()
	{
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program:'test'
			},
			success: function(ret){
				document.getElementById('result').innerHTML= ret
			}
		});
	}
	function runInfo()
	{
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program: 'info'
			},
			success: function(ret){
				document.getElementById('result').innerHTML= ret
			}
		});
	}
	
	function showProjects()
	{
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program:'show',
				what:'projects',
				detailed:'1'
			},
			success: function(ret){
				document.getElementById('result').innerHTML= ret
			}
		});
	}

	function showUsers()
	{
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program:'show',
				what:'users',
				detailed:'1'
			},
			success: function(ret){
				document.getElementById('result').innerHTML= ret
			}
		});
	}
	
	function analyze()
	{
	
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program:'use',
				what:'project',
				name:'x'
			},
			success: function(ret){
				document.getElementById('result').innerHTML= "<br/>"+ret
				$.ajax({
							type:'POST',
							url:'http://localhost/mind/',
							data:{
								program:'analyze'
							},
							success: function(ret){
								document.getElementById('result').innerHTML+= "<br/>"+ret
							}
						});
			}
		});
	}

	function analyzeY()
	{
	
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program:'use',
				what:'project',
				name:'y'
			},
			success: function(ret){
				document.getElementById('result').innerHTML= "<br/>"+ret
				$.ajax({
							type:'POST',
							url:'http://localhost/mind/',
							data:{
								program:'analyze'
							},
							success: function(ret){
								document.getElementById('result').innerHTML+= "<br/>"+ret
							}
						});
			}
		});
	}

	function logoff()
	{
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program:'exit'
			},
			success: function(ret){
				document.getElementById('result').innerHTML= ret
			}
		});
	}
</script>
