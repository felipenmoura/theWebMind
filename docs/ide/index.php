<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
        <link rel="shortcut icon" href="images/logo.png" />
        <link href="css/ui-lightness/jquery-ui-1.8.12.custom.css" rel="stylesheet" type="text/css" />
        
		<script src='scripts/jquery.js'></script>
		<script src='scripts/jquery-ui.js'></script>
        
		<style tye='text/css'>
            td
            {
                vertical-align:top;
            }
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
        <div id="MindConsoleWindow" style="position:absolute; height:400px; width:90%;">
            <table style="width:100%;height:100%;" border="1">
                <tr>
                    <td id='MindConsoleWindowTitle' style="height:30px;">
                        Console
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top;background-color: black;">
                        <div style='overflow-y:auto;
                                    overflow-x:hidden;
                                    color:white;'
                             id="scrollingDiv">
                            <table style='width:100%;'>
                                <tr>
                                    <td colspan="2">
                                        <div  id='result'
                                              style='width:100%;
                                                     height:100%;
                                                     font-family: Courier New;
                                                     white-space:pre-wrap;'></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='vertical-align:top;
                                               font-family: Courier New;
                                               width:75px;'>
                                        mind3rd>
                                    </td>
                                    <td style="height:20px;">
                                        <textarea style='width:100%;
                                                         margin: 0px;
                                                         border: none;
                                                         color:white;
                                                         height:100%;
                                                         font-family: Courier New;
                                                         background-color: black;
                                                         resize: none;'
                                                 border="0"
                                                 id='consoleCommand'></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
		<input type='button' value='create demo_en project' onclick="createDemo_en()"/>
		<input type='button' value='run example' onclick="exampleModel()"/>
		<input type='button' value='API Facade tests' onclick="APITest()"/>
	</body>
	<script>
        var consoleCall= "mind3rd>";
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
            },
            focus: function()
            {
            }
        };
        
        $('#consoleCommand').bind('keyup', function(event){
            if(event.which == 38) // up
            {
                var comm= false;
                if(comm = MindConsole.back())
                {
                    this.value= comm;
                    MindConsole.focus();
                }
            }
            if(event.which == 40) // down
            {
                var comm= false;
                if(comm= MindConsole.next())
                {
                    this.value= comm;
                    MindConsole.focus();
                }else{
                    MindConsole.add(this.value);
                    this.value= '';
                    MindConsole.focus();
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
							el.innerHTML+= consoleCall+commandToExecute+"\n"+retQ;
                            
                            if(retQ.substring(retQ.length -2) != "\n")
                                el.innerHTML+= "\n";
                            
                            document.getElementById('scrollingDiv').scrollTop= el.offsetHeight;
						}
					});
                this.value= '';
                this.parentNode.style.height= '20px';
                MindConsole.focus();
            }else{
                if(this.scrollTop > 0)
                {
                    this.parentNode.style.height= this.parentNode.offsetHeight+30+"px";
                }
            }
        });
        function adjust()
        {
            var sd= document.getElementById('scrollingDiv');
            sd.style.height= sd.parentNode.offsetHeight+"px";
            sd.style.width= sd.parentNode.offsetWidth+"px";
            document.getElementById('consoleCommand').focus();
            sd.style.display= '';
        }
        
        $('#MindConsoleWindow').draggable({handle:'#MindConsoleWindowTitle'}).resizable({
            ghost:true,
            start:function(){
                document.getElementById('scrollingDiv').style.display= 'none';
            },
            stop: adjust
        });
        
        $('#MindConsoleWindow').bind('click', function(){
            document.getElementById('consoleCommand').focus();
        }).bind('dblclick', function(){
            document.getElementById('consoleCommand').select();
        });
        $(document).ready(adjust);
        //$(window).bind('resize', adjust);
	</script>
</html>