<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="{[getContentType()]}; charset={[getCharset()]}" />
<title>{[getTitle()]}</title>
<link href="/theme/toevla/css/style.css" rel="stylesheet" type="text/css" />
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
<script src="http://platform.twitter.com/anywhere.js?id=VfipE4kEsDwA6ljKWzKG5A&v=1" type="text/javascript"></script>
<script type="text/javascript">
	twttr.anywhere(function (T) {
		T("#login").connectButton();
	});
</script>
{{if hasHash()}}
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
{{end}}
</head>

<body bgcolor="f4f3ee">
<div id="fb-root"></div>
<table id="centerTable">
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td></td>
		<td id="centerCell"><div id="centerDiv">
			{{if hasHash()}}
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
				<div class="fb-login-button" scope="email">Login with Facebook</div>
				<span id="login"></span><br />
				<a href="{{getTwitterLoginUrl()}}">Login by Google</a><br />
				<a href="">Login by Mail</a>
			{{end}}
		</div></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
</table>
</body>
</html>
