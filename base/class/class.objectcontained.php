<?php

abstract class ObjectContained extends Object implements IContainerItem {

	private $m_aContainedIn = array();

	abstract function getKeyForContainer(Container $oContainer);

	public final function addToContainer(Container $oContainer) {
		if(in_array($oContainer, $this->m_aContainedIn) || !$oContainer->addItem($this))
			return false;
			
		$this->m_aContainedIn []= $oContainer;
		return true;
	}

	public final function removeFromContainer(Container $oContainer) {
		if(($nIndex = array_search($oContainer, $this->m_aContainedIn)) === false || !$oContainer->removeItem($this))
			return false;
		
		unset($this->m_aContainedIn[$nIndex]);
		return true;
	}

	public final function removeFromAllContainers() {
		$aContainedIn = $this->m_aContainedIn;
		$this->m_aContainedIn = array();
		foreach($aContainedIn as $oContainer) {
			$oContainer->removeItem($this);
		}
	}
}