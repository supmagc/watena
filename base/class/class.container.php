<?php

final class Container extends Object implements IteratorAggregate {
	
	private $m_aItems = array();
	private $m_cbVerifyAdd;
	private $m_cbVerifyRemove;
	private $m_mIdentifier;
	
	public function __construct($mIdentifier = null, callable $cbVerifyAdd = null, callable $cbVerifyRemove = null) {
		$this->m_mIdentifier = $mIdentifier;
		$this->m_cbVerifyAdd = $cbVerifyAdd;
		$this->m_cbVerifyRemove = $cbVerifyRemove;
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
		if(isset($this->m_aItems[$sKey]) || ($this->m_cbVerifyAdd && !call_user_func($this->m_cbVerifyAdd, $oItem)))
			return false;
		
		$this->m_aItems[$sKey] = $oItem;
		$oItem->addedToContainer($this);
		return true;
	}
	
	public function removeItem(IContainerItem $oItem) {
		$sKey = $oItem->getKeyForContainer($this);
		if(!isset($this->m_aItems[$sKey]) || ($this->m_cbVerifyRemove && !call_user_func($this->m_cbVerifyRemove, $oItem))) 
			return false;
		
		unset($this->m_aItems[$sKey]);
		$oItem->removedFromContainer($this);
		return true;
	}
}
