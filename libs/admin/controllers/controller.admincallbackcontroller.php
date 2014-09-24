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
	
	public function requestContent($sLastMapping = '', $sNextMapping = '/', $sAction = null, array $aData = array(), array $aState = array()) {
		$oUser = $this->getUserManager()->getLoggedInUser();
		if(null == $oUser) {
			$this->getModel()->displayLogin('', '', '', $sNextMapping);
		}
		else {
			$oLastTab = Admin::getLoader()->getByMapping($sLastMapping);
			$oNextTab = Admin::getLoader()->getByMapping($sNextMapping);
			if($oNextTab !== false) {
				$oRequest = new AdminModuleContentRequest($oNextTab, $sAction, $aData, $aState);
				if(empty($oLastTab) || $oLastTab->getModuleItem() != $oNextTab->getModuleItem()) {
					$this->getModel()->displayModuleTabs($oRequest);
					$this->getModel()->displayModuleInfo($oRequest);
				}
				$this->getModel()->displayModuleContent($oRequest);
			}
			else {
				$this->getModel()->displayError("The given mapping could not be matched to an existing module.", "Module 404", $this->getModel()->makeRequestLoadingContent('/'));
			}
		}
	}
	
	public function requestNavItems() {
		$oUser = $this->getUserManager()->getLoggedInUser();
		if(null != $oUser) {
			$this->getModel()->displayNavItems(Admin::getLoader());
		}
	}
	
	public function requestLogin($sUserName, $sPassword, $sContinueMapping) {
		try {
			$oUser = $this->getUserManager()->loginByName($sUserName, $sPassword);
			$this->getModel()->displayNavItems(Admin::getLoader());
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