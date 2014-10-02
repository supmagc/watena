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
	
	public function requestContent($bInit, $sLastMapping = '', $sNextMapping = '/', $sAction = null, array $aData = array(), array $aState = array()) {
		$oUser = $this->getUserManager()->getLoggedInUser();
		if(null == $oUser) {
			if($bInit) {
				$this->getModel()->setShowSearch(false);
				$this->getModel()->setShowLogout(false);
				$this->getModel()->clearNavItems();
			}
			
			$this->getModel()->displayLogin('', '', '', $sNextMapping);
		}
		else {
			if($bInit) {
				$this->getModel()->setShowSearch(true);
				$this->getModel()->setShowLogout(true);
				$this->getModel()->displayNavItems(Admin::getLoader());
			}
			
			$oLastTab = Admin::getLoader()->getByMapping($sLastMapping);
			$oNextTab = Admin::getLoader()->getByMapping($sNextMapping);
			if($oNextTab !== false) {
				$oRequest = new AdminModuleContentRequest($oNextTab, $sAction, $aData, $aState);
				if($bInit || empty($oLastTab) || $oLastTab->getModuleItem() != $oNextTab->getModuleItem()) {
					$this->getModel()->displayModuleTabs($oRequest);
					$this->getModel()->displayModuleInfo($oRequest);
				}
				$this->getModel()->displayModuleContent($oRequest);
			}
			else {
				$this->getModel()->displayError("The given mapping could not be matched to an existing module.", "Module 404", AdminJSFunctions::makeRequestLoadingContent('/'));
			}
		}
	}
	
	public function requestLogin($sUserName, $sPassword, $sContinueMapping) {
		try {
			$oUser = $this->getUserManager()->loginByName($sUserName, $sPassword);
			$this->getModel()->displaySucces("You are now logged in, enjoy your stay!", "Welcome " . $oUser->getName(), AdminJSFunctions::makeRequestLoadingContent(true, $sContinueMapping));
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
	
	public function requestLogout() {
		$this->getUserManager()->logout();
		$this->getModel()->clearNavItems();
		$this->getModel()->clearModuleTabs();
		$this->getModel()->clearModuleInfo();
		$this->getModel()->clearModuleContent();
		$this->getModel()->setShowLogout(false);
		$this->getModel()->setShowSearch(false);
		$this->getModel()->displaySucces("Your session has been deactivated!", "See you next time!", AdminJSFunctions::makeDisplayLogin());
	}
}

?>