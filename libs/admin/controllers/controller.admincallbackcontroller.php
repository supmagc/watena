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
	
	public function requestContent($sMapping = '/') {
		$oUser = $this->getUserManager()->getLoggedInUser();
		if(null == $oUser) {
			$this->getModel()->displayLogin('', '', '', $sMapping);
		}
		else {
			$oTab = Admin::getLoader()->getByMapping($sMapping);
			if($oTab !== false) {
				$this->getModel()->displayModuleTabs($oTab);
				$this->getModel()->displayModuleInfo($oTab);
				$this->getModel()->displayModuleContent($oTab);
			}
			else {
				$this->getModel()->displayError("The given mapping could not be matched to an existing module.", "Module 404", $this->getModel()->makeRequestLoadingContent('/'));
			}
		}
	}
	
	public function requestLogin($sUserName, $sPassword, $sContinueMapping) {
		try {
			$oUser = $this->getUserManager()->loginByName($sUserName, $sPassword);
			$this->getModel()->displaySucces("You are now logged in, enjoy your stay!", "Welcome " . $oUser->getName(), $this->getModel()->makeRequestLoadingContent($sContinueMapping));
		}
		catch(UserInvalidNameException $oUserException) {
			$this->getModel()->displayLogin($sUserName, 'Invalid username!', '');
		}
		catch(UserUnknownNameException $oUserException) {
			$this->getModel()->displayLogin($sUserName, 'Unknown username!', '');
		}
		catch(UserUnverifiedUserException $oUserException) {
			$this->getModel()->displayLogin($sUserName, 'Unverified username!', '');
		}
		catch(UserNoPasswordException $oUserException) {
			$this->getModel()->displayLogin($sUserName, '', 'Empty Password!');
		}
		catch(UserInvalidPasswordException $oUserException) {
			$this->getModel()->displayLogin($sUserName, '', 'Invalid password!');
		}
	}
}

?>