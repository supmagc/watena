<?php
define('NMVC', true);
include '../base/watena.php';
require_plugin('ToeVla');

$aUrls = array(
	'http://api.flickr.com/services/feeds/photoset.gne?set=72157622354465015&nsid=9767342@N05&lang=en-us',
	'http://www.flickr.com/photos/the-dortenzios/sets/72157622354465015/',
	'http://www.flickr.com/photos/21752781@N03/',
	'http://api.flickr.com/services/feeds/photos_public.gne?id=21752781@N03&lang=en-us&format=rss_200',
	'http://www.flickr.com/photos/79371297@N07/favorites/',
	'http://api.flickr.com/services/feeds/photos_faves.gne?nsid=79371297@N07&lang=en-us&format=rss_200',
	'http://www.flickr.com/photos/79371297@N07/favorites/show/'
);

foreach($aUrls as $sSource) {
	$bError = false;
	echo '<pre>';
	dump(ToeVla::parseFlickr($sSource, $bError));
	dump($bError);
	echo '</pre>';
}

$aUrls = array(
	'http://www.youtube.com/watch?gl=BE&feature=youtu.be&v=2PjjhUB8xts',
	'http://youtu.be/2PjjhUB8xts',
	'http://www.youtube.com/watch?v=2PjjhUB8xts',
	'<iframe width="560" height="315" src="http://www.youtube-nocookie.com/embed/2PjjhUB8xts?rel=0" frameborder="0" allowfullscreen></iframe>',
);

foreach($aUrls as $sSource) {
	$bError = false;
	echo '<pre>';
	dump(ToeVla::parseYoutube($sSource, $bError));
	dump($bError);
	echo '</pre>';
}

$aUrls = array(
	'https://picasaweb.google.com/data/feed/base/user/113489392464068054966?alt=rss&kind=album&hl=nl&imgmax=1600',
	'https://profiles.google.com/100499904495703797717/about',
	'https://profiles.google.com/100499904495703797717/photos',
	'https://profiles.google.com/100499904495703797717/photos/5605828277154706881',
	'https://picasaweb.google.com/data/feed/base/user/100499904495703797717?alt=rss&kind=photo'
);

foreach($aUrls as $sSource) {
	$bError = false;
	echo '<pre>';
	dump(ToeVla::parsePicasa($sSource, $bError));
	dump($bError);
	echo '</pre>';
}

$aUrls = array(
	'https://twitter.com/#!/hestival_heist',
	'@hestival_heist',
	'hestival_heist',
);

foreach($aUrls as $sSource) {
	$bError = false;
	echo '<pre>';
	dump(ToeVla::parseTwitterName($sSource, $bError));
	dump($bError);
	echo '</pre>';
}

$aUrls = array(
	'#hestival_heist',
	'hestival_heist'
);

foreach($aUrls as $sSource) {
	$bError = false;
	echo '<pre>';
	dump(ToeVla::parseTwitterHash($sSource, $bError));
	dump($bError);
	echo '</pre>';
}

$aUrls = array(
	'http://www.facebook.com/genkonstage',
	'https://www.facebook.com/#!/popwijk',
	'http://www.facebook.com/pages/Antilliaanse-feesten/112970158735517'
);

foreach($aUrls as $sSource) {
	$bError = false;
	echo '<pre>';
	dump(ToeVla::parseFacebook($sSource, $bError));
	dump($bError);
	echo '</pre>';
}

?>