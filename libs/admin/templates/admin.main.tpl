<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="{[getContentType()]}; charset={[getCharset()]}">
	<meta http-equiv="Description" content="{[getDescription()]}">
	<meta http-equiv="Keywords" content="{[getKeywords()]}">
	<title>Watena - {[getTitle()]}</title>
	<link href="/theme/admin/css/admin.main.css" rel="stylesheet" type="text/css" media="all" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic|Exo+2:400,600,400italic,600italic' rel='stylesheet' type='text/css'>
	{{call addJavascriptLink(url('/theme/admin/js/jquery-1.10.2.min.js'))}}
	{{call addJavascriptLink(url('/theme/default/js/ajax.js'))}}
	{{call addJavascriptLink(url('/theme/admin/js/watena-admin.js'))}}
	{[getJavascriptLoader('loaderCallback')]}
	{[getAjax()]}
</head>
<body>
	<div id="nav">
		<div id="logo">Watena</div>
		{{foreach getCategories()}}
		<div class="nav-item">
			<span class="title">{[index]}</span>
			<ul>
				{{foreach value}}
				<li>{[index]}</li>
				{{end}}
			</ul>
		</div>
		{{end}}
		<div class="nav-item">
			<span class="title">Users</span>
			<ul>
				<li>Users</li>
				<li>Permissions</li>
				<li>Groups</li>
			</ul>
		</div>
		<div class="nav-item">
			<span class="title">Content</span>
			<ul>
				<li>Pages</li>
				<li>Images</li>
				<li>Quotes</li>
				<li>Templates</li>
				<li>Styles</li>
			</ul>
		</div>
		<div class="nav-item">
			<span class="title">API's</span>
			<ul>
				<li>Trakt DVD's</li>
				<li>Test-Realm</li>
			</ul>
		</div>
	</div>
	<div id="row"></div>
	<div id="page">
		<div id="left">
			<div id="tabs"></div>
			<div id="module"></div>
			<div id="copy">2013 &copy; Voet Jelle</div>
		</div>
		<div id="right">
			<div id="content">
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
				a<br />
			</div>
		</div>
	</div>
</body>
</html>