// Simple help description text at the bototm of the screen
function PTM_SetSimpleHelp(sDescription) {
	if(document.getElementById('HelpContainer')) document.getElementById('HelpContainer').innerHTML = sDescription;
}

// Slightly more advanced help description display at the bottomp, and at the mous-pointer with the use of overlib
function PTM_SetHelp(sDescription) {
	if(document.getElementById('HelpContainer')) document.getElementById('HelpContainer').innerHTML = sDescription;
	if(document.getElementById('OverLibCheckBox').checked) overlib(sDescription, WIDTH, 350, DELAY, 1000, SHADOW);
}

// Clear the help text
function PTM_CH() {
	if(document.getElementById('HelpContainer')) document.getElementById('HelpContainer').innerHTML = "";
	return nd();
}

// set the size of the internal elements (styling stuff)
function PTM_SetSize() {
	myWidth = 0, myHeight = 0;
	if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		//IE 6+ in 'standards compliant mode'
	    myWidth = document.documentElement.clientWidth;
	    myHeight = document.documentElement.clientHeight;
	} else {
		if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
	    	//IE 4 compatible
	        myWidth = document.body.clientWidth;
	        myHeight = document.body.clientHeight;
	    }
	}
	document.getElementById('CenterGroup').style.height = '' + (myHeight - 86) + 'px';
	if(document.getElementById('ContentTable')) document.getElementById('ContentTable').style.height = '' + (myHeight - 86) + 'px';
	if(document.getElementById('ContentCell')) document.getElementById('ContentCell').style.height = '' + (myHeight - 136) + 'px';

	var nScrollbar = document.getElementById('CenterGroup').offsetWidth - document.getElementById('CenterGroup').clientWidth;
	if(nScrollbar > 0) {
		if(document.getElementById('ContentTable')) document.getElementById('ContentTable').style.width = '' + (myWidth - nScrollbar) + 'px';
		if(document.getElementById('ContentCell')) document.getElementById('ContentCell').style.width = '' + (myWidth - nScrollbar - 50) + 'px';
	}
}

// General init funtion called during the onload-event
function PTM_Init() {
	if(navigator.userAgent.indexOf("MSIE")!=-1) {
		window.onresize = PTM_SetSize;
		PTM_SetSize();
	}
	if(PTM_GetCookie("OverLibHelp") == "false") document.getElementById('OverLibCheckBox').checked = false;
	setTimeout('PTM_StartupCommand();', 2500);
}

// Clear all content holders
function PTM_SetClearDisplay() {
	document.getElementById('LoadingTable').style.display = 'none';
	document.getElementById('ErrorTable').style.display = 'none';
	document.getElementById('SuccesTable').style.display = 'none';
	document.getElementById('ConfirmTable').style.display = 'none';
	document.getElementById('LoginTable').style.display = 'none';
	document.getElementById('ModuleTable').style.display = 'none';
}

function PTM_SetLoadingDisplay() {
	PTM_SetClearDisplay();
	document.getElementById('CenterGroup').style.backgroundColor = '#9999AA';
	document.getElementById('LoadingTable').style.display = 'table';
}

// Display an error message
function PTM_SetErrorDisplay(sMessage) {
	PTM_SetClearDisplay();
	document.getElementById('CenterGroup').style.backgroundColor = '#660000';
	document.getElementById('ErrorTable').style.display = 'table';
	document.getElementById('RoundedErrorTableContent').innerHTML = sMessage;
}

// Display a succes message
function PTM_SetSuccesDisplay(sMessage) {
	PTM_SetClearDisplay();
	document.getElementById('CenterGroup').style.backgroundColor = '#339999';
	document.getElementById('SuccesTable').style.display = 'table';
	document.getElementById('RoundedSuccesTableContent').innerHTML = sMessage;
}

