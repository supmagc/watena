<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="{[getContentType()]}; charset={[getCharset()]}">
<meta http-equiv="Description" content="{[getDescription()]}">
<meta http-equiv="Keywords" content="{[getKeywords()]}">
<link href="/theme/admin/css/admin_main.css" rel="stylesheet" type="text/css" media="all" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic|Exo+2:400,600,400italic,600italic' rel='stylesheet' type='text/css'>
{{call addJavascriptLink(url('/theme/admin/js/jquery-1.10.2.min.js'))}}
{{call addJavascriptLink(url('/theme/default/js/ajax.js'))}}
{{call addJavascriptLink(url('/theme/admin/js/watena-admin.js'))}}
{[getJavascriptLoader('loaderCallback')]}
<title>Watena - {[getTitle()]}</title>
{[getAjax()]}
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<div id="Container"><img src="/theme/admin/logo.png" id="LogoImg" />
	<div id="MenuGroup">
		{{var set MENU_OFFSET 93}}
		{{foreach getCategories()}}
		<ul class="MenuItem" style="left:{[MENU_OFFSET]}px;">
			{{var increase MENU_OFFSET 110}}
			<li class="MainBtn">{[index]}</li>
			<li class="SubMenuItem">
				<ul class="SubMenuList">
					{{foreach value}}
					<li class="SubMenuBtn" style="background-color:<[COLOR]>;" ><a href="javascript:PTM_RequestContent('{[getMapping()]}');" onMouseOver="PTM_SetHelp('{[getDescription()]}');" onMmouseOut="PTM_CH();" title="{[getDescription()]}">{[getName()]}</a></li>
					{{end}}
				</ul>
			</li>
		</ul>
		{{end}}
	</div>
	
	<div id="CenterGroup">
		<div id="LayoutGroupA"></div>
		<div id="LayoutGroupB"></div>
		<div id="ContentGroup">
			<table cellpadding="0" cellspacing="0" id="LoadingTable">
				<tr>
					<td align="center" valign="middle"><div id="LoadingField"></div></td>
				</tr>
			</table>
			{{include admin.login.tpl}}			
		</div>
	</div>
	<div id="HelpGroup"><div id="HelpSwitch"><input type="checkbox" checked="checked" onchange="PTM_SetCookie('OverLibHelp', this.checked);" onmouseover="PTM_SetSimpleHelp('Check this box to enable in-place-help.');" onmouseout="PTM_CH();" id="OverLibCheckBox" /></div><div id="HelpContainer">&nbsp;</div>&nbsp;PEToM v2.0 All rights reserved &copy; <a href="http://www.tomo-design.be">ToMo-design</a> - <a href="javascript:PTM_RequestRefresh();" title="Refresh the backend">Refresh</a></div>
</div>
</body>
</html>