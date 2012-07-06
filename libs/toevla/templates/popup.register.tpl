{{extends popup.base.tpl}}

{{region begin content}}

<h1>{[getHeader()]}</h1>

{{if getShowSucces()}}
<span class="succes">
<p>{[getText()]}</p>
</span>
{{elseif getShowError()}}
<span class="error">
<p>{[getText()]}</p>
</span>
{{else}}
<p>{[getText()]}</p>
{{end}}

{{if getShowEmail()}}
<form class="field" action="/login/register" method="post">
	<input type="text" name="email" value="{[getEmail()]}" />
	<input type="submit" value="" />
</form>
{{end}}

{{if getShowPass()}}
<form class="field" action="/login/register" method="post">
	<input type="hidden" name="email" value="{[getEmail()]}" />
	<input type="password" name="pass" />
	<input type="submit" value="" />
</form>	
{{end}}

{{if getHash()}}
<script><!--
if(window.parent.hashCallback)
	window.parent.hashCallback('{[getHash()]}');
--></script>
{{end}}

{{region end}}