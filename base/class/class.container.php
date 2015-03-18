<?php

final class Container extends Object implements IteratorAggregate {
	
	private $m_aItems = array();
	private $m_mIdentifier;
	
	public function __construct($mIdentifier = null) {
		$this->m_mIdentifier = $mIdentifier;
	}
	
	public function getIdentifier() {
		return $this->m_mIdentifier;
	}
	
	/**
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator($this->m_aItems);
	}
	
	public function getItem($mKey, $mDefault = null) {
		return isset($this->m_aItems[$mKey]) ? $this->m_aItems[$mKey] : $mDefault;
	}
	
	public function getItems() {
		return $this->m_aItems;
	}
	
	public function containsItem(IContainerItem $oItem) {
		$sKey = $oItem->getKeyForContainer($this);
		return isset($this->m_aItems[$sKey]);
	}
	
	public function addItem(IContainerItem $oItem) {
		$sKey = $oItem->getKeyForContainer($this);
		if(isset($this->m_aItems[$sKey]))
			return false;
		
		$this->m_aItems[$sKey] = $oItem;
		$oItem->addToContainer($this);
		return true;
	}
	
	public function removeItem(IContainerItem $oItem) {
		$sKey = $oItem->getKeyForContainer($this);
		if(!isset($this->m_aItems[$sKey])) 
			return false;
		
		unset($this->m_aItems[$sKey]);
		$oItem->removeFromContainer($this);
		return true;
	}
}
