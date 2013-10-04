var AJAX = (function() {
	
	this.m_aRequest = new Array();
	this.m_oRequest = null;
	this.m_sPrefix = 'AJAX';
	
	this.error = function(sMessage) {
		alert(sMessage);
	};
	
	this.request = function(oRequest, sUrl, sPhpCallback, aArgs, sValues) {
		this.m_oRequest = oRequest;
		this.m_sUrl = sUrl;
		this.m_sPhpCallback = sPhpCallback;
		this.m_aArgs = aArgs;
		this.m_sValues = sValues;
		
		this.getSendString = function() {
			var sTmp = 'args='+encodeURIComponent(JSON.stringify(this.m_aArgs));
			sTmp += '&callback='+this.m_sPhpCallback;
			sTmp += '&values='+this.m_sValues;
			return sTmp;
		};
	};


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
			m_oRequest.open("POST", decodeURIComponent(oRequest.m_sUrl), true);
			m_oRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			//m_oRequest.setRequestHeader("Content-length", oRequest.getParamCount());
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
	    		parseResponse(this.responseText);
	    	}
	  		else {
	    		error("Problem retrieving data:" + this.statusText);
	    	}
	    	m_oRequest = null;
	  		dispatch();
	  	}
	};

	// parse the response
	this.parseResponse = function(sResponse) {
		if(parseResponseCode(sResponse)) {
			var oReg = new RegExp(/<script(.*?)>(.*?)<\/script>/);
			var aMatches = "";
			while((aMatches = oReg.exec(sResponse)) != null) {
				var fileref = null;
				var src="";
				var attributes = "";
				
				if(aMatches[1]) {
					attributes = aMatches[1];
					if((src = (attributes.split(/src=\"(.*?)\"/))) != attributes) {;
						fileref = document.createElement('script');
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
	};

	// parse the response-code for client-sided awareness when a server-error occured
	this.parseResponseCode = function(sResponse) {
		if(sResponse.substring(0, 1).length == 1) {
			nCode = parseInt(sResponse.substring(0, 1));
			if(nCode == 1) {return true;}
			if(nCode == 2) error('Serversided PHP error: ' + sResponse.substring(1));
			if(nCode == 3) error('Problem with POST-values: ' + sResponse.substring(1));
			if(nCode == 4) error('Problem setting AJAX-Response object: ' + sResponse.substring(1));
		}
		error('No valid AJAX-response-code found: ' + sResponse);
		return false;
	};

	// create an element reference to load extrenal files in
	this.getExternalFileElementTag = function(sFile, bJS) {
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
	};

	// Add a external file
	this.addExternalFile = function(sNewFile, bJS) {
		var aList = document.getElementsByTagName('head').item(0).appendChild(AJAX_GetExternalFileElementTag(sNewFile, bJS));
	};

	// Replace a loaded external file
	this.replaceExternalFile = function(sOldFile, sNewFile, bJS) {
		var aList = document.getElementsByTagName(bJS ? 'script' : 'link');
		for(var i=aList.length; i>=0; --i) {
			if(aList[i] && aList[i].getAttribute(bJS ? 'src' : 'href') != null && aList[i].getAttribute(bJS ? 'src' : 'href').indexOf(sOldFile)!=-1) {
				aList[i].parentNode.replaceChild(AJAX_GetExternalFileElementTag(sNewFile, bJS), aList[i]);
	 		}
		}
	};

	// Remove a loaded external file
	this.removeExternalFile = function(sOldFile, bJS) {
		var aList = document.getElementsByTagName(bJS ? 'script' : 'link');
		for(var i=aList.length; i>=0; --i) {
			if(aList[i] && aList[i].getAttribute(bJS ? 'src' : 'href') != null && aList[i].getAttribute(bJS ? 'src' : 'href').indexOf(sOldFile)!=-1) {
				aList[i].parentNode.removeChild(aList[i]);
	 		}
		}
	};
	
	return function(sUrl, sPhpCallback, aArgs, sValues) {
		console.log(request);
		var oRequest = createRequest();
		if(oRequest) {
			m_aRequest.push(new request(oRequest, sUrl, sPhpCallback, aArgs, sValues));
			return dispatch();
		}
		else {
			error("Your browser does not support XMLHTTP.");
			return false;
		}		
	};
})();