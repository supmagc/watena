{{extends popup.base.tpl}}

{{region begin content}}
<div id="fb-root" style="width:150px;"></div>
<script>
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/nl_NL/all.js#xfbml=1&appId=121283004662650";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function postToFacebook() {
	var obj = {
		method: 'feed',
		display: 'popup',
		link: '{[getUrl]}',
		picture: '{[getUrl]}/theme/toevla/icon.png',
		name: 'Flanders Is A Festival',
		caption: 'What are you doing?',
		description: 'Talk about your \'Flanders Is A Festival\' experience!',
		actions: {
			name: 'Have a look',
			link: '{[getUrl()]}'
		}
	};

    FB.ui(obj, function callback(response) {
    	if(response.post_id)
    		window.parent.socialCallback();
    });
}

window.twttr = (function(d,s,id) {
	var t, js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
	js.src="//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
	return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
}(document, "script", "twitter-wjs"));

twttr.ready(function(twttr) {
	twttr.events.bind('tweet', function(event) {
		window.parent.socialCallback();
	});
});
</script>
<div id="social">
	<p><a href="javascript:postToFacebook();">Post to Facebook</a></p>
	<p><a href="https://twitter.com/intent/tweet?{[getTwitterParams()]}">Post to Twitter</a></p>
	<!--<div class="fb-like" data-href="http://flandersisafestival.com" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false" data-font="arial"></div>-->
</div>
{{region end}}