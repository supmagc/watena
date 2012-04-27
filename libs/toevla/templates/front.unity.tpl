<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://webplayer.unity3d.com/download_webplayer-3.x/3.0/uo/UnityObject.js"></script>
<script type="text/javascript"><!--
function GetUnity() {
	if (typeof unityObject != "undefined") {
		return unityObject.getObjectById("unityPlayer");
	}
	return null;
}
if(typeof unityObject != "undefined") {
	var params = {
			wmode: "opaque",
			disableContextMenu: true,
			backgroundcolor: "FFFFFF",
			bordercolor: "FFFFFF",
			textcolor: "000000",
			logoimage: "{[getUrl()]}/files/toevla/unity/loadunity.png"
	};
	unityObject.embedUnity("unityPlayer", "{[getUrl()]}/files/toevla/unity/{[getConfig('unity', 'WebPlayer.unity3d')]}", 728, 450, params);
}



function Hide() {
	videoFrame.location = '{[getUrl()]}/files/toevla/video/blank.html';
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
		window.frames['dezelicious'].location = '{[getUrl()]}/files/toevla/video/blank.html';
	}
}



window.tvHash = '{[getHash()]}';
window.cancelCallback;
function triggerSocial() {
	document.getElementById('connectLayer').style.display = 'block';
	connectFrame.location = '{[getUrl()]}/social';	
	window.cancelCallback = window.socialCallback;
	console.log('Social triggered !');
}
function socialCallback() {
	document.getElementById('connectLayer').style.display = 'none';
	GetUnity().SendMessage('Persistent', 'InjectSocial', '');
	connectFrame.location = '{[getUrl()]}/loading';	
}
function requestHash(sRequestHashType) {
	if(sRequestHashType == "HIDDEN" || (sRequestHashType == "AUTOMATIC" && window.tvHash.length > 0))
		GetUnity().SendMessage('Persistent', 'InjectHash', window.tvHash);		
	else {
		document.getElementById('connectLayer').style.display = 'block';
		connectFrame.location = '{[getUrl()]}/login';	
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
//	$.ajax({
//		url: sUrl,
//		data: null,
//		type: 'GET',
//		statusCode: {
//			301: function(s) {console.log(s);},
//			302: function(s) {console.log(s);},
//			303: function(s) {console.log(s);},
//			304: function(s) {console.log(s);}
//		},
//		error: function(jqXhr, error) {console.log(error);},
//		success: function(data, code, jqXhr) {console.log('success');},
//		complete: function(jqXhr, code) {
//			console.log(jqXhr);
//			if(jqXhr.status == 302) {
//				console.log(jqXhr.getResponseHeader('Location'));
//			}
//		}
//	});
	connectFrame.location = '{[getUrl()]}/loading';	
	var nLeft = (screen.width - 950) / 2;
	var nTop = (screen.height - 600) / 2;
	window.tvLoginPopup = window.open(sUrl, 'Login', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=950,height=600,left='+nLeft+',top='+nTop);
	window.tvLoginPopup.blur();
	window.focus();
	setTimeout('if(window.tvLoginPopup) window.tvLoginPopup.focus();', 3000)
}
function toFrame() {
	if(window.tvLoginPopup) {
		document.getElementById("loginLayer").style.display = 'block';
		window.frames['connectFrame'].location = window.tvLoginPopup.location;
		window.tvLoginPopup.close();
		window.tvLoginPopup = undefined;
	}
}
--></script>
<div id="connectLayer" style="display: none;">
	<iframe name="connectFrame" src="/loading" frameborder="0" scrolling="0"></iframe>
</div>

<div id="videoLayer" style="display: none;">
	<iframe name="videoFrame" src="/files/toevla/video/blank.html" frameborder="0" scrolling="0"></iframe>
</div>

<div id="unityPlayer"></div>

