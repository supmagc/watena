<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="{[getContentType()]}; charset={[getCharset()]}">
<meta http-equiv="Description" content="{[getDescription()]}">
<meta http-equiv="Keywords" content="{[getKeywords()]}">
<link href="/theme/admin/css/admin_main.css" rel="stylesheet" type="text/css" media="all" />
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
			
			
			<[BLOCK-LOADING[
			<table cellpadding="0" cellspacing="0" id="LoadingTable">
				<tr>
					<td align="center" valign="middle"><div id="LoadingField"></div></td>
				</tr>
			</table>
			]]>

	
			<[BLOCK-SUCCES[
			<table cellpadding="0" cellspacing="0" id="SuccesTable">
				<tr>
					<td valign="middle" align="center">
						<table cellpadding="0" cellspacing="0" id="RoundedSuccesTable">
							<tr>
								<td class="TopLeft">&nbsp;</td>
								<td class="Top">&nbsp;</td>
								<td class="TopRight"></td>
							</tr>
							<tr>
				
								<td class="Left">&nbsp;</td>
								<td class="Center"><img src="/PXTMSTYLE/succes/Succes.png" style="position:relative; right:5px;" align="right" />
									<div id="RoundedSuccesTableTitle">SUCCES:</div>
									<div id="RoundedSuccesTableContent">Succesfully logged in !</div></td>
								<td class="Right">&nbsp;</td>
							</tr>
							<tr>
								<td class="BottomLeft"></td>
				
								<td class="Bottom">&nbsp;</td>
								<td class="BottomRight"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			]]>
			
			
			<[BLOCK-ERROR[
			<table cellpadding="0" cellspacing="0" id="ErrorTable">
				<tr>
					<td valign="middle" align="center">
						<table cellpadding="0" cellspacing="0" id="RoundedErrorTable">
							<tr>
								<td class="TopLeft">&nbsp;</td>
								<td class="Top">&nbsp;</td>
								<td class="TopRight"></td>
							</tr>
							<tr>
				
								<td class="Left">&nbsp;</td>
								<td class="Center" id="SmallCell"><img src="/PXTMSTYLE/error/Error.png" style="position:relative; right:5px;" align="right" />
									<div id="RoundedErrorTableTitle">ERROR:</div>
									<div id="RoundedErrorTableContent">Unable to login !</div></td>
								<td class="Right">&nbsp;</td>
							</tr>
							<tr>
								<td class="BottomLeft"></td>
				
								<td class="Bottom">&nbsp;</td>
								<td class="BottomRight"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			]]>
			
			
			<[BLOCK-CONFIRM[
			<table cellpadding="0" cellspacing="0" id="ConfirmTable">
				<tr>
					<td valign="middle" align="center">
						<table cellpadding="0" cellspacing="0" id="RoundedConfirmTable">
							<tr>
								<td class="TopLeft">&nbsp;</td>
								<td class="Top">&nbsp;</td>
								<td class="TopRight"></td>
							</tr>
							<tr>
				
								<td class="Left">&nbsp;</td>
								<td class="Center" id="SmallCell"><img src="/PXTMSTYLE/confirm/Confirm.png" style="position:relative; right:5px;" align="right" />
									<div id="RoundedConfirmTableTitle">CONFIRM:</div>
									<div id="RoundedConfirmTableContent">Are you sure !</div>
									<div id="SmallTableButton"></div></td>
								<td class="Right">&nbsp;</td>
							</tr>
							<tr>
								<td class="BottomLeft"></td>
				
								<td class="Bottom">&nbsp;</td>
								<td class="BottomRight"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			]]>
			
			
			<[BLOCK-LOGIN[
			<table cellpadding="0" cellspacing="0" id="LoginTable">
				<tr>
					<td align="center" valign="middle"><div id="RoundedLoginBlock">
							<form name="LoginForm">
								<div id="LoginDescField"><div id="LoginDescContent">Provide your credentials and login!</div></div>
								<div id="LoginUsnField">
									<label for="LoginUsnInput">username:</label><br />
									<input type="text" name="Username" id="LoginUsnInput" />
								</div>
								<div id="LoginPwdField">
									<label for="LoginPwdInput">password:</label><br />
									<input type="password" name="Password" id="LoginPwdInput" />
								</div>
								<div id="LoginButtonField"><a href="javascript:PTM_RequestLogin();">Click to proceed</a></div>
							</form>
						</div></td>
				</tr>
			</table>
			]]>
			
			
			<[BLOCK-CONTENT[
			<table cellpadding="0" cellspacing="0" id="ModuleTable">
	
				<tr>
					<td class="TopLeft">&nbsp;</td>
					<td class="Top">&nbsp;</td>
					<td class="TopRight"></td>
				</tr>
				<tr>
					<td class="Left">&nbsp;</td>
					<td class="Center" id="ModuleCell"><table id="ModuleTabTable" cellpadding="0" cellspacing="0">
						<tr>
							<td id="ModuleTabMenu"><ul id="ModuleTabMenuList"><[BLOCK-CONTENT-TAB[
									<li<[SELECTED]> onclick="PTM_RequestContent('<[MAPPING]>'"><[TITLE]></li>
									<li>testB</li>
									<li class="selected">Selected</li>
								]]></ul></td>
							<td id="ModuleTabContent">
								<div id="MTCTitle">-nothing-</div>
								<div id="MTCDescription">-nothing-</div>
								<div id="MTCContent">-nothing-</div>
							</td>
						</tr>
					</table></td>
					<td class="Right">&nbsp;</td>
				</tr>
				<tr>
					<td class="BottomLeft"></td>
					<td class="Bottom">&nbsp;</td>
	
					<td class="BottomRight"></td>
				</tr>
			</table>
			]]>
			
		</div>
		<script language="javascript1.1" type="text/javascript">
		function PTM_StartupCommand() {
			<[COMMAND]>
		}
		</script>
	</div>
	<div id="HelpGroup"><div id="HelpSwitch"><input type="checkbox" checked="checked" onchange="PTM_SetCookie('OverLibHelp', this.checked);" onmouseover="PTM_SetSimpleHelp('Check this box to enable in-place-help.');" onmouseout="PTM_CH();" id="OverLibCheckBox" /></div><div id="HelpContainer">&nbsp;</div>&nbsp;PEToM v2.0 All rights reserved &copy; <a href="http://www.tomo-design.be">ToMo-design</a> - <a href="javascript:PTM_RequestRefresh();" title="Refresh the backend">Refresh</a></div>
</div>
</body>
</html>