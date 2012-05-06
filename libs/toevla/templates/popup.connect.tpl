{{extends popup.base.tpl}}

{{region begin content}}

{{if isSucces()}}
	<script><!--
	if(window.opener.parent.hashCallback)
		window.opener.parent.hashCallback('{[getHash()]}');
	else if(window.parent.hashCallback)
		window.parent.hashCallback('{[getHash()]}');
	--></script>
{{else}}
	<script><!--
	if(window.opener && window.opener.connectIntoFrame) window.opener.toFrame();
	--></script>
	<br /><br />
	<h1>Oeps, an error ...</h1>
	<span class="error">
		{{if isFailed()}}
		<p>An error occured, but couldn't be determined!</p>
		<p>If this problem continues,<br />please contact us at info@flandersisafestival.com.</p>
		{{end}}
		
		{{if isDuplicateLogin()}}
		<p>You are allready logged in with a different account!</p>
		{{end}}
		
		{{if isDuplicateEmail()}}
		<p>Duplicate email-adress.<br />Maybe you allready have an account?</p>
		{{end}}
		
		{{if isInvalidEmail()}}
		<p>Invalid email-adress!</p>
		{{end}}
		
		{{if isDuplicateName() | isInvalidName()}}
		<p>This name is allready in use or invalid!<br />You may try another name!</p>
		<form class="field" action="?action=rename" method="post">
			<input type="text" value="{[getSaveName()]}" name="name" />
			<input type="submit" name="sub" value="" />
		</form>
		{{end}}
		
		{{if isUnverifiedEmail()}}
		<p>An account with this email-adress exists,<br />but can't be merched since it's not activated yet!</p>
		<p>If this is you, check your inbox for a verificationmail.</p>
		{{end}}
	</span>
{{end}}

{{region end}}