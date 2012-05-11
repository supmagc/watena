function GetUnity() {
	if (typeof unityObject != "undefined") {
		return unityObject.getObjectById("unityPlayer");
	}
	return null;
}

function Hide() {
	videoFrame.location = window.url + '/files/toevla/video/blank.html';
	document.getElementById("videoLayer").style.display = 'none';
}

function Video(URL){
	if(URL.length > 0) {
		document.getElementById('videoLayer').style.display = 'block';
		videoFrame.location = URL;
	}
}

function deezerChange(mId) {
	if(mId && (mId.length > 0 || mId > 0)) {
		document.getElementById("dezelicious").style.display = 'block';
		window.frames['dezelicious'].location = 'http://www.deezer.com/nl/plugins/player?autoplay=true&playlist=false&width=300&height=69&cover=false&type=playlist&id=' + mId;
	}
	else {
		document.getElementById("dezelicious").style.display = 'none';
		window.frames['dezelicious'].location = window.url + '/files/toevla/video/blank.html';
	}
}

function triggerSocial() {
	document.getElementById('connectLayer').style.display = 'block';
	connectFrame.location = window.url + '/social';	
	window.cancelCallback = window.socialCallback;
	console.log('Social triggered !');
}

function socialCallback(sHash, sName) {
	document.getElementById('connectLayer').style.display = 'none';
	GetUnity().SendMessage('Persistent', 'InjectSocial', '');
	connectFrame.location = window.url + '/loading?hash=' + sHash + '&name=' + sName;	
}

function requestHash(sRequestHashType) {
	if(sRequestHashType == "HIDDEN" || (sRequestHashType == "AUTOMATIC" && window.tvHash.length > 0))
		GetUnity().SendMessage('Persistent', 'InjectHash', window.tvHash);		
	else {
		document.getElementById('connectLayer').style.display = 'block';
		connectFrame.location = window.url + '/login';	
	}
	window.tvHash = '';
	window.cancelCallback = window.hashCallback;
	console.log('Hash requested !');
}

function hashCallback(sHash) {
	document.getElementById('connectLayer').style.display = 'none';
	GetUnity().SendMessage('Persistent', 'InjectHash', '' + sHash);
	if(window.tvLoginPopup) {
		window.tvLoginPopup.close();
		window.tvLoginPopup = undefined;
	}
}

function connectCancel() {
	if(window.cancelCallback)
		window.cancelCallback();
}

function connectPopup(sUrl) {
	if(window.tvLoginPopup) {
		window.tvLoginPopup.close();
	}
	connectFrame.location = window.url + '/loading';	
	var nLeft = (screen.width - 950) / 2;
	var nTop = (screen.height - 600) / 2;
	window.tvLoginPopup = window.open(sUrl, 'Login', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=950,height=600,left='+nLeft+',top='+nTop);
	//window.tvLoginPopup.blur();
	//window.focus();
	setTimeout('if(window.tvLoginPopup) window.tvLoginPopup.focus();', 3000);
}

function toFrame() {
	if(window.tvLoginPopup) {
		document.getElementById("loginLayer").style.display = 'block';
		window.frames['connectFrame'].location = window.tvLoginPopup.location;
		window.tvLoginPopup.close();
		window.tvLoginPopup = undefined;
	}
}
