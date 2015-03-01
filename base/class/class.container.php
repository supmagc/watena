<?php

interface IContainerItem {
	
	public function getKeyForContainer(Container $oContainer);
	public function addedToContainer(Container $oContainer);
	public function removedFromContainer(Container $oContainer);
}

class Container extends Object implements IteratorAggregate {
	
	private $m_aItems = array();
	
	/**
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator($this->m_aItems);
	}
	
	public function getItem($mKey) {
		
	}
	
	public function getItems() {
		
	}
	
	public function containsItem(IContainerItem $oItem) {
		$sKey = $oItem->getKeyForContainer($this);
		return isset($this->m_aItems[$sKey]);
	}
	
	public function addItem(IContainerItem $oItem) {
		$sKey = $oItem->getKeyForContainer($this);
		if(!isset($this->m_aItems[$sKey])) {
			$this->m_aItems[$sKey] = $oItem;
			$oItem->addedToContainer($this);
		}
	}
	
	public function removeItem(IContainerItem $oItem) {
		$sKey = $oItem->getKeyForContainer($this);
		if(isset($this->m_aItems[$sKey])) {
			unset($this->m_aItems[$sKey]);
			$oItem->removedFromContainer($this);
		}
	}
}
