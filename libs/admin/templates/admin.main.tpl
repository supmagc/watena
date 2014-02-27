<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="{[getContentType()]}; charset={[getCharset()]}">
	<meta http-equiv="Description" content="{[getDescription()]}">
	<meta http-equiv="Keywords" content="{[getKeywords()]}">
	<title>Watena - {[getTitle()]}</title>
	<link href="/theme/admin/css/admin.main.css" rel="stylesheet" type="text/css" media="all" />
	<link href="/theme/admin/css/admin.overlay.css" rel="stylesheet" type="text/css" media="all" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600|Droid+Sans:400,700' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,300,500|Exo+2:200,400,200italic,400italic' rel='stylesheet' type='text/css'>
	{{call addJavascriptLink(url('/theme/admin/js/jquery-1.10.2.min.js'))}}
	{{call addJavascriptLink(url('/theme/default/js/ajax.js'))}}
	{{call addJavascriptLink(url('/theme/admin/js/watena-admin.js'))}}
	{[getJavascriptLoader('loaderCallback')]}
	{[getAjax()]}
</head>
<body>
	<div id="nav">
		<div id="nav-logo">Watena</div>
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
				<div class="title">Dashboard</div>
				<ul>
					<li>Home</li>
					<li>News</li>
					<li>Session</li>
				</ul>
			</div>
			<div id="main-module">
				<div class="title">Module-Info</div>
				<u>Name</u>: AdminMain<br />
				<u>Version</u>: 0.1.0 beta<br />
				<br />
				This module provides some basic welcome page functionality and is meant to be the default module when first visiting the backend.
			</div>
			<div id="main-copy">2013 &copy; Voet Jelle</div>
		</div>
		<div id="main-right">
			<div id="main-content">
				<div class="title">Home</div>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque consequat consectetur nisl nec facilisis. Vestibulum viverra dui at laoreet vulputate. Mauris elementum, ipsum eget adipiscing sollicitudin, est ante venenatis velit, id dictum turpis ligula vel arcu. Sed id velit sem. Vestibulum eu faucibus tortor. Nam cursus euismod magna, nec placerat purus faucibus a. Sed auctor augue orci, eu fringilla libero ullamcorper in. Etiam tincidunt tortor sit amet elementum aliquet. Morbi cursus lectus non tortor tincidunt, ac tincidunt urna sagittis. Integer mi ipsum, adipiscing eget nulla ac, malesuada cursus metus. Mauris non justo cursus turpis bibendum dignissim id non sapien. Sed a tortor non elit vulputate ultricies non vitae orci. Fusce sed nisi a enim aliquet dapibus. Sed sapien neque, tincidunt quis diam in, consequat convallis quam. Maecenas ullamcorper dictum tellus, quis hendrerit leo dapibus eget. Curabitur tempor dictum sapien sit amet tempor.</p>
				<p>Vestibulum vehicula lorem tincidunt quam malesuada placerat. Nullam ut elit leo. Aliquam erat volutpat. Sed mollis nisi vitae ullamcorper tincidunt. Pellentesque malesuada lacinia justo, consectetur ultricies eros elementum vel. Mauris malesuada viverra turpis, vel rhoncus tellus tincidunt sit amet. Pellentesque malesuada sapien sodales felis adipiscing sollicitudin. Fusce arcu orci, ultrices nec venenatis nec, imperdiet a orci. Quisque ullamcorper purus et erat congue, sed vestibulum elit molestie. Curabitur ultricies augue ac adipiscing volutpat. Fusce lobortis viverra odio vel mattis. Morbi in lacus sit amet nisl luctus pharetra. Vestibulum tincidunt eleifend risus ut varius. Vivamus sit amet euismod nunc.</p>
				<p>Phasellus ac nunc gravida elit consectetur tempus. Nam porttitor porttitor nisi, sit amet pharetra nisl fringilla id. Vivamus et luctus massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam sit amet gravida massa. Aenean a mi augue. Proin eget velit in nisi semper volutpat. Mauris ac dui ac sapien porttitor lacinia non ut elit. Duis metus nisi, tempus sit amet feugiat nec, mattis eget diam. Aliquam pulvinar purus a nulla dapibus, nec consectetur neque sollicitudin. Maecenas vehicula arcu sit amet sem blandit vulputate. Cras pulvinar in sapien sit amet tincidunt.</p>
				<p>Fusce id hendrerit lorem. Etiam sapien urna, vulputate vitae mattis dignissim, malesuada at nisi. Quisque mattis bibendum molestie. Fusce urna purus, consequat a euismod viverra, elementum nec urna. Integer tempus metus vel nisi euismod fermentum. In hac habitasse platea dictumst. Nunc nec libero fermentum, volutpat ligula id, consequat tellus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non faucibus risus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nulla ultricies nunc et mi dictum, quis lacinia dolor scelerisque. Duis nec erat sed felis egestas tempor. In mattis leo ac purus congue, tristique ultrices nisl dapibus.</p>
				<p>Sed porttitor, nisl in tincidunt scelerisque, nisl orci euismod sapien, non tempus enim nisi eu velit. Nulla ullamcorper dui lorem, at faucibus orci faucibus et. Fusce vestibulum sodales arcu, non euismod dui blandit ut. Quisque tempor eu erat a sollicitudin. Vivamus posuere enim ac sem euismod feugiat. Maecenas iaculis nisi a lacinia posuere. Mauris at erat justo. Ut mollis eget justo ut cursus. Duis vitae tincidunt velit. Nulla et luctus velit, porttitor lobortis elit. Sed ornare euismod tempus. Nunc posuere dolor ut mauris mollis, ut ultricies mauris porta. Nulla ultricies felis in ante dignissim, vitae tempus enim consectetur. Proin sed pellentesque libero.</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque consequat consectetur nisl nec facilisis. Vestibulum viverra dui at laoreet vulputate. Mauris elementum, ipsum eget adipiscing sollicitudin, est ante venenatis velit, id dictum turpis ligula vel arcu. Sed id velit sem. Vestibulum eu faucibus tortor. Nam cursus euismod magna, nec placerat purus faucibus a. Sed auctor augue orci, eu fringilla libero ullamcorper in. Etiam tincidunt tortor sit amet elementum aliquet. Morbi cursus lectus non tortor tincidunt, ac tincidunt urna sagittis. Integer mi ipsum, adipiscing eget nulla ac, malesuada cursus metus. Mauris non justo cursus turpis bibendum dignissim id non sapien. Sed a tortor non elit vulputate ultricies non vitae orci. Fusce sed nisi a enim aliquet dapibus. Sed sapien neque, tincidunt quis diam in, consequat convallis quam. Maecenas ullamcorper dictum tellus, quis hendrerit leo dapibus eget. Curabitur tempor dictum sapien sit amet tempor.</p>
				<p>Vestibulum vehicula lorem tincidunt quam malesuada placerat. Nullam ut elit leo. Aliquam erat volutpat. Sed mollis nisi vitae ullamcorper tincidunt. Pellentesque malesuada lacinia justo, consectetur ultricies eros elementum vel. Mauris malesuada viverra turpis, vel rhoncus tellus tincidunt sit amet. Pellentesque malesuada sapien sodales felis adipiscing sollicitudin. Fusce arcu orci, ultrices nec venenatis nec, imperdiet a orci. Quisque ullamcorper purus et erat congue, sed vestibulum elit molestie. Curabitur ultricies augue ac adipiscing volutpat. Fusce lobortis viverra odio vel mattis. Morbi in lacus sit amet nisl luctus pharetra. Vestibulum tincidunt eleifend risus ut varius. Vivamus sit amet euismod nunc.</p>
				<p>Phasellus ac nunc gravida elit consectetur tempus. Nam porttitor porttitor nisi, sit amet pharetra nisl fringilla id. Vivamus et luctus massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam sit amet gravida massa. Aenean a mi augue. Proin eget velit in nisi semper volutpat. Mauris ac dui ac sapien porttitor lacinia non ut elit. Duis metus nisi, tempus sit amet feugiat nec, mattis eget diam. Aliquam pulvinar purus a nulla dapibus, nec consectetur neque sollicitudin. Maecenas vehicula arcu sit amet sem blandit vulputate. Cras pulvinar in sapien sit amet tincidunt.</p>
				<p>Fusce id hendrerit lorem. Etiam sapien urna, vulputate vitae mattis dignissim, malesuada at nisi. Quisque mattis bibendum molestie. Fusce urna purus, consequat a euismod viverra, elementum nec urna. Integer tempus metus vel nisi euismod fermentum. In hac habitasse platea dictumst. Nunc nec libero fermentum, volutpat ligula id, consequat tellus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non faucibus risus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nulla ultricies nunc et mi dictum, quis lacinia dolor scelerisque. Duis nec erat sed felis egestas tempor. In mattis leo ac purus congue, tristique ultrices nisl dapibus.</p>
				<p>Sed porttitor, nisl in tincidunt scelerisque, nisl orci euismod sapien, non tempus enim nisi eu velit. Nulla ullamcorper dui lorem, at faucibus orci faucibus et. Fusce vestibulum sodales arcu, non euismod dui blandit ut. Quisque tempor eu erat a sollicitudin. Vivamus posuere enim ac sem euismod feugiat. Maecenas iaculis nisi a lacinia posuere. Mauris at erat justo. Ut mollis eget justo ut cursus. Duis vitae tincidunt velit. Nulla et luctus velit, porttitor lobortis elit. Sed ornare euismod tempus. Nunc posuere dolor ut mauris mollis, ut ultricies mauris porta. Nulla ultricies felis in ante dignissim, vitae tempus enim consectetur. Proin sed pellentesque libero.</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque consequat consectetur nisl nec facilisis. Vestibulum viverra dui at laoreet vulputate. Mauris elementum, ipsum eget adipiscing sollicitudin, est ante venenatis velit, id dictum turpis ligula vel arcu. Sed id velit sem. Vestibulum eu faucibus tortor. Nam cursus euismod magna, nec placerat purus faucibus a. Sed auctor augue orci, eu fringilla libero ullamcorper in. Etiam tincidunt tortor sit amet elementum aliquet. Morbi cursus lectus non tortor tincidunt, ac tincidunt urna sagittis. Integer mi ipsum, adipiscing eget nulla ac, malesuada cursus metus. Mauris non justo cursus turpis bibendum dignissim id non sapien. Sed a tortor non elit vulputate ultricies non vitae orci. Fusce sed nisi a enim aliquet dapibus. Sed sapien neque, tincidunt quis diam in, consequat convallis quam. Maecenas ullamcorper dictum tellus, quis hendrerit leo dapibus eget. Curabitur tempor dictum sapien sit amet tempor.</p>
				<p>Vestibulum vehicula lorem tincidunt quam malesuada placerat. Nullam ut elit leo. Aliquam erat volutpat. Sed mollis nisi vitae ullamcorper tincidunt. Pellentesque malesuada lacinia justo, consectetur ultricies eros elementum vel. Mauris malesuada viverra turpis, vel rhoncus tellus tincidunt sit amet. Pellentesque malesuada sapien sodales felis adipiscing sollicitudin. Fusce arcu orci, ultrices nec venenatis nec, imperdiet a orci. Quisque ullamcorper purus et erat congue, sed vestibulum elit molestie. Curabitur ultricies augue ac adipiscing volutpat. Fusce lobortis viverra odio vel mattis. Morbi in lacus sit amet nisl luctus pharetra. Vestibulum tincidunt eleifend risus ut varius. Vivamus sit amet euismod nunc.</p>
				<p>Phasellus ac nunc gravida elit consectetur tempus. Nam porttitor porttitor nisi, sit amet pharetra nisl fringilla id. Vivamus et luctus massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam sit amet gravida massa. Aenean a mi augue. Proin eget velit in nisi semper volutpat. Mauris ac dui ac sapien porttitor lacinia non ut elit. Duis metus nisi, tempus sit amet feugiat nec, mattis eget diam. Aliquam pulvinar purus a nulla dapibus, nec consectetur neque sollicitudin. Maecenas vehicula arcu sit amet sem blandit vulputate. Cras pulvinar in sapien sit amet tincidunt.</p>
				<p>Fusce id hendrerit lorem. Etiam sapien urna, vulputate vitae mattis dignissim, malesuada at nisi. Quisque mattis bibendum molestie. Fusce urna purus, consequat a euismod viverra, elementum nec urna. Integer tempus metus vel nisi euismod fermentum. In hac habitasse platea dictumst. Nunc nec libero fermentum, volutpat ligula id, consequat tellus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non faucibus risus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nulla ultricies nunc et mi dictum, quis lacinia dolor scelerisque. Duis nec erat sed felis egestas tempor. In mattis leo ac purus congue, tristique ultrices nisl dapibus.</p>
				<p>Sed porttitor, nisl in tincidunt scelerisque, nisl orci euismod sapien, non tempus enim nisi eu velit. Nulla ullamcorper dui lorem, at faucibus orci faucibus et. Fusce vestibulum sodales arcu, non euismod dui blandit ut. Quisque tempor eu erat a sollicitudin. Vivamus posuere enim ac sem euismod feugiat. Maecenas iaculis nisi a lacinia posuere. Mauris at erat justo. Ut mollis eget justo ut cursus. Duis vitae tincidunt velit. Nulla et luctus velit, porttitor lobortis elit. Sed ornare euismod tempus. Nunc posuere dolor ut mauris mollis, ut ultricies mauris porta. Nulla ultricies felis in ante dignissim, vitae tempus enim consectetur. Proin sed pellentesque libero.</p>
			</div>
		</div>
	</div>
	{{include admin.overlay.login.tpl}}
	{{include admin.overlay.error.tpl}}
	{{include admin.overlay.succes.tpl}}
	{{include admin.overlay.info.tpl}}
</body>
</html>