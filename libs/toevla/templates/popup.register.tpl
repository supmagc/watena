{{extends popup.base.tpl}}

{{region begin content}}

{{if true}}
<br /><br />
<h1>Under construction!</h1>
<p>The mail-based login is currently under construction.<br />Please bare with us, as we finalize this feature!</p>
<p><a href="/login">Go back</a></p>
{{else}}

{{if isLogin()}}
<h1>Welcome back!</h1>
<p>Provide us with your password, and enter 'Flanders Is A Festival'!</p>
{{end}}

{{if isRegister()}}
<h1>Register to continue!</h1>
<p>Your email-adress wasn't recognised. Enter a password to register yourself.</p>
{{end}}

<form class="field" action="/login/register" method="post">
	<input type="hidden" name="email" value="{[getEmail()]}" />
	<input type="password" name="pass" />
	<input type="submit" value="" />
</form>

{{if isUnverified()}}
<h1>Unverified user!</h1>
<span class="error">
<p>Your email-adress exists, but is not yet verified.<br />Please check your mailbox if this is you!</p>
</span>
{{end}}

{{if isConnection()}}
<h1>Register to continue!</h1>
<p>Your email-adress wasn't recognised. Enter a password to register yourself.</p>
{{end}}

{{end}}

{{region end}}