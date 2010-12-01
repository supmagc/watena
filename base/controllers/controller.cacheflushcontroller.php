<?php

class CacheFlushController extends Controller {
	
	public function render() {
		parent::getWatena()->getCache()->flush();
		parent::operationalMessage('Cache flushed!');
	}
}

?>