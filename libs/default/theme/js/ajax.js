var AJAX = (function() {
	
	this.m_aRequest = new Array();
	this.m_oRequest = null;
	
	// Show error
	this.error = function(sMessage, sContent, bDebug) {
		if(true || bDebug) {
			console.log(sMessage);
			if(sContent.length > 0) {
			    oWin = window.open('', '', 'width=800,height=400,location=0,resizeable=1,scrollbars=1');
			    oWin.document.write('<title>'+sMessage+'</title>'+sContent);
			    oWin.document.close();
			}
		}
	};
	
	// The customised ajax object
	this.request = function(oRequest, sUrl, sMethod, aArguments, bDebug) {
		this.m_oRequest = oRequest;
		this.m_sUrl = sUrl;
		this.m_bDebug = bDebug;
		this.m_sMethod = sMethod;
		this.m_aArguments = aArguments;
		
		this.getSendString = function() {
			var sTmp = 'arguments='+encodeURIComponent(JSON.stringify(this.m_aArguments));
			sTmp += '&method='+this.m_sMethod;
			return sTmp;
		};
	};

	// Create the request browser object
	this.createRequest = function() {
	    try {
	    	return new ActiveXObject('Msxml2.XMLHTTP');
	    }
	    catch(err2) {
	         try {
	        	 return new ActiveXObject('Microsoft.XMLHTTP');
	         }
	         catch(err3) {
				try {
					return new XMLHttpRequest();
				}
				catch(err1)  {
					return undefined;
				}
	    	}
	    }
	};

	// if a request exists is queued and at the current moment no other request is processed, call the next one
	this.dispatch = function() {
		if(m_oRequest == null && m_aRequest.length > 0) {
			var oRequest = m_aRequest.shift();
			m_oRequest = oRequest.m_oRequest;
			m_oRequest.debug = oRequest.m_bDebug;
			m_oRequest.open("POST", decodeURIComponent(oRequest.m_sUrl), true);
			m_oRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			m_oRequest.setRequestHeader("Connection", "close");
			m_oRequest.onreadystatechange = this.stateChange;
			m_oRequest.send(oRequest.getSendString());
		}
		return true;
	};

	// callback function when a request-state changes
	this.stateChange = function() {
		if(this != null && this == m_oRequest && this.readyState == 4) {
			if(this.status == 200) {
				try {
					oData = JSON.parse(this.responseText);
					if(oData.code != undefined) {
						if(oData.code == 1) {
							error("Error when processing the ajax request on the server: (code:" + oData.error_code + ")", oData.error_message, this.debug);
						}
						else if(oData.code == 2) {
							try {
								eval(oData.data);
							}
							catch(err) {
								error("Error evaluating the ajax-response: " + err, oData.data, this.debug);
							}
						}
					}
					else {
						error("Error reading the ajax-response as no return-code could be found.", '', this.debug);
					}
				}
				catch(err) {
					error("Error parsing an ajax-response as json: " + err, this.responseText, this.debug);
				}
	    	}
	  		else {
	    		error("Error retrieving an ajax-response: " + this.statusText, '', this.debug);
	    	}
	    	m_oRequest = null;
	  		dispatch();
	  	}
	};
	
	return function(sUrl, sMethod, aArguments, bDebug) {
		console.log(request);
		var oRequest = createRequest();
		if(oRequest) {
			m_aRequest.push(new request(oRequest, sUrl, sMethod, aArguments, bDebug));
			return dispatch();
		}
		else {
			error("Your browser does not support XMLHTTP.");
			return false;
		}		
	};
})();