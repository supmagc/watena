<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="UTF-8" />
	<title tpl:content="getTitle()">Portfolio main template</title>
	<link href="/laf/zoom.css" rel="stylesheet" type="text/css" />
	<link href="/laf/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
	<!--
	function Init() {
		bg_Size();
		img = new Image();
		img.src = "<[LAF]>/btn_archive_over.png";
		img.src = "<[LAF]>/btn_contact_over.png";
		img.src = "<[LAF]>/btn_profile_over.png";
		img.src = "<[LAF]>/btn_search_over.png";
	}
	
	function bg_Size() {
		// BG lines
		document.getElementById("bg_left").style.height = document.body.parentNode.scrollHeight + "px";
		document.getElementById("bg_left").style.width = (document.body.parentNode.scrollWidth / 2 + document.body.parentNode.scrollWidth % 2) + "px";
		document.getElementById("bg_right").style.height = document.body.parentNode.scrollHeight + "px";
		document.getElementById("bg_right").style.width = (document.body.parentNode.scrollWidth / 2 - document.body.parentNode.scrollWidth % 2) + "px";
		document.getElementById("bg_right").style.left = (document.body.parentNode.scrollWidth / 2 + document.body.parentNode.scrollWidth % 2) + "px";
	
		// BG gradient
		var offset = 200;
		var height = 800;
		if(window.innerHeight < offset + height) {
			var dif = (offset + height) - window.innerHeight;
			document.getElementById("bg_gradient").style.top = offset - dif/2 + "px";
			document.getElementById("bg_gradient").style.height = (height - dif/2) + dif%2 + "px";
		}
		else {
			document.getElementById("bg_gradient").style.top = window.innerHeight - height + "px";
			document.getElementById("bg_gradient").style.height = height + "px";
		}
	
		// table side heights
		var tds = document.getElementsByTagName("td");
		for(i=0 ; i<tds.length ; ++i) {
			var sizeto = tds.item(i).getAttribute("size-to");
			if(sizeto) {
				for(j=0 ; j<tds.length ; ++j) {
					var size = tds.item(j).getAttribute("size-ref");
					if(size && size == sizeto) {
						var sizemin = tds.item(j).getAttribute("size-min");
						sizemin = sizemin ? sizemin : 0;
						tds.item(i).style.height = tds.item(j).scrollHeight - sizemin + "px";
					}
				}
			}
		}
	}
	//--></script>
