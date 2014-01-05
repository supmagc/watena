<?php
require_controller('CallbackController');
require_plugin('Admin');
require_plugin('UserManager');

class AdminCallbackController extends CallbackController {
	
	/**
	 * @return UserManager
	 */
	public function getUserManager() {
		return watena()->getContext()->getPlugin('UserManager');
	}
	
	public function requestContent($sModule = null, $sTab = null) {
		$oUser = $this->getUserManager()->getLoggedInUser();
		if(null == $oUser) {
			$this->getModel()->displayLogin();
		}
	}
}

?>