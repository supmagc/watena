{{if !isSucces()}}
	<script><!--
	if(window.opener && window.opener.connectIntoFrame) window.opener.connectIntoFrame();
	--></script>
{{end}}

{{if isSucces()}}
	<script><!--
	if(window.opener.parent.connectCallback) {
		window.opener.parent.connectCallback('{[getHash()]}');
	}
	else if(window.parent.connectCallback) {
		window.parent.connectCallback('{[getHash()]}');
	}
	--></script>
{{end}}

{{if isFailed()}}
FAILED !
{{end}}

{{if isDuplicateLogin()}}
You are allready logged in with a different account !
{{end}}

{{if isDuplicateEmail()}}
Duplicate email ... maybe you allready have an account ?
{{end}}

{{if isDuplicateName() | isInvalidName()}}
This name is allready in use or invalid
<form action="?action=rename" method="post">
	<input type="text" name="name" value="{[getSaveName()]}" />
	<input type="submit" name="sub" value="Rename" />
</form>
{{end}}

{{if isUnverifiedEmail()}}
An account with this email-adress exists, but can't be merched since it's not activated yet!
If this is you, check your inbox for a verificationmail.
{{end}}