</head>
<body onLoad="Init();" onResize="bg_Size();">
<div style="position:relative; height:100%; text-align:center;"><div tpl:content=""></div>
	<div id="bg_left" tpl:content="bla"></div>
	<div id="bg_right"></div>
	<div id="bg_gradient"></div>
	<div id="bg_blur">
		<div style="width:842px; margin:auto;">
			<div style="height:15px;"></div>
			<table class="zoomed" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="lt"></td>
					<td class="tlt"></td>
					<td class="t"></td>
					<td class="trt"></td>
					<td class="rt"></td>
				</tr>
				<tr>
					<td class="llt"></td>
					<td class="center" size-ref="top" size-min="42" colspan="3" rowspan="3"><div id="top">
							<div id="btn_archive"><a href=""><img src="/laf/btn_archive.png" onMouseOver="this.src='<[LAF]>/btn_archive_over.png';" onMouseOut="this.src='<[LAF]>/btn_archive.png';" /></a></div>
							<div id="btn_contact"><a href=""><img src="/laf/btn_contact.png" onMouseOver="this.src='<[LAF]>/btn_contact_over.png';" onMouseOut="this.src='<[LAF]>/btn_contact.png';" /></a></div>
							<div id="btn_profile"><a href=""><img src="/laf/btn_profile.png" onMouseOver="this.src='<[LAF]>/btn_profile_over.png';" onMouseOut="this.src='<[LAF]>/btn_profile.png';" /></a></div>
							<div id="btn_search"><a href=""><img src="/laf/btn_search.png" onMouseOver="this.src='<[LAF]>/btn_search_over.png';" onMouseOut="this.src='<[LAF]>/btn_search.png';" /></a></div>
						</div></td>
					<td class="rrt"></td>
				</tr>
				<tr>
					<td class="l" size-to="top"></td>
					<td class="r" size-to="top"></td>
				</tr>
				<tr>
					<td class="llb"></td>
					<td class="rrb"></td>
				</tr>
				<tr>
					<td class="lb"></td>
					<td class="blb"></td>
					<td class="b"></td>
					<td class="brb"></td>
					<td class="rb"></td>
				</tr>
			</table>
			<div style="width:842px; height:264px;"><img src="/laf/header.png" /></div>
			<table class="zoomed" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="l" size-to="content"></td>
					<td class="center" size-ref="content" size-min="21" colspan="3" rowspan="2"><div id="main">
							<div id="main_zoom_left"></div>
							<div id="main_zoom_middle"></div>
							<div id="main_zoom_right"></div>
							<table class="main" cellpadding="0" cellspacing="0">
								<tr>
									<td class="l"><div class="contentItem">
											<div class="contentItemHeader">Welcome</div>
											<div class="contentItemContent">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed aliquet adipiscing diam, id pretium <strong>turpis</strong> consectetur in. Quisque eros est, mattis sit amet lacinia id, tincidunt ut velit. Fusce iaculis est in lacus <em>suscipit</em> pellentesque <strong>venenatis</strong> massa semper. Vestibulum ultrices arcu et massa laoreet interdum. Maecenas vulputate metus quis orci ornare eu sollicitudin turpis auctor. Sed et felis lorem, eget ultrices mi.<br />
												<a href="">Read more</a></div>
											<div class="contentItemFooter">12 jun 2010 - 3 comments</div>
										</div><div class="contentItem">
											<div class="contentItemHeader">About this site</div>
											<div class="contentItemContent">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed aliquet adipiscing diam, id pretium <strong>turpis</strong> consectetur in. Quisque eros est, mattis sit amet lacinia id, tincidunt ut velit. Fusce iaculis est in lacus <em>suscipit</em> pellentesque <strong>venenatis</strong> massa semper. Vestibulum ultrices arcu et massa laoreet interdum. Maecenas vulputate metus quis orci ornare eu sollicitudin turpis auctor. Sed et felis lorem, eget ultrices mi.<br />
												<a href="">Read more</a></div>
											<div class="contentItemFooter">12 jun 2010 - 3 comments</div>
										</div></td>
									<td class="r"><div class="menuItem">
											<div class="menuItemHeader"><img src="/laf/menu_hotlinks.png" /></div>
											<div class="menuItemContent">bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla</div>
										</div>
										<div class="menuItem">
											<div class="menuItemHeader"><img src="/laf/menu_newest.png" /></div>
											<div class="menuItemContent">bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla</div>
										</div>
										<div class="menuItem">
											<div class="menuItemHeader"><img src="/laf/menu_categories.png" /></div>
											<div class="menuItemContent">bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla</div>
										</div>
										<div class="menuItem">
											<div class="menuItemHeader"><img src="/laf/menu_connect.png" /></div>
											<div class="menuItemContent">Waiting for a stable connection!</div>
										</div>
										<div class="menuItem">
											<div class="menuItemHeader"><img src="/laf/menu_neighbours.png" /></div>
											<div class="menuItemContent"><ul><li>link<Li></ul></div>
										</div>
										<div class="menuItem">
											<div class="menuItemHeader"><img src="/laf/menu_quote.png" /></div>
											<div class="menuItemContent">&ldquo;Hello world !&rdquo;<br />~<em>Jelle</em></div>
										</div>
										<div class="menuSpacer"></div></td>
								</tr>
							</table>
						</div></td>
					<td class="r" size-to="content">&nbsp;</td>
				</tr>
				<tr>
					<td class="llb"></td>
					<td class="rrb"></td>
				</tr>
				<tr>
					<td class="lb"></td>
					<td class="blb"></td>
					<td class="b"></td>
					<td class="brb"></td>
					<td class="rb"></td>
				</tr>
			</table>
			<div>2010&nbsp;&copy;&nbsp;www.tomo-design.be</div>
			<div style="width:842px; height:20px;"></div>
		</div>
	</div>
</div>
</body>
</html>