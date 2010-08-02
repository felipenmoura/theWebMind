<?php
	$_GET['page']= urlencode(addslashes(strip_tags($_GET['page'])));
?>
<table style='width:100%;
			  height:100%;'>
	<tr style='border-bottom:solid 1px #bbb;'>
		<td style='height:25px;border-bottom:solid 1px #666;vertical-align:top;'>
			<input type='button'
				   value='<<'
				   class='ui-state-default ui-corner-all'
				   onclick="helpTopicsContent.history.go(-1);"/>
			<input type='button'
				   value='>>'
				   class='ui-state-default ui-corner-all'
				   onclick="helpTopicsContent.history.go(+1);"/>
			<input type='text'
				   id='searchInput'/>
			<img src='images/search_for.png'
				 style='cursor:pointer;'
				 onclick="document.getElementById('helpTopicsContent').src= 'http://docs.thewebmind.org/index.php?title=Special%3ASearch&search='+document.getElementById('searchInput').value+'&go=Go'"/>
			<img src='images/home.png'
				 style='cursor:pointer;'
				 onclick="document.getElementById('helpTopicsContent').src= 'http://docs.thewebmind.org/index.php?title=Guia-do-desenvolvedor'"/>
		</td>
	</tr>
	<tr>
		<td>
			<iframe name='helpTopicsContent'
					id='helpTopicsContent'
					src='http://docs.thewebmind.org/index.php?title=<?php
						echo ($_GET['page'] == 'false')? 'Guia-do-desenvolvedor': $_GET['page'];
					?>'
					style='width:100%;
						   height:100%;
						   border:solid 1px #66a;'
					frameborder='0'
					border='none'></iframe>
		</td>
	</tr>
</table>