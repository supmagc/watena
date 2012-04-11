{{if getHash()}}
<script>
window.opener.loginPopupCallback('{[getHash()]}');
</script>
{{end}}