function PTM_SetLoginDisplay(sMessage, sUsername) {
	PTM_SetClearDisplay();
	document.getElementById('CenterGroup').style.backgroundColor = '#9999AA';
	document.getElementById('LoginTable').style.display = 'table';
	document.getElementById('LoginDescContent').innerHTML = sMessage;
	if(sUsername.length > 0) document.getElementById('LoginUsnInput').value = sUsername;
}

function PTM_SetContentDisplay(sContentColor, sTabColor) {
	PTM_SetClearDisplay();
	document.getElementById('CenterGroup').style.backgroundColor = sContentColor;
	document.getElementById('ModuleTabMenuList').style.backgroundColor = sTabColor;
	document.getElementById('ModuleTable').style.display = 'table';
	//document.getElementById('LoginDescContent').innerHTML = sMessage;
}

function PTM_ModuleTabMenuAdd(sTitle, sName, sMapping, sDescription) {
	var aList = document.getElementsByName('ModuleTabMenuListItem_'+sName)
	if(aList.length == 0) {
		var oEl = document.createElement('li');
		oEl.setAttribute('name', 'ModuleTabMenuListItem_'+sName);
		oEl.setAttribute('onClick', "PTM_RequestContent('"+sMapping+"');");
		oEl.innerHTML = sName;
		document.getElementById('ModuleTabMenuList').appendChild(oEl);
	}
}

function PTM_ModuleTabMenuRemove(sName) {
	var aList = document.getElementsByName('ModuleTabMenuListItem_'+sName)
	for(var i=0 ; i<aList.length ; ++i) {
		aList[i].parentNode.removeChild(aList[i]);
	}
}

function PTM_ModuleTabContent(sTitle, sDescription, sContent) {
	document.getElementById('MTCTitle').innerHTML = sTitle;
	document.getElementById('MTCDescription').innerHTML = sDescription;
	document.getElementById('MTCContent').innerHTML = sContent;
}

// Update the main-menu with the given content
function PTM_SetMenuBlock(sContent) {
	document.getElementById('MenuGroup').innerHTML = sContent;
}

// Send a login request
function PTM_RequestLogin() {
	PTM_SetLoadingDisplay();
	PXTM_Login(document.getElementById('LoginUsnInput').value, document.getElementById('LoginPwdInput').value);
	document.getElementById('LoginPwdInput').value = "";	
}

// Request content
function PTM_RequestContent(sMap) {
	PTM_SetLoadingDisplay();
	PXTM_Content(sMap, false);
}

// Request inline content
function PTM_RequestContent(sMap) {
	PXTM_Content(sMap, false);
}

// Request the content to be resend
function PTM_RequestRefresh() {
	PTM_SetLoadingDisplay();
	setTimeout("PXTM_Refresh();", 1000);
}

// Switch display styles betweel the two given elements
function PTM_SwitchDisplay(IDa, IDb) {
	var tmp = document.getElementById(IDa).style.display;
	document.getElementById(IDa).style.display = document.getElementById(IDb).style.display;
	document.getElementById(IDb).style.display = tmp;
}

function PTM_OpenRegion(IDl, IDb) {
	document.getElementById(IDl).style.display = 'none';
	document.getElementById(IDb).style.display = 'block';
}

// Retrieve a cookie-value if available
function PTM_GetCookie(sName) {
	if(document.cookie.length > 0) {
  		nStart=document.cookie.indexOf(sName + "=");
  		if(sName != -1) { 
			nStart = nStart + sName.length+1; 
			nEnd = document.cookie.indexOf(";", sName);
			if(nEnd == -1) nEnd = document.cookie.length;
			return unescape(document.cookie.substring(nStart, nEnd));
		} 
	}
	return "";
}

// Set a new cookie-value
function PTM_SetCookie(sName, sValue, nExpireDays) {
	var oDate = new Date();
	oDate.setDate(oDate.getDate() + nExpireDays);
	document.cookie = sName + "=" + escape(sValue) + ((nExpireDays == null) ? "" : ";expires=" + oDate.toGMTString());
}