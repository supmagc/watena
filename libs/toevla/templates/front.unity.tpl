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
function login(sName) {
}
--></script>
<div id="unityPlayer"></div>

<div id="youtubePlayer" style="display: none;">
	<iframe width="150" height="120" src="/files/toevla/video/blank.html" frameborder="0" allowfullscreen></iframe>
</div>

<div id="loginLayer" style="display: none;">
{{if hasFacebookLogin()}}
	<a href="{[getFacebookLoginUrl()]}">Login by Facebook</a><br />	
{{end}}
{{if hastwitterLogin()}}
	<a href="{[getTwitterLoginUrl()]}">Login by Twitter</a><br />
{{end}}
</div>
