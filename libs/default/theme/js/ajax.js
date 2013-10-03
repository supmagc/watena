
var AJAX = (function() {
	var m_aRequest = new Array();
	
	var error = function(sMessage) {
		alert(sMessage);
	};
	
	var request = function(oRequest, sUrl, PhpCallback, aArgs, aValues) {
		this.m_oRequest = oRequest;
		this.m_sUrl = sUrl;
		this.m_PhpCallback = PhpCallback;
		this.m_aArgs = aArgs;
		this.m_aValues = aValues;
		
		this.getSendString = function(sPrefix) {
			var sTmp = 'callback='+this.m_PHPCallback+'&';
	
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
		};
		
		this.getParamCount = function() {
			return 3 + this.m_aArgs.length + (2 * this.m_aValues.length);
		};
	};


	var createRequest = function() {
	    try {
	    	return new ActiveXObject('Msxml2.XMLHTTP');
	    }
	    catch (err2) {
	         try {
	        	 return new ActiveXObject('Microsoft.XMLHTTP');
	         }
	         catch (err3) {
				try {
					return new XMLHttpRequest();
				}
				catch (err1)  {
					return undefined;
				}
	    	}
	    }
	};

	// if a request exists is queued and at the current moment no other request is processed, call the next one
	var dispatch = function() {
		if(AJAX_oRequest == null && AJAX_aRequest.length > 0) {
			setTimeout("AJAX_ProcessCountdown(" + (++AJAX_nSendIndex) + ");", 30000);
			var oRequest = AJAX_aRequest.shift();
			AJAX_oRequest = oRequest.m_oRequest;
			AJAX_oRequest.open("POST", oRequest.m_sURL, true);
			AJAX_oRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			AJAX_oRequest.setRequestHeader("Content-length", oRequest.GetParamCount());
			AJAX_oRequest.setRequestHeader("Connection", "close");
			AJAX_oRequest.onreadystatechange = AJAX_StateChange;
			AJAX_oRequest.send(oRequest.GetSendString(AJAX_sPrefix));
		}
		return true;
	};
	
	return {send: function(sUrl, sPhpCallback, aArgs, aValues) {
		var oRequest = createRequest();
		if(oRequest) {
			m_aRequest.push(new request(oRequest, sUrl, sPhpCallback, aArgs, aValues));
			return dispatch();
		}
		else {
			error("Your browser does not support XMLHTTP.");
			return false;
		}		
	}};
})();

var AJAX_aRequest = new Array();
var AJAX_oRequest = null;
var AJAX_sPrefix = 'AJAX';
var AJAX_nSendIndex = 0;
var AJAX_nReceiveIndex = 0;

// debug-function
function AJAX_Error(sMessage) {
	alert(sMessage);
}

// set the prefix needed for the POST-data
function AJAX_SetPrefix(sPrefix) {
	AJAX_sPrefix = sPrefix;
}

// parse the res�nse-code for client-sided awareness when a server-error occured
function AJAX_ParseResponseCode(sResponse) {
	if(sResponse.substring(0, 1).length == 1) {
		nCode = parseInt(sResponse.substring(0, 1));
		if(nCode == 1) {return true;}
		if(nCode == 2) AJAX_Error('Serversided PHP error: ' + sResponse.substring(1));
		if(nCode == 3) AJAX_Error('Problem with POST-values: ' + sResponse.substring(1));
		if(nCode == 4) AJAX_Error('Problem setting AJAX-Response object: ' + sResponse.substring(1));
	}
	AJAX_Error('No valid AJAX-response-code found: ' + sResponse);
	return false;
}

// parse the response
function AJAX_ParseResponse(sResponse) {
	if(AJAX_ParseResponseCode(sResponse)) {
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
function AJAX_GetExternalFileElementTag(sFile, bJS) {
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
function AJAX_AddExternalFile(sNewFile, bJS) {
	var aList = document.getElementsByTagName('head').item(0).appendChild(AJAX_GetExternalFileElementTag(sNewFile, bJS));
}

// Replace a loaded external file
function AJAX_ReplaceExternalFile(sOldFile, sNewFile, bJS) {
	var aList = document.getElementsByTagName(bJS ? 'script' : 'link');
	for(var i=aList.length; i>=0; --i) {
		if(aList[i] && aList[i].getAttribute(bJS ? 'src' : 'href') != null && aList[i].getAttribute(bJS ? 'src' : 'href').indexOf(sOldFile)!=-1) {
			aList[i].parentNode.replaceChild(AJAX_GetExternalFileElementTag(sNewFile, bJS), aList[i]);
 		}
	}
}

// Remove a loaded external file
function AJAX_RemoveExternalFile(sOldFile, bJS) {
	var aList = document.getElementsByTagName(bJS ? 'script' : 'link');
	for(var i=aList.length; i>=0; --i) {
		if(aList[i] && aList[i].getAttribute(bJS ? 'src' : 'href') != null && aList[i].getAttribute(bJS ? 'src' : 'href').indexOf(sOldFile)!=-1) {
			aList[i].parentNode.removeChild(aList[i]);
 		}
	}
}

// callback function when a request-state changes
function AJAX_StateChange() {
	if(this != null && this == AJAX_oRequest && this.readyState == 4) {
		if(this.status == 200) {
			++AJAX_nReceiveIndex;
    		AJAX_ParseResponse(this.responseText);
    	}
  		else {
    		AJAX_Error("Problem retrieving data:" + this.statusText);
    	}
    	AJAX_oRequest = null;
  		AJAX_Dispatch();
  	}
}

function AJAX_ProcessCountdown(nIndex) {
	if(nIndex > AJAX_nReceiveIndex) {
		AJAX_Error("Unable to process the dynamic request, check your connection.");
	}
}