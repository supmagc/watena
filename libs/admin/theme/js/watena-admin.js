
var oWatena = new (function() {
	this.sSearchDefault = '';
	this.cbOverlayCross = null;
	this.cbOverlayButton = null;
})();

function execute(callback) {
	if(typeof callback == 'string' || callback instanceof String)
		eval('('+callback+')()');
	else
		callback();
}

function loaderCallback() {
	requestContent('/');

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
		execute(oWatena.cbOverlayCross);
	});
	
	$(".overlay .footer").click(function() {
		$(".overlay").hide();
		execute(oWatena.cbOverlayButton);
	});
}

function displayLogin(sUserName, sUserNameError, sPasswordError) {
	$(".overlay").hide();
	$("#overlay_login").show();
	$("#login_usn").val(sUserName);
	$("#login_usn_error").text(sUserNameError);
	$("#login_pwd_error").text(sPasswordError);
	$("#login_usn").focus();
	oWatena.cbOverlayCross = function() {
		requestContent();
	};
	oWatena.cbOverlayButton = function() {
		displayLoading("Processing login", function() {
			requestLogin(
				$("#login_usn").val(),
				$("#login_pwd").val()
			);
			
		});
	};
}

function displayError(sMessage, sTitle, cbOk) {
	$(".overlay").hide();
	$("#overlay_error").show();
	$("#overlay_error .center .title").text(sTitle);
	$("#overlay_error .center .content").text(sMessage);
	oWatena.cbOverlayCross = cbOk;
	oWatena.cbOverlayButton = cbOk;
}

function displaySucces(sMessage, sTitle, cbOk) {
	$(".overlay").hide();
	$("#overlay_succes").show();
	$("#overlay_succes .content").text(sMessage);
}

function displayInfo(sMessage, sTitle, cbOk) {
	$(".overlay").hide();
	$("#overlay_info").show();
	$("#overlay_info .content").text(sMessage);
}

function displayModuleTabs(sTitle, aTabs) {
	$(".overlay").hide();
	$("#tabs-title").text(sTitle);
	var oTemplate = $(".tabs-item").first().clone();
	$('.tabs-item').remove();
	$.each(aTabs, function(nIndex, lElement) {
		oTemplate.text(lElement.name);
		oTemplate.click(function() {requestLoadingContent(lElement.mapping);});
		$('#tabs-list').append(oTemplate.clone(true));
	});
}

function displayModuleInfo(sName, sVersion, sDescription) {
	$(".overlay").hide();
	$("#module-name").text(sName);
	$("#module-version").text(sVersion);
	$("#module-description").text(sDescription);
}

function displayModuleContent(sTitle, sContent) {
	$(".overlay").hide();
	$("#content-title").text(sTitle);
	$("#content-content").html(sContent);
}

function displayLoading(sTitle, cbTimeout) {
	if(sTitle != undefined && sTitle != "") {
		$("#overlay_error .header .title").text(sTitle);
	}
	$(".overlay").hide();
	$("#overlay_loading").show();
	if(cbTimeout != undefined && cbTimeout != "") {
		setTimeout(cbTimeout, 1000);
	}
}

function requestLoadingContent(sMapping) {
	displayLoading("Get content", function() {
		requestContent(sMapping);
	});
}