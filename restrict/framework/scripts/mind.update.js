Mind.Update = {
	url : "http://felipe/flpnm/thewebmind.org/update/update.php",
	Verify :  function(){
		var html = $.ajax({
			url: Mind.Update.url,
			async: false
		}).responseText;
		Mind.Dialog.OpenModal(true,"400","400","Mind Update","middle",html,"");
	}
}