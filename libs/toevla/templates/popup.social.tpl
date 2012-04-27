{{extends popup.base.tpl}}

{{region begin content}}
<div id="fb-root" style="width:150px;"></div>
<script>
window.fbAsyncInit = function() {
	FB.init({
      appId      : '121283004662650',
      cookie     : true, // enable cookies to allow the server to access the session
    });
};

(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
}(document));

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
	<p><a href="https://twitter.com/intent/tweet">Post to Twitter</a></p>
</div>
{{region end}}