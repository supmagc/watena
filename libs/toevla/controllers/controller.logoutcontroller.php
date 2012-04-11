<?php
require_plugin('UserManager');

class LogoutController extends Controller {
	
	public function process(Model $oModel = null, View $oView = null) {
		UserManager::setLoggedInUser(null);
		$this->redirect('/');
	}
}

?>