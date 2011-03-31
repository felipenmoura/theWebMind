<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<script src='scripts/jquery.js'></script>
		<style tye='text/css'>
			property
			{
				font-weight:bold;
			}
			keyword
			{
				font-weight:bold;
				color: blue;
			}
			object
			{
				color: blue;
			}
			comment
			{
				font-style:italic;
				color: green;
			}
		</style>
	</head>
	<body>
		<input type='button' value='autenticate' onclick="autenticate()"/>
		<input type='button' value='run test' onclick="runTest()"/>
		<input type='button' value='run info' onclick="runInfo()"/>
		<input type='button' value='show projects' onclick="showProjects()"/>
		<input type='button' value='show users' onclick="showUsers()"/>
		<input type='button' value='analyze project demo_en' onclick="analyzeX()"/>
		<input type='button' value='commit project demo_en' onclick="commit()"/>
		<input type='button' value='show queries' onclick="showQueries()"/>
		<input type='button' value='logoff' onclick="logoff()"/>
		<pre><div id='result' style='border:dashed 1px #777;'></div></pre>
	</body>
	<script>
		function setLoading()
		{
			document.getElementById('result').innerHTML= "&nbsp;&nbsp;<img src='loading_animation.gif' /><br/>Loading...";
		}
	
		function autenticate(){
			setLoading();
			$.ajax({
				type:'POST',
				url:'../../',
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
				url:'../../',
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
				url:'../../',
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
				url:'../../',
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
				url:'../../',
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
				url:'../../',
				data:{
					program:'use',
					what:'project',
					name:'demo_en'
				},
				success: function(ret){
					$.ajax({
								type:'POST',
								url:'../../',
								data:{
									program:'analyze'//,
									//commit:true
								},
								success: function(ret){
									document.getElementById('result').innerHTML= ret;
								}
							});
				}
			});
		}
		function commit()
		{
			setLoading();
			$.ajax({
				type:'POST',
				url:'../../',
				data:{
					program:'use',
					what:'project',
					name:'demo_en'
				},
				success: function(ret){
					$.ajax({
								type:'POST',
								url:'../../',
								data:{
									program:'commit'
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
				url:'../../',
				data:{
					program:'use',
					what:'project',
					name:'y'
				},
				success: function(ret){
					$.ajax({
								type:'POST',
								url:'../../',
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

		function showQueries()
		{
			setLoading();
			$.ajax({
						type:'POST',
						url:'../../',
						data:{
							program:'dqb',
							query: 'create',
							table: '*'
						},
						success: function(retQ){
							document.getElementById('result').innerHTML= retQ;
						}
					});
		}

		function logoff()
		{
			setLoading();
			$.ajax({
				type:'POST',
				url:'../../',
				data:{
					program:'exit'
				},
				success: function(ret){
					document.getElementById('result').innerHTML= ret
				}
			});
		}
	</script>
</html>
