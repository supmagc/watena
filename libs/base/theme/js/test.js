
var oWatena = new (function() {
	this.sSearchDefault = '';
	this.cbOverlayCancel = null;
	this.cbOverlayButton = null;
	this.sLastMapping = null;
	this.sNextMapping = null;
})();

function execute(callback) {
	if(typeof callback == 'string' || callback instanceof String)
		eval('('+callback+')()');
	else if(callback != undefined)
		callback();
}

function loaderCallback() {
	if(location.hash.length > 0)
		requestLoadingContent(true, location.hash.substr(1));
	else
		requestLoadingContent(true, '/');

	oWatena.sSearchDefault = $("#search_txt").val();
	$("#search_txt").focus(function() {
		if(this.value == oWatena.sSearchDefault)
			this.value = "";
	});
	$("#search_txt").blur(function() {
		if(this.value == "")
			this.value = oWatena.sSearchDefault;
	});
	
	$(".overlay .close.clickable, .overlay .cancel.clickable").click(function(oEvent) {
		clearOverlay();
		execute(oWatena.cbOverlayCancel);
	});
	
	$(".overlay .footer.clickable, .overlay .confirm.clickable").click(function() {
		clearOverlay();
		execute(oWatena.cbOverlayButton);
	});
	
	$("#nav-logout.clickable").click(function() {
		displayConfirm("Are you sure you want to logout ?", "Logout", function() {
			displayLoading("Processing logout", requestLogout);
		});
	});
}

function setShowSearch(bShow) {
	if(bShow)
		$("#nav-search").show();
	else
		$("#nav-search").hide();
}

function setShowLogout(bShow) {
	if(bShow)
		$("#nav-logout").show();
	else
		$("#nav-logout").hide();
}

function displayLogin(sUserName, sUserNameError, sPasswordError, sNextMapping) {
	clearOverlay();
	$("#overlay_login").show();
	$("#login_usn").val(sUserName);
	$("#login_usn_error").text(sUserNameError);
	$("#login_pwd_error").text(sPasswordError);
	$("#login_usn").focus();
	oWatena.sNextMapping = sNextMapping;
	oWatena.cbOverlayCancel = function() {
		requestLoadingContent(true, sNextMapping);
	};
	oWatena.cbOverlayButton = function() {
		clearOverlay();
		displayLoading("Processing login", function() {
			requestLogin(
				$("#login_usn").val(),
				$("#login_pwd").val(),
				oWatena.sNextMapping
			);
		});
	};
}

function displayError(sMessage, sTitle, cbOk) {
	clearOverlay();
	$("#overlay_error").show();
	$("#overlay_error .center .title").text(sTitle);
	$("#overlay_error .center .content").text(sMessage);
	oWatena.cbOverlayCancel = cbOk;
	oWatena.cbOverlayButton = cbOk;
}

function displaySucces(sMessage, sTitle, cbOk) {
	clearOverlay();
	$("#overlay_succes").show();
	$("#overlay_succes .center .title").text(sTitle);
	$("#overlay_succes .center .content").text(sMessage);
	oWatena.cbOverlayCancel = cbOk;
	oWatena.cbOverlayButton = cbOk;
}

function displayInfo(sMessage, sTitle, cbOk) {
	clearOverlay();
	$("#overlay_info").show();
	$("#overlay_info .center .title").text(sTitle);
	$("#overlay_info .center .content").text(sMessage);
	oWatena.cbOverlayCancel = cbOk;
	oWatena.cbOverlayButton = cbOk;
}

function displayConfirm(sMessage, sTitle, cbOk) {
	clearOverlay();
	$("#overlay_confirm").show();
	$("#overlay_confirm .center .title").text(sTitle);
	$("#overlay_confirm .center .content").text(sMessage);
	oWatena.cbOverlayCancel = undefined;
	oWatena.cbOverlayButton = cbOk;
}

function displayLoading(sTitle, cbTimeout) {
	if(sTitle != undefined && sTitle != "") {
		$("#overlay_error .header .title").text(sTitle);
	}
	$(".overlay").hide();
	$("#overlay_loading").show();
	if(cbTimeout != undefined && cbTimeout != "") {
		setTimeout(cbTimeout, 500);
	}
}

function clearOverlay() {
	$(".overlay").hide();
}

function displayNavItems(aNavs) {
	clearNavItems();
	$.each(aNavs, function(nIndx, lElement) {
		oCategory = $('<div class="nav-category"><span class="title"></span><ul class="nav-list"></ul></div>');
		oCategory.find('.title').text(lElement.name);
		$.each(lElement.items, function(nSubIndex, lSubElement) {
			oItem = $('<li class="nav-item"></li>').text(lSubElement.name);
			//oItem.attr('title', lSubElement.description).tooltip();
			oItem.click(function() {requestLoadingContent(false, lSubElement.mapping);});
			oCategory.find('.nav-list').append(oItem);
		});
		$('#nav-logo, .nav-category').last().after(oCategory);
	});
}

function clearNavItems() {
	$('.nav-category').remove();
}

function displayModuleTabs(sTitle, sDescription, aTabs) {
	clearOverlay();
	clearModuleTabs();
	$('#main-tabs').show();
	$('#tabs-title').text(sTitle).attr('title', sDescription).tooltip();
	$.each(aTabs, function(nIndex, lElement) {
		oItem = $('<li class="tabs-item"></li>').text(lElement.name);
		oItem.attr('title', lElement.description).tooltip();
		oItem.click(function() {requestLoadingContent(false, lElement.mapping);});
		$('#tabs-list').append(oItem);
	});
}

function clearModuleTabs() {
	$('#main-tabs').hide();
	$('.tabs-item').remove();
}

function displayModuleInfo(sName, sVersion, sDescription) {
	clearOverlay();
	$('#main-module').show();
	$('#module-name').text(sName);
	$('#module-version').text(sVersion);
	$('#module-description').text(sDescription);
}

function clearModuleInfo() {
	$('#main-module').hide();	
}

function displayModuleContent(sTitle, sDescription, sContent) {
	clearOverlay();
	$('#main-content').show();
	$('#content-title').text(sTitle).attr('title', sDescription).tooltip();
	$('#content-content').html(sContent);
	oWatena.sLastMapping = location.hash.substr(1);
}

function clearModuleContent() {
	$('#main-content').hide();	
}

function requestLoadingContent(bInit, sMapping, sAction, aData, aState) {
	displayLoading("Get content", function() {
		location.hash = '#' + sMapping;
		requestContent(bInit, oWatena.sLastMapping, sMapping, sAction, aData, aState);
	});
}