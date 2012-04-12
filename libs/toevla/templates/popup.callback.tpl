{{if isSucces()}}
	{{if getHash()}}
	<script>
	window.opener.parent.connectPopupCallback('{[getHash()]}');
	</script>
	{{end}}
{{end}}

{{if isFailed()}}
FAILED !
{{end}}

{{if isDuplicateEmail()}}
Duplicate email ... maybe you allready have an account ?
{{end}}

{{if isDuplicateConnection()}}
You are allready logged in with a different account !
{{end}}

{{if isDuplicateName()}}
This name is allready in use ...
{[getError()]}
<form action="?action=rename" method="post">
	<input type="text" name="name" value="{[getSaveName()]}" />
	<input type="submit" name="sub" value="Rename" />
</form>
{{end}}
