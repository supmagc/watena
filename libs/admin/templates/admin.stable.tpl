<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="{[getContentType()]}; charset={[getCharset()]}">
	<meta http-equiv="Description" content="{[getDescription()]}">
	<meta http-equiv="Keywords" content="{[getKeywords()]}">
	<title>Watena - {[getTitle()]}</title>
	<link href="/theme/admin/css/admin.main.css" rel="stylesheet" type="text/css" media="all" />
	<link href="/theme/admin/css/admin.overlay.css" rel="stylesheet" type="text/css" media="all" />
	<link href="/theme/admin/jqueryui/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css" media="all" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600|Droid+Sans:400,700' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,300,500|Exo+2:200,400,200italic,400italic' rel='stylesheet' type='text/css'>
	{{call addJavascriptLink(url('/theme/admin/js/jquery-1.10.2.min.js'))}}
	{{call addJavascriptLink(url('/theme/admin/jqueryui/jquery-ui-1.10.4.custom.min.js'))}}
	{{call addJavascriptLink(url('/theme/default/js/stable.js'))}}
	{{call addJavascriptLink(url('/theme/default/js/ajax.js'))}}
	{{call addJavascriptLink(url('/theme/admin/js/watena-admin.js'))}}
	{[getJavascriptLoader('loaderCallback')]}
	{[getAjax()]}
</head>
<body>
	<div id="nav">
		<div id="nav-logo">Watena</div>
		<!--<div class="nav-category">
			<span class="title"></span>
			<ul class="nav-list">
				<li class="nav-item"></li>
			</ul>
		</div>-->
		
		<div id="nav-logout" class="clickable">
		</div>
		
		<div id="nav-search">
			<form>
				<input id="search_txt" name="search_txt" type="text" value="Search Watena" />
				<input id="search_sub" name="search_sub" type="submit" value="" />
			</form>
		</div>
	</div>
	<div id="row"></div>
	<div id="main">
		<div id="main-left">
			<div id="main-tabs">
				<div class="title" id="tabs-title"></div>
				<ul id="tabs-list">
					<!--<li class="tabs-item"></li>-->
				</ul>
			</div>
			<div id="main-module">
				<div class="title">Module-Info</div>
				<u>Name</u>: <span id="module-name"></span><br />
				<u>Version</u>: <span id="module-version"></span><br />
				<br />
				<span id="module-description"></span>
			</div>
			<div id="main-copy">2013 &copy; Voet Jelle</div>
		</div>
		<div id="main-right">
			<div id="main-content">
				<div class="title" id="content-title"></div>
				<div id="content-content">
				</div>
			</div>
		</div>
	</div>
	{{include admin.overlay.login.tpl}}
	{{include admin.overlay.error.tpl}}
	{{include admin.overlay.succes.tpl}}
	{{include admin.overlay.info.tpl}}
	{{include admin.overlay.loading.tpl}}
	{{include admin.overlay.confirm.tpl}}
</body>
</html>