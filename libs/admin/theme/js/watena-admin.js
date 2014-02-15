
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
	
	displayError("You are not logged in, proceed to verify your identity!");
}

function displayLogin() {
	$("#overlay_login").show();
	$("#login_usn").focus();
}

function displayError(sMessage, sTitle, cbOk) {
	$("#overlay_error").show();
	$("#overlay_error .content").text(sMessage);
}

function displaySucces(sMessage, sTitle, cbOk) {
	$("#overlay_succes").show();
	$("#overlay_succes .content").text(sMessage);
}

function displayInfo(sMessage, sTitle, cbOk) {
	$("#overlay_info").show();
	$("#overlay_info .content").text(sMessage);
}
