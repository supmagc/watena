<?php
require_plugin('UserManager');

class LogoutController extends Controller {
	
	public function process(Model $oModel, View $oView) {
		UserManager::setLoggedInUser(null);
		$this->redirect('/');
	}
}

?>