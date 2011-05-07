<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
        <link rel="shortcut icon" href="images/logo.png" />
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
			value
			{
				color: #c00;
				font-style:italic;
			}
			element
			{
				font-style:italic;
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
			.mindTableName
			{
				text-decoration: underline;
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
		<input type='button' value='generate db' onclick="genDB()"/>
		<input type='button' value='generate docs' onclick="genDocs()"/>
		<input type='button' value='logoff' onclick="logoff()"/>
        <div style='border:solid 1px #777;
                    overflow:auto;
                    color:white;
                    background-color: black;'
             id="scrollingDiv">
            <div  id='result'
                  style='border:none;
                         width:100%;
                         font-family: Courier New;
                         white-space:pre;'></div>
            <textarea style='width:100%;
                             margin: 0px;
                             border: none;
                             height:120px;
                             color:white;
                             font-family: Courier New;
                             background-color: black;'
                      id='consoleCommand'
                      value=""></textarea>
        </div>
		<input type='button' value='create demo_en project' onclick="createDemo_en()"/>
		<input type='button' value='run example' onclick="exampleModel()"/>
		<input type='button' value='API Facade tests' onclick="APITest()"/>
	</body>
	<script>
		function setLoading()
		{
			document.getElementById('result').innerHTML= "&nbsp;&nbsp;<img src='images/loading_animation.gif' /><br/>Loading...";
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
					program:'test'
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
					projectName:'demo_en'
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
		
		function genDB()
		{
			setLoading();
			$.ajax({
						type:'POST',
						url:'../../',
						data:{
							program:'generate',
							lobe: 'db'
						},
						success: function(retQ){
							document.getElementById('result').innerHTML= retQ;
						}
					});
		}
		function genDocs()
		{
			setLoading();
			$.ajax({
						type:'POST',
						url:'../../',
						data:{
							program:'generate',
							what: 'sql'
						},
						success: function(retQ){
							document.getElementById('result').innerHTML= retQ;
						}
					});
		}
        function createDemo_en()
		{
			setLoading();
			$.ajax({
				type:'POST',
				url:'../../',
				data:{
                        program:'create',
                        what: 'project',
                        argName:'demo_en'
				},
				success: function(ret){
					document.getElementById('result').innerHTML= ret
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
        
		function exampleModel()
		{
			setLoading();
			$.ajax({
				type:'POST',
				url:'../../',
				data:{
					program:'modeloteste',
                    firstArgument: 'Y'
				},
				success: function(ret){
					document.getElementById('result').innerHTML= ret
				}
			});
		}
        
		function APITest()
		{
			setLoading();
			$.ajax({
						type:'POST',
						url:'../../',
						data:{
							program:'generate',
							lobe: 'testfacade'
						},
						success: function(retQ){
							document.getElementById('result').innerHTML= retQ;
						}
					});
		}
        
        var MindConsole= {
            history: [],
            current: 0,
            add: function(str)
            {
                if(str=='')
                    return;
                
                MindConsole.current= MindConsole.history.length;
                
                if(MindConsole.current && MindConsole.history[MindConsole.current-1] == str)
                {
                    return;
                }
                MindConsole.history.push(str);
                MindConsole.current++;
            },
            back: function(){
                if(MindConsole.current == 0)
                    return false;
                MindConsole.current--;
                return MindConsole.history[MindConsole.current];
            },
            next: function(){
                if(MindConsole.current == MindConsole.history.length)
                    return false;
                MindConsole.current++;
                return MindConsole.history[MindConsole.current];
            }
        };
        
        $('#consoleCommand').bind('keyup', function(event){
            if(event.which == 38) // up
            {
                var comm= false;
                if(comm = MindConsole.back())
                    this.value= comm;
            }
            if(event.which == 40) // down
            {
                var comm= false;
                if(comm= MindConsole.next())
                    this.value= comm;
                else{
                    MindConsole.add(this.value);
                    this.value= '';
                }
            }
            if(event.which == 13)
            {
                this.value= this.value.replace(/[\t\n]/g, '');
                MindConsole.add(this.value);
                if(this.value == 'clear')
                {
                    var el= document.getElementById('result');
                    var scr= document.getElementById('scrollingDiv');
                    el.innerHTML= '';
                    this.value= '';
                    return;
                }
                var commandToExecute= this.value;
                $.ajax({
						type:'POST',
						url:'../../',
						data:{
							program:'eval',
							command: commandToExecute
						},
						success: function(retQ){
                            var el= document.getElementById('result');
							el.innerHTML+= commandToExecute+"\n"+retQ;
                            //el.scrollIntoView(true);
                            document.getElementById('scrollingDiv').scrollTop= el.offsetHeight;
						}
					});
                this.value= '';
            }
        });
        function adjust()
        {
            document.getElementById('scrollingDiv').style.height= ($(document).height() - 100)+"px";
            document.getElementById('consoleCommand').focus();
        }
        
        $('#scrollingDiv').bind('click', adjust);
        $(document).ready(adjust);
        $(window).bind('resize', adjust);
	</script>
</html>