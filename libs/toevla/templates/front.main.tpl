{{extends front.base.tpl}}

{{region begin header}}
{{if getHash()}}
	<script type="text/javascript" src="http://webplayer.unity3d.com/download_webplayer-3.x/3.0/uo/UnityObject.js"></script>
	<script type="text/javascript">
	<!--
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
				logoimage: "loadfestival.png"
		};
		unityObject.embedUnity("unityPlayer", "WebPlayer.unity3d", 728, 450, params);
		
	}
	-->
	</script>
{{else}}
	<script>
		window.fbAsyncInit = function() {
			FB.init({
				appId      : '121283004662650',
				status     : true, 
				cookie     : true,
				xfbml      : true,
				oauth      : true,
			});
			FB.Event.subscribe('auth.response', function(response) {
				window.location.reload();
			});
		};
	    (function(d){
			var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
			js = d.createElement('script'); js.id = id; js.async = true;
			js.src = "//connect.facebook.net/en_US/all.js";
			d.getElementsByTagName('head')[0].appendChild(js);
		}(document));
	</script>
{{end}}
{{region end}}

{{region begin content}}
{{if getHash()}}
	<div id="unityPlayer"></div>
	<div id="youtubePlayer">					
		<iframe width="150" height="120" src="http://www.youtube-nocookie.com/embed/jJkSmErWc20?version=3&feature=player_embedded&autoplay=1&controls=0&rel=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
		<!--
		<object style="height: 120px; width: 150px">
		<param name="movie" value="https://www.youtube.com/v/jJkSmErWc20?version=3&feature=player_embedded&autoplay=1&controls=0&rel=0&showinfo=0">
		<param name="allowFullScreen" value="true">
		<param name="allowScriptAccess" value="always">
		<embed src="https://www.youtube.com/v/jJkSmErWc20?version=3&feature=player_embedded&autoplay=1&controls=0&rel=0&showinfo=0&loop=1" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="150" height="120"></object>
		-->
	</div>
{{else}}
	<a href="{[getFacebookLoginUrl()]}">Login by Facebook</a><br />	
	<a href="{[getTwitterLoginUrl()]}">Login by Twitter</a><br />
	<a href="">Login by Mail</a>
{{end}}
{{region end}}
