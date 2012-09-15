<?php
define('NMVC', true);
include '../index.php';
set_time_limit(600);

for($i = 0 ; $i<500 ; ++$i) {
	$oRequest = new WebRequest("http://www.oohfoto.be/ana%C3%AFs/images/anai_s_{$i}_1.jpg");
	$oResponse = $oRequest->send();
	if($oResponse->getContentSize() > 0 && $oResponse->getHttpCode() == 200 && !Encoding::contains($oResponse->getContent(), 'Not Found')) {
		file_put_contents("D:/www/Anais/{$i}_1.jpg", file_get_contents("http://www.oohfoto.be/ana%C3%AFs/images/anai_s_{$i}_1.jpg"));
		echo "http://www.oohfoto.be/ana%C3%AFs/images/anai_s_{$i}.jpg => D:/www/Anais/$i.jpg";
	}
}
?>