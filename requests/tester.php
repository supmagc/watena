<?php
define('NMVC', true);
include '../base/watena.php';

$aUrls = array(
	'http://api.flickr.com/services/feeds/photoset.gne?set=72157622354465015&nsid=9767342@N05&lang=en-us',
	'http://www.flickr.com/photos/the-dortenzios/sets/72157622354465015/',
	'http://www.flickr.com/photos/21752781@N03/',
	'http://api.flickr.com/services/feeds/photos_public.gne?id=21752781@N03&lang=en-us&format=rss_200',
	'http://www.flickr.com/photos/79371297@N07/favorites/',
	'http://api.flickr.com/services/feeds/photos_faves.gne?nsid=79371297@N07&lang=en-us&format=rss_200',
	'http://www.flickr.com/photos/79371297@N07/favorites/show/'
);

$aMatches = array();
$aData = array('flickr' => null);
foreach($aUrls as $sSource) {
	if(Encoding::regFind('/photos/([0-9@N]+)/sets/([0-9]+)', $sSource, $aMatches))
		$aData['flickr'] = array('source' => $sSource, 'type' => 'set', 'url' => "id=$aMatches[1]");
	else if(Encoding::regFind('/photos/([0-9@N]+)/favorites', $sSource, $aMatches))
		$aData['flickr'] = array('source' => $sSource, 'type' => 'faves', 'url' => "nsid=$aMatches[1]");
	else if(Encoding::regFind('/photos/([0-9@N]+)', $sSource, $aMatches))
		$aData['flickr'] = array('source' => $sSource, 'type' => 'public', 'url' => "id=$aMatches[1]");
	else if(Encoding::regFind('/photoset\\.gne\\?(set=[0-9]+&nsid=[0-9@N]+)', $sSource, $aMatches))
		$aData['flickr'] = array('source' => $sSource, 'type' => 'set', 'url' => $aMatches[1]);
	else if(Encoding::regFind('/photos_public\\.gne\\?(id=[0-9@N]+)', $sSource, $aMatches))
		$aData['flickr'] = array('source' => $sSource, 'type' => 'public', 'url' => $aMatches[1]);
	else if(Encoding::regFind('/photos_faves\\.gne\\?(nsid=[0-9@N]+)', $sSource, $aMatches))
		$aData['flickr'] = array('source' => $sSource, 'type' => 'faces', 'url' => $aMatches[1]);
	else
		$aData['flickr'] = array('source' => $sSource, 'type' => 'unknown', 'url' => '');
	
	echo '<pre>';
	dump($aData['flickr']);
	echo '</pre>';
}

$aUrls = array(
	'http://www.youtube.com/watch?gl=BE&feature=youtu.be&v=2PjjhUB8xts',
	'http://youtu.be/2PjjhUB8xts',
	'http://www.youtube.com/watch?v=2PjjhUB8xts',
	'<iframe width="560" height="315" src="http://www.youtube-nocookie.com/embed/2PjjhUB8xts?rel=0" frameborder="0" allowfullscreen></iframe>',
);

$aMatches = array();
$aData = array('youtube' => null);
foreach($aUrls as $sSource) {
	$aMatches = array();
	if(Encoding::regFind('youtu\\.be/([-a-zA-Z0-9]+)', $sSource, $aMatches))
		$aData['youtube'] = $aMatches[1];
	else if(Encoding::regFind('youtube\\.com/watch\\?.*v=([-a-zA-Z0-9]+)', $sSource, $aMatches))
		$aData['youtube'] = $aMatches[1];
	else if(Encoding::regFind('youtube-nocookie\\.com/embed/([-a-zA-Z0-9]+)', $sSource, $aMatches))
		$aData['youtube'] = $aMatches[1];
	$aData['youtube'] = "http://www.youtube-nocookie.com/embed/$aData[youtube]?version=3&feature=player_embedded&autoplay=1&controls=0&rel=0&showinfo=0";
	
	echo '<pre>';
	dump($aData['youtube']);
	echo '</pre>';
}

$aUrls = array(
	'https://picasaweb.google.com/data/feed/base/user/113489392464068054966?alt=rss&kind=album&hl=nl&imgmax=1600',
	'https://profiles.google.com/100499904495703797717/about',
	'https://profiles.google.com/100499904495703797717/photos',
	'https://profiles.google.com/100499904495703797717/photos/5605828277154706881',
	'https://picasaweb.google.com/data/feed/base/user/100499904495703797717?alt=rss&kind=photo'
	
);

$aMatches = array();
$aData = array('picasa' => null);
foreach($aUrls as $sSource) {
	if(Encoding::regFind('(user/[0-9]+/albumid/[0-9]+)', $sSource, $aMatches))
		$aData['picasa'] = array('source' => $sSource, 'type' => 'default', 'url' => $aMatches[1]);
	else if(Encoding::regFind('(user/[0-9]+)', $sSource, $aMatches))
		$aData['picasa'] = array('source' => $sSource, 'type' => 'default', 'url' => $aMatches[1]);
	else if(Encoding::regFind('google\\.com/([0-9]+)/photos/([0-9]+)', $sSource, $aMatches))
		$aData['picasa'] = array('source' => $sSource, 'type' => 'default', 'url' => "user/$aMatches[1]/albumid/$aMatches[2]");
	else if(Encoding::regFind('google\\.com/([0-9]+)', $sSource, $aMatches))
		$aData['picasa'] = array('source' => $sSource, 'type' => 'default', 'url' => "user/$aMatches[1]");
	else {
		$aData['picasa'] = array('source' => $sSource, 'type' => 'unknown', 'url' => '');
		$aErrors []= 'picasa';
	}
	
	echo '<pre>';
	dump($aData['picasa']);
	echo '</pre>';
}

?>