var TMX_aRequest = new Array();
var TMX_oRequest = null;
var TMX_sPrefix = 'TMX';
var TMX_nSendIndex = 0;
var TMX_nReceiveIndex = 0;

// debug-function
function TMX_Error(sMessage) {
	alert(sMessage);
}

// set the prefix needed for the POST-data
function TMX_SetPrefix(sPrefix) {
	TMX_sPrefix = sPrefix;
}

// parse the resônse-code for client-sided awareness when a server-error occured
function TMX_ParseResponseCode(sResponse) {
	if(sResponse.substring(0, 1).length == 1) {
		nCode = parseInt(sResponse.substring(0, 1));
		if(nCode == 1) {return true;}
		if(nCode == 2) TMX_Error('Serversided PHP error: ' + sResponse.substring(1));
		if(nCode == 3) TMX_Error('Problem with POST-values: ' + sResponse.substring(1));
		if(nCode == 4) TMX_Error('Problem setting TMX-Response object: ' + sResponse.substring(1));
	}
	TMX_Error('No valid TMX-response-code found: ' + sResponse);
	return false;
}

// parse the response
function TMX_ParseResponse(sResponse) {
	if(TMX_ParseResponseCode(sResponse)) {
		var oReg = new RegExp(/<script(.*?)>(.*?)<\/script>/);
		var aMatches = "";
		while((aMatches = oReg.exec(sResponse)) != null) {
			var fileref = null;
			var src="";
			var attributes = "";
			
			if(aMatches[1]) {
				attributes = aMatches[1];
				if((src = (attributes.split(/src=\"(.*?)\"/))) != attributes) {;
					fileref = document.createElement('script')
					fileref.setAttribute("type","text/javascript");
					fileref.setAttribute("src", src[1]);
				}
			}
			 
			if (fileref != null) {
				document.getElementsByTagName("head").item(0).appendChild(fileref);
			} else if(aMatches[2]) { 
				eval(aMatches[2]);
			}
			sResponse = sResponse.replace(/<script(.*?)>(.*?)<\/script>/, "");
		}
		window.focus();
	}
	return sResponse;
}

// create an element reference to load extrenal files in
function TMX_GetExternalFileElementTag(sFile, bJS) {
	var oFileref = document.createElement(bJS ? 'script' : 'link');
	if(bJS) {
		oFileref.setAttribute("type","text/javascript");
		oFileref.setAttribute("src", sFile);
	}
	else {
		oFileref.setAttribute("rel", "stylesheet")
		oFileref.setAttribute("type", "text/css")
		oFileref.setAttribute("href", sFile)
	}
	return oFileref
}

// Add a external file
function TMX_AddExternalFile(sNewFile, bJS) {
	var aList = document.getElementsByTagName('head').item(0).appendChild(TMX_GetExternalFileElementTag(sNewFile, bJS));
}

// Replace a loaded external file
function TMX_ReplaceExternalFile(sOldFile, sNewFile, bJS) {
	var aList = document.getElementsByTagName(bJS ? 'script' : 'link');
	for(var i=aList.length; i>=0; --i) {
		if(aList[i] && aList[i].getAttribute(bJS ? 'src' : 'href') != null && aList[i].getAttribute(bJS ? 'src' : 'href').indexOf(sOldFile)!=-1) {
			aList[i].parentNode.replaceChild(TMX_GetExternalFileElementTag(sNewFile, bJS), aList[i]);
 		}
	}
}

// Remove a loaded external file
function TMX_RemoveExternalFile(sOldFile, bJS) {
	var aList = document.getElementsByTagName(bJS ? 'script' : 'link');
	for(var i=aList.length; i>=0; --i) {
		if(aList[i] && aList[i].getAttribute(bJS ? 'src' : 'href') != null && aList[i].getAttribute(bJS ? 'src' : 'href').indexOf(sOldFile)!=-1) {
			aList[i].parentNode.removeChild(aList[i]);
 		}
	}
}

// structure that represents a TMX-request (on the client)
function TMX_Request(oRequest, sURL, PHPCallback, aArgs, aValues) {
	this.m_oRequest = oRequest;
	this.m_sURL = sURL;
	this.m_PHPCallback = PHPCallback;
	this.m_aArgs = aArgs;
	this.m_aValues = aValues;
	
	this.GetSendString = function(sPrefix) {
		var sTmp = sPrefix+'_PHPCallback='+this.m_PHPCallback+'&';

		sTmp += sPrefix+'_nArg_Count='+this.m_aArgs.length+'&';
		for(var i in this.m_aArgs) sTmp += sPrefix+'_sArg_'+i+'='+escape(this.m_aArgs[i])+'&';

		var nCount = 0;
		for(var i in this.m_aValues) {
			sTmp += sPrefix+'_sValue_'+nCount+'_name='+escape(i)+'&';
			sTmp += sPrefix+'_sValue_'+nCount+'_value='+escape(this.m_aValues[i])+'&';
			++nCount;
		}
		sTmp += sPrefix+'_nValue_Count='+nCount+'&';
		return sTmp;
	}
	
	this.GetParamCount = function() {
		return 3 + this.m_aArgs.length + (2 * this.m_aValues.length);
	}
}

function TMX_CreateRequest() {
    var request = false;
    try {
    	request = new ActiveXObject('Msxml2.XMLHTTP');
    }
    catch (err2) {
         try {
             request = new ActiveXObject('Microsoft.XMLHTTP');
         }
         catch (err3) {
			try {
				request = new XMLHttpRequest();
			}
			catch (err1)  {
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
		TMX_oRequest.send(oRequest.GetSendString(TMX_sPrefix));
	}
	return true;
}

// add a request to the queue
function TMX_Send(sURL, PHPCallback, aArgs, aValues) {
	oRequest = TMX_CreateRequest();
	if(oRequest) {
		TMX_aRequest.push(new TMX_Request(oRequest, sURL, PHPCallback, aArgs, aValues));
		return TMX_Dispatch();
	}
	else {
		TMX_Error("Your browser does not support XMLHTTP.");
		return false;
	}
}

// callback function when a request-state changes
function TMX_StateChange() {
	if(this != null && this == TMX_oRequest && this.readyState == 4) {
		if(this.status == 200) {
			++TMX_nReceiveIndex;
    		TMX_ParseResponse(this.responseText);
    	}
  		else {
    		TMX_Error("Problem retrieving data:" + this.statusText);
    	}
    	TMX_oRequest = null;
  		TMX_Dispatch();
  	}
}

function TMX_ProcessCountdown(nIndex) {
	if(nIndex > TMX_nReceiveIndex) {
		TMX_Error("Unable to process the dynamic request, check your connection.");
	}
}