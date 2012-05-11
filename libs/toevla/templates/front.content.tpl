{{extends front.base.tpl}}

{{region begin content}}
<div id="page">
<div id="header_content"><div class="container_content">{{region use nav}}</div></div>
<div id="body_content_header"></div>
<div id="body_content">
	<div id="body_content_shadow">
		<div class="container_content">
			{{if getLocal() == '/about'}}
				{{include content.about.tpl}}
			{{end}}
			{{if getLocal() == '/privacy'}}
				{{include content.privacy.tpl}}
			{{end}}
			{{if getLocal() == '/tos'}}
				{{include content.tos.tpl}}
			{{end}}
		</div>
	</div>
</div>
<div id="body_content_footer"></div>
<div id="footer"><div class="container_content">{{region use copy}}</div></div>
</div>
{{region end content}}

