/**
 * All the internals of the TMX system, someday I need to obfuscate this page
 * 
 * @author Jelle Voet - ToMo-design
 * @version 2.1.2 beta
 * 
 * VERSION-LOG
 * -----------
 * 
 * 3-9-2010: 2.1.1 => 2.1.2
 * - Fixed a bug when a selector selects 'null' (ex: getElementById(unknown))
 * 
 * 31-8-2010: 2.1.0 => 2.1.1
 * - Fixed a bug within TMX_RemoveDOM involving references in an array vs nodelist
 * - Added support for sequential logic
 * 
 * 30-8-2010: 2.0.3 => 2.1.0
 * - Added JSON support when sending data to the server (we now support arrays and advanced types)
 * - Added a custom JSON class for incompatible browsers
 * 
 * 1-8-2010: 2.0.1 => 2.0.3
 * - Added IE compatibility (and probably some other browsers to)
 * - Fixed a bug in the DOM_ins routine
 * - Removed some older functions
 * 
 * 30-7-2010: 2.0.0 => 2.0.1
 * - Bugfixes
 * 
 * 30-7-2010: 1.0.0 => 2.0.0
 * - Anticipate all the changes made to the php-scripting
 * -- JSON
 * -- encodeURIComponent
 * -- innerHTML becomes DOM
 * -- New communication protocol
 */

var TMX_aRequest = new Array();
var TMX_oRequest = null;
var TMX_nSendIndex = 0;
var TMX_nReceiveIndex = 0;

// parse the response
function TMX_ParseResponse(sResponse) {
	if(TMX_ParseResponseCode(sResponse)) {
		var aJSON = JSON.parse(sResponse.substring(1));
		for(var i in aJSON) {
			var aData = aJSON[i];
			if(aData[0] == 'call') {
				if(aData[1][0] == "alert") alert(aData[1][1]);
				else window[aData[1][0]].apply(this, aData[1][1]);
			}
			if(aData[0] == 'DOM_ins') {
				var aList = TMX_Select(aData[1][0]);
				for(var j in aList) {
					if(aData[1][2]) {
						TMX_RemoveDOM(aList[j].childNodes);
					}
					TMX_LoadDOM(aList[j], aData[1][1]);
				}
			}
			if(aData[0] == 'DOM_del') {
				TMX_RemoveDOM(TMX_Select(aData[1][0]));
			}
			if(aData[0] == 'STYLE') {
				var aList = TMX_Select(aData[1][0]);
				for(var j in aList) {
					if(aList[j].style) aList[j].style[aData[1][1]] = aData[1][2];
				}
			}
			if(aData[0] == 'CLASS') {
				var aList = TMX_Select(aData[1][0]);
				for(var j in aList) {
					if(aList[j].className) aList[j].className = aData[1][1];
				}
			}
			if(aData[0] == 'JS_add') {
				TMX_AddExternalFile(aData[1][0], true);
			}
			if(aData[0] == 'JS_rep') {
				TMX_ReplaceExternalFile(aData[1][0], aData[1][1], true);
			}
			if(aData[0] == 'JS_rem') {
				TMX_RemoveExternalFile(aData[1][0], true);
			}
			if(aData[0] == 'CSS_add') {
				TMX_AddExternalFile(aData[1][0], false);
			}
			if(aData[0] == 'CSS_rep') {
				TMX_ReplaceExternalFile(aData[1][0], aData[1][1], false);
			}
			if(aData[0] == 'CSS_rem') {
				TMX_RemoveExternalFile(aData[1][0], false);
			}
		}
		window.focus();
	}
	return sResponse;
}

// Retrieve a list of node(s) based on the layered selector-data
function TMX_Select(aData) {
	var aRoot = new Array(document);
	for(var i in aData) {
		var aReturn = new Array();
		for(var j in aRoot) {
			aReturn = aReturn.concat(TMX_SelectLayer(aRoot[j], aData[i]));
		}
		aRoot = aReturn;
		if(aRoot.length == 0) break;
	}
	return aRoot;
}

// remove a list of DOM node(s)
function TMX_RemoveDOM(aList) {
	var nIndex = 0;
	while(aList.length > nIndex) {
		var nLength = aList.length;
		aList[nIndex].parentNode.removeChild(aList[nIndex]);
		if(nLength == aList.length) ++nIndex;
	}
}

// perform a select by layer starting from the given root-node(s)
function TMX_SelectLayer(aRoot, aData) {
	var obj;
	var attribute;
	var value;
	switch(aData[0]) {
		case 0 : 
			obj = new Array(aRoot.getElementById(aData[1])); // ID
			break;
		case 4 : 
			obj = new Array(aRoot.firstChild); // FirstChild
			break;
		case 5 : 
			obj = new Array(aRoot.lastChild); // LastChild
			break;
		case 1 : 
			obj = aRoot.getElementsByName(aData[1]); // Name
			if(aData.length > 2) {
				attribute = aData[2];
				value = aData[3];
			}
			break;
		case 2 : 
			obj = aRoot.getElementsByTagName(aData[1]); // Tag
			if(aData.length > 2) {
				attribute = aData[2];
				value = aData[3];
			}
			break;
		case 3 : 
			obj = aRoot.childNodes; // Childs
			if(aData.length > 1) {
				attribute = aData[1];
				value = aData[2];
			}
			break;
	}
	var aReturn = new Array();
	for(var i=0 ; i<obj.length ; ++i) {
		if(obj[i] && (!attribute || !value || (obj[i].getAttribute && obj[i].getAttribute(attribute) == value))) aReturn.push(obj[i]);
	}
	return aReturn;
}

// load DOM data
function TMX_LoadDOM(oRoot, aData) {
	for(var i in aData) {
		var oItem = aData[i];
		if(TMX_IsString(oItem)) {
			var oEl = document.createTextNode(oItem);
			oRoot.appendChild(oEl);
		}
		else {
			var oEl = document.createElement(oItem['n']);
			if(oItem['a']) {
				for(var sKey in oItem['a']) {
					oEl.setAttribute(sKey, oItem['a'][sKey]);
				}				
			}
			if(oItem['c']) {
				TMX_LoadDOM(oEl, oItem['c']);
			}
			oRoot.appendChild(oEl);
		}		
	}
}

// create an element reference to load extrenal files in
function TMX_GetExternalFileElementTag(sFile, bJS) {
	var oFileref = document.createElement(bJS ? 'script' : 'link');
	if(bJS) {
		oFileref.setAttribute("type","text/javascript");
		oFileref.setAttribute("src", sFile);
	}
	else {
		oFileref.setAttribute("rel", "stylesheet");
		oFileref.setAttribute("type", "text/css");
		oFileref.setAttribute("href", sFile);
	}
	return oFileref;
}

// add a external file
function TMX_AddExternalFile(sNewFile, bJS) {
	var aList = document.getElementsByTagName('head').item(0).appendChild(TMX_GetExternalFileElementTag(sNewFile, bJS));
}

// replace a loaded external file
function TMX_ReplaceExternalFile(sOldFile, sNewFile, bJS) {
	var aList = document.getElementsByTagName(bJS ? 'script' : 'link');
	for(var i=aList.length; i>=0; --i) {
		if(aList[i] && aList[i].getAttribute(bJS ? 'src' : 'href') != null && aList[i].getAttribute(bJS ? 'src' : 'href').indexOf(sOldFile)!=-1) {
			aList[i].parentNode.replaceChild(TMX_GetExternalFileElementTag(sNewFile, bJS), aList[i]);
 		}
	}
}

// remove a loaded external file
function TMX_RemoveExternalFile(sOldFile, bJS) {
	var aList = document.getElementsByTagName(bJS ? 'script' : 'link');
	for(var i=aList.length; i>=0; --i) {
		if(aList[i] && aList[i].getAttribute(bJS ? 'src' : 'href') != null && aList[i].getAttribute(bJS ? 'src' : 'href').indexOf(sOldFile)!=-1) {
			aList[i].parentNode.removeChild(aList[i]);
 		}
	}
}

// parse the resonse-code for client-sided awareness when a server-error occured
function TMX_ParseResponseCode(sResponse) {
	if(sResponse.substring(0, 1).length == 1) {
		nCode = parseInt(sResponse.substring(0, 1));
		if(nCode == 1) {return true;}
		if(nCode == 2) return TMX_Error('Serversided PHP error: ' + sResponse.substring(1));
		if(nCode == 3) return TMX_Error('Problem with POST-values: ' + sResponse.substring(1));
		if(nCode == 4) return TMX_Error('Problem setting TMX-Response object: ' + sResponse.substring(1));
	}
	return TMX_Error('No valid TMX-response-code found: ' + sResponse);
}

// debug-function
function TMX_Error(sMessage) {
	alert(sMessage);
	return false;
}

// get the prefix needed for the POST-data
function TMX_GetPrefix() {
	return TMX_sPrefix ? TMX_sPrefix : 'TMX';
}

// check if is string
function TMX_IsString(obj) {
	return obj && (obj.constructor.toString().indexOf("String") != -1);
}

// structure that represents a TMX-request (on the client)
function TMX_Request(oRequest, sURL, PHPCallback, sArgs, sValues) {
	this.m_oRequest = oRequest;
	this.m_sURL = sURL;
	this.m_PHPCallback = PHPCallback;
	this.m_sArgs = sArgs;
	this.m_sValues = sValues;
	
	this.GetSendString = function(sPrefix) {
		var sTmp = sPrefix+'_PHPCallback='+this.m_PHPCallback+'&';
		sTmp += sPrefix+'_Args='+this.m_sArgs+'&';
		sTmp += sPrefix+'_Values='+this.m_sValues;
		return sTmp;
	}
	
	this.GetParamCount = function() {
		return 4;
	}
}

// create a crossbrowser request
function TMX_CreateRequest() {
    var request = false;
    try {
    	request = new ActiveXObject('Msxml2.XMLHTTP');
    }
    catch (err1) {
         try {
             request = new ActiveXObject('Microsoft.XMLHTTP');
         }
         catch (err2) {
			try {
				request = new XMLHttpRequest();
			}
			catch (err3)  {
				request = false;
			}
    	}
    }
    return request;
}

// if a request exists is queued and at the current moment no other request is processed, call the next one
function TMX_Dispatch() {
	if(TMX_oRequest == null && TMX_aRequest.length > 0) {
		setTimeout("TMX_ProcessCountdown(" + (++TMX_nSendIndex) + ");", 30000);
		var oRequest = TMX_aRequest.shift();
		TMX_oRequest = oRequest.m_oRequest;
		TMX_oRequest.open("POST", oRequest.m_sURL, true);
		TMX_oRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		TMX_oRequest.setRequestHeader("Content-length", oRequest.GetParamCount());
		TMX_oRequest.setRequestHeader("Connection", "close");
		TMX_oRequest.onreadystatechange = TMX_StateChange;
		TMX_oRequest.send(oRequest.GetSendString(TMX_GetPrefix()));
	}
	return true;
}

// add a request to the queue
function TMX_Send(sURL, PHPCallback, aArgs, sValues) {
	oRequest = TMX_CreateRequest();
	if(oRequest) {
		TMX_aRequest.push(new TMX_Request(oRequest, decodeURIComponent(sURL), PHPCallback, JSON.stringify(aArgs), sValues));
		return TMX_Dispatch();
	}
	else {
		TMX_Error("Your browser does not support XMLHTTP.");
		return false;
	}
}

// callback function when a request-state changes
function TMX_StateChange() {
	if(/*this != null && this == TMX_oRequest && */TMX_oRequest.readyState == 4) {
		if(TMX_oRequest.status == 200) {
			++TMX_nReceiveIndex;
    		TMX_ParseResponse(TMX_oRequest.responseText);
    	}
  		else {
    		TMX_Error("Problem retrieving data:" + TMX_oRequest.statusText);
    	}
    	TMX_oRequest = null;
  		TMX_Dispatch();
  	}
}

// the countdown process that checks for incomplete requests
function TMX_ProcessCountdown(nIndex) {
	if(nIndex > TMX_nReceiveIndex) {
		TMX_Error("Unable to process the dynamic request, check your connection.");
	}
}