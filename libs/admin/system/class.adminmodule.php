<?php

class AdminModule extends CacheableFile {
	
	private $m_aMenus = array();
	
	public final function make(array $aMembers) {
		$oXml = new SimpleXMLElement($this->getFilePath(), null, true);
		foreach($oXml->menu as $oXmlMenu) {
			$sName = '' . $oXmlMenu['name'];
			$sCategory = '' . $oXmlMenu->category;
			$sDefaultTab = '' . $oXmlMenu->defaulttab;
			$sDescription = '' . $oXmlMenu->description;
			$oMenu = new AdminMenu($sName, $sCategory, $sDescription, $sDefaultTab);
			foreach($oXmlMenu->tab as $oXmlTab) {
				$sTabName = '' . $oXmlTab['name'];
				$sTabDescription = '' . $oXmlTab->description;
				$sTabContent = '' . $oXmlTab->content;
				$sTabContentType = !empty($sTabContent) ? ('' . $oXmlTab->content['type']) : '';
				$oMenu->addTab(new AdminTab($sTabName, $sTabDescription, $sTabContentType, $sTabContent));
			}
			$this->m_aMenus []= $oMenu;
		}
	}
	
	public static function createModule($sPath) {
		return self::create($sPath, array(), array());
	}
}

?>