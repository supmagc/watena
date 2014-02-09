
var oWatena = new (function() {
	this.sSearchDefault = '';
})();

function loaderCallback() {
	requestContent();

	oWatena.sSearchDefault = $("#search_txt").val();
	$("#search_txt").focus(function() {
		if(this.value == oWatena.sSearchDefault)
			this.value = "";
	});
	$("#search_txt").blur(function() {
		if(this.value == "")
			this.value = oWatena.sSearchDefault;
	});
	
	$(".overlay_close").click(function() {
		$(".overlay").hide();
	});
	
	displayLogin();
}

function displayLogin() {
	$("#overlay_login").show();
	$("#login_usn").focus();
}