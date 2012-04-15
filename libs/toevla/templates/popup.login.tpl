{{if hasFacebookLogin()}}
	<a href="javascript:window.parent.connectPopup('{[getFacebookLoginUrl()]}');"><img src="/theme/toevla/fb.png" /></a><br />	
{{end}}
{{if hasTwitterLogin()}}
	<a href="javascript:window.parent.connectPopup('{[getTwitterLoginUrl()]}');">Login by Twitter</a><br />
{{end}}
<a href="javascript:window.parent.connectCancel();">Cancel</a>