{{extends popup.base.tpl}}

{{region begin content}}
<div id="login">
	{{if hasFacebookLogin()}}
		<a id="btn_facebook" href="javascript:window.parent.connectPopup('{[getFacebookLoginUrl()]}');"></a>
	{{end}}
	{{if hasTwitterLogin()}}
		<a id="btn_twitter" href="javascript:window.parent.connectPopup('{[getTwitterLoginUrl()]}');"></a>
	{{end}}
	<!--
	<form class="field" action="/login/register" method="post">
		<input type="text" onfocus="if(this.value=='Your email') this.value='';" onblur="if(this.value=='') this.value='Your email';" name="email" value="Your email" />
		<input type="submit" value="" />
	</form>
	-->
</div>
{{region end}}