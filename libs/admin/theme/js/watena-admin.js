
var oWatena = new (function() {
	this.sSearchDefault = '';
	this.cbOverlayCross = null;
	this.cbOverlayButton = null;
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
	
	$(".overlay .close").click(function() {
		$(".overlay").hide();
		eval(oWatena.cbOberlayCross);
	});
	
	$(".overlay .footer").click(function() {
		$(".overlay").hide();
		eval(oWatena.cbOverlayButton);
	});
	
	displayError("You are not logged in, proceed to verify your identity!", "Authentication Failure", "displayLogin()");
}

function displayLogin() {
	$("#overlay_login").show();
	$("#login_usn").focus();
}

function displayError(sMessage, sTitle, cbOk) {
	$("#overlay_error").show();
	$("#overlay_error .center .title").text(sTitle);
	$("#overlay_error .center .content").text(sMessage);
	oWatena.cbOverlayCross = cbOk;
	oWatena.cbOverlayButton = cbOk;
}

function displaySucces(sMessage, sTitle, cbOk) {
	$("#overlay_succes").show();
	$("#overlay_succes .content").text(sMessage);
}

function displayInfo(sMessage, sTitle, cbOk) {
	$("#overlay_info").show();
	$("#overlay_info .content").text(sMessage);
}
