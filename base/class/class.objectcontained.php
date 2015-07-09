<?php namespace Watena\Core;

abstract class ObjectContained extends Object implements IContainerItem {

	private $m_aContainedIn = array();

	abstract function getKeyForContainer(Container $oContainer);
	protected function onAddedToContainer(Container $oContainer) {}
	protected function onRemovedFromContainer(Container $oContainer) {}

	public final function addedToContainer(Container $oContainer) {
		if(($nIndex = array_search($oContainer, $this->m_aContainedIn, true)) === false)
			$this->m_aContainedIn []= $oContainer;
		$this->onAddedToContainer($oContainer);
	}

	public final function removedFromContainer(Container $oContainer) {
		if(($nIndex = array_search($oContainer, $this->m_aContainedIn, true)) !== false)
			unset($this->m_aContainedIn[$nIndex]);
		$this->onRemovedFromContainer($oContainer);
	}

	public final function removeFromAllContainers() {
		foreach($this->m_aContainedIn as $oContainer) {
			$oContainer->removeItem($this);
		}
		$this->m_aContainedIn = array();
	}
}