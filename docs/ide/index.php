<script src='scripts/jquery.js'></script>
<body>
	<input type='button' value='autenticate' onclick="autenticate()"/>
	<input type='button' value='run test' onclick="runTest()"/>
	<input type='button' value='run info' onclick="runInfo()"/>
	<input type='button' value='show projects' onclick="showProjects()"/>
	<input type='button' value='show users' onclick="showUsers()"/>
	<input type='button' value='analyze project x' onclick="analyzeX()"/>
	<input type='button' value='analyze project y' onclick="analyzeY()"/>
	<input type='button' value='logoff' onclick="logoff()"/>
	<pre><div id='result' style='white-space:pre;'></div></pre>
</body>
<script>
	function setLoading()
	{
		document.getElementById('result').innerHTML= "<img src='gif-loading.gif' />";
	}
	
	function autenticate(){
		setLoading();
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
		setLoading();
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program:'test',
				unit: true
			},
			success: function(ret){
				document.getElementById('result').innerHTML= ret
			}
		});
	}
	function runInfo()
	{
		setLoading();
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
		setLoading();
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
		setLoading();
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
	
	function analyzeX()
	{
		setLoading();
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program:'use',
				what:'project',
				name:'x'
			},
			success: function(ret){
				$.ajax({
							type:'POST',
							url:'http://localhost/mind/',
							data:{
								program:'analyze'
							},
							success: function(ret){
								document.getElementById('result').innerHTML= ret;
							}
						});
			}
		});
	}

	function analyzeY()
	{
		setLoading();	
		$.ajax({
			type:'POST',
			url:'http://localhost/mind/',
			data:{
				program:'use',
				what:'project',
				name:'y'
			},
			success: function(ret){
				$.ajax({
							type:'POST',
							url:'http://localhost/mind/',
							data:{
								program:'analyze'
							},
							success: function(retY){
								document.getElementById('result').innerHTML= retY;
							}
						});
			}
		});
	}

	function logoff()
	{
		setLoading();
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
