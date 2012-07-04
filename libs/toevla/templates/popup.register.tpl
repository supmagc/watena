{{extends popup.base.tpl}}

{{region begin content}}

{{if isInvalidEmail()}}
<h1>Hello!</h1>
<p>The email-adress you provided isnot valid. Please, try something else!</p>
<form class="field" action="/login/register" method="post">
	<input type="text" name="email" value="{[getEmail()]}" />
	<input type="submit" value="" />
</form>

{{elseif isUnverifiedEmail()}}
<h1>Unverified email!</h1>
<span class="error">
<p>Your email-adress exists, but is not yet verified.<br />Please check your mailbox if this is you!</p>
</span>

{{elseif isRegisterDone()}}
<h1>Registration done!</h1>
<span class="succes">
<p>You are registered, but your email is not yet verified.<br />Please check your mailbox if this is you!</p>
</span>

{{else}}
	
	{{if isLogin()}}
	<h1>Welcome back!</h1>
	<p>Provide us with your password, and enter 'Flanders Is A Festival'!</p>
	
	{{elseif isRegister()}}
	<h1>Register to continue!</h1>
	<p>Choose a valid password you can remember to continue!</p>
	
	{{elseif isInvalidPassword()}}
	<h1>Invalid password!</h1>
	<p>The password you entered is not valid. Try again!</p>
	{{end}}
	
	<form class="field" action="/login/register" method="post">
		<input type="hidden" name="email" value="{[getEmail()]}" />
		<input type="password" name="pass" />
		<input type="submit" value="" />
	</form>	

{{end}}

{{if isDone()}}
<script><!--
if(window.opener.parent.hashCallback)
	window.opener.parent.hashCallback('{[getHash()]}');
else if(window.parent.hashCallback)
	window.parent.hashCallback('{[getHash()]}');
--></script>
{{end}}

{{region end}}