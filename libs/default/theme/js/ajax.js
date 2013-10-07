var AJAX = (function() {
	
	this.m_aRequest = new Array();
	this.m_oRequest = null;
	
	// Show error
	this.error = function(sMessage) {
		alert(sMessage);
	};
	
	// The customised ajax object
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
					eval(this.responseText);
				}
				catch(err) {
					error("Error parsing response: " + err + "\n" + this.responseText);
				}
	    	}
	  		else {
	    		error("Problem retrieving data: " + this.statusText);
	    	}
	    	m_oRequest = null;
	  		dispatch();
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