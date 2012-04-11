<script type="text/javascript" src="http://webplayer.unity3d.com/download_webplayer-3.x/3.0/uo/UnityObject.js"></script>
<script type="text/javascript"><!--
window.tvHash = '{[getHash()]}';
function GetUnity() {
	if (typeof unityObject != "undefined") {
		return unityObject.getObjectById("unityPlayer");
	}
	return null;
}
if (typeof unityObject != "undefined") {
	var params = {
			disableContextMenu: true,
			backgroundcolor: "FFFFFF",
			bordercolor: "FFFFFF",
			textcolor: "000000",
			logoimage: "{[getUrl()]}/theme/toevla/loadunity.png"
	};
	unityObject.embedUnity("unityPlayer", "{[getUrl()]}/files/toevla/unity/WebPlayer.unity3d", 728, 450, params);
	
}
function Hide() {
	video.location='{[getUrl()]}/files/toevla/video/blank.html';
	document.getElementById("youtubePlayer").style.display = 'none';
}
function Video(URL){
	document.getElementById("youtubePlayer").style.display = 'block';
	video.location=URL;
}
function login() {
	document.getElementById("loginLayer").style.display = 'block';
}
function loginPopup(sUrl) {
	if(window.tvLoginPopup) {
		window.tvLoginPopup.close();
	}
	var nLeft = (screen.width - 950) / 2;
	var nTop = (screen.height - 600) / 2;
	window.tvLoginPopup = window.open(sUrl, 'Login', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=800,height=600,left='+nLeft+',top='+nTop);
	window.tvLoginPopup.focus();
}
function loginPopupCallback(sHash) {
	document.getElementById("loginLayer").style.display = 'none';
	window.tvHash = sHash;
	if(window.tvLoginPopup) {
		window.tvLoginPopup.close();
		window.tvLoginPopup = undefined;
	}	
}
--></script>
<div id="unityPlayer"></div>

<div id="youtubePlayer" style="display: none;">
	<iframe width="150" height="120" name="video" src="/files/toevla/video/blank.html" frameborder="0" allowfullscreen></iframe>
</div>

<div id="loginLayer" style="display: none;">
{{if hasFacebookLogin()}}
	<a href="javascript:loginPopup('{[getFacebookLoginUrl()]}');"><img src="/theme/toevla/fb.png" /></a><br />	
{{end}}
{{if hastwitterLogin()}}
	<a href="javascript:loginPopup('{[getTwitterLoginUrl()]}');">Login by Twitter</a><br />
{{end}}
</div>
