
var oWatena = new (function() {
	this.sSearchDefault = '';
	this.cbOverlayCross = null;
	this.cbOverlayButton = null;
	this.sContinueMapping = null;
})();

function execute(callback) {
	if(typeof callback == 'string' || callback instanceof String)
		eval('('+callback+')()');
	else
		callback();
}

function loaderCallback() {
	requestNavItems();
	
	if(location.hash.length > 0)
		requestLoadingContent(location.hash.substr(1));
	else
		requestLoadingContent('/');

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

function displayLogin(sUserName, sUserNameError, sPasswordError, sContinueMapping) {
	$(".overlay").hide();
	$("#overlay_login").show();
	$("#login_usn").val(sUserName);
	$("#login_usn_error").text(sUserNameError);
	$("#login_pwd_error").text(sPasswordError);
	$("#login_usn").focus();
	oWatena.sContinueMapping = sContinueMapping;
	oWatena.cbOverlayCross = function() {
		requestLoadingContent(sContinueMapping);
	};
	oWatena.cbOverlayButton = function() {
		displayLoading("Processing login", function() {
			requestLogin(
				$("#login_usn").val(),
				$("#login_pwd").val(),
				oWatena.sContinueMapping
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
	$("#overlay_succes .center .title").text(sTitle);
	$("#overlay_succes .center .content").text(sMessage);
	oWatena.cbOverlayCross = cbOk;
	oWatena.cbOverlayButton = cbOk;
}

function displayInfo(sMessage, sTitle, cbOk) {
	$(".overlay").hide();
	$("#overlay_info").show();
	$("#overlay_info .content").text(sMessage);
}

function displayNavItems(aNavs) {
	$('.nav-item').remove();
	$.each(aNavs, function(nIndx, lElement) {
		oCategory = $('<div class="nav-category"><span class="title"></span><ul class="nav-list"></ul></div>');
		oCategory.find('.title').text(lElement.name);
		$.each(lElement.subitems, function(nSubIndex, lSubElement) {
			oItem = $('<li class="nav-item"></li>').text(lSubElement.name);
			//oItem.attr('title', lSubElement.description).tooltip();
			oItem.click(function() {requestLoadingContent(lSubElement.mapping);});
			oCategory.find('.nav-list').append(oItem);
		});
		$('#nav-logo, .nav-category').last().after(oCategory);
	});
}

function displayModuleTabs(sTitle, sDescription, aTabs) {
	$('.overlay').hide();
	$('#main-tabs').show();
	$('.tabs-item').remove();
	$('#tabs-title').text(sTitle).attr('title', sDescription).tooltip();
	$.each(aTabs, function(nIndex, lElement) {
		oItem = $('<li class="tabs-item"></li>').text(lElement.name);
		oItem.attr('title', lElement.description).tooltip();
		oItem.click(function() {requestLoadingContent(lElement.mapping);});
		$('#tabs-list').append(oItem);
	});
}

function displayModuleInfo(sName, sVersion, sDescription) {
	$('.overlay').hide();
	$('#main-module').show();
	$('#module-name').text(sName);
	$('#module-version').text(sVersion);
	$('#module-description').text(sDescription);
}

function displayModuleContent(sTitle, sDescription, sContent) {
	$('.overlay').hide();
	$('#main-content').show();
	$('#content-title').text(sTitle).attr('title', sDescription).tooltip();
	$('#content-content').html(sContent);
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
		location.hash = '#' + sMapping;
		requestContent(sMapping);
	});
}