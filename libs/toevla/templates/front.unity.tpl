<script type="text/javascript" src="/theme/toevla/js/unity.js"></script>
<script type="text/javascript" src="/theme/toevla/js/logic.js"></script>
<script type="text/javascript"><!--
window.url = "{[getUrl()]}";
window.tvHub = '{[getHubId()]}';
window.tvHash = '{[getHash()]}';
window.tvFestival = '{[getFestivalId()]}';
window.cancelCallback = undefined;

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
--></script>
<div id="connectLayer" style="display: none;">
	<iframe name="connectFrame" src="/loading" frameborder="0" scrolling="0"></iframe>
</div>

<div id="videoLayer" style="display: none;">
	<iframe name="videoFrame" src="/files/toevla/video/blank.html" frameborder="0" scrolling="0"></iframe>
</div>

<div id="unityPlayer"></div>

