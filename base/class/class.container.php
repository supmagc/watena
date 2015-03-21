<?php
/**
 * Helper class to contain and manage a list of specific items.
 * Originally created for UserManager, but ported to Watena main.
 * 
 * It's purpose is to have a simple and consistent way to keep a
 * collection of objects without replicating similar code.
 * Main features:
 * - Associativeness based on user-specifiable key
 * - Callback for verifying add operations
 * - Auto sorting when items are added
 * - Iterator implementation
 * - Easy access to first, last or default item(s)
 * 
 * @author Jelle
 * @version 0.1.0
 */
final class Container extends Object implements IteratorAggregate {
	
	private $m_aItems = array();
	private $m_cbVerifyAdd;
	private $m_cbSort;
	private $m_mIdentifier;
	
	/**
	 * Create a new container instance.
	 * 
	 * @param string $mIdentifier optional identifier, can be used to determine which conteiner use differences.
	 * @param callable $cbVerifyAdd Verify if the given item can be added. Signature: bool function(IContainerItem);
	 * @param callable $cbSort Compare function for sorting. Signature: int function(IContainerItem $a, IContainerItem $b); (-1: $a<$b, 0:$a=$b, 1:$a>$b)
	 */
	public function __construct($mIdentifier = null, callable $cbVerifyAdd = null, callable $cbSort = null) {
		$this->m_mIdentifier = $mIdentifier;
		$this->m_cbVerifyAdd = $cbVerifyAdd;
		$this->m_cbSort = $cbSort;
	}
	
	/**
	 * Get the identifier specified when creating the instance.
	 * 
	 * @return string
	 */
	public function getIdentifier() {
		return $this->m_mIdentifier;
	}
	
	/**
	 * Get iterator (used for looping).
	 * 
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator($this->m_aItems);
	}
	
	/**
	 * Get the item specified by the given key, or return the default value.
	 * 
	 * @param mixed $mDefault
	 * @return IContainerItem|$mDefault
	 */
	public function getItem($mKey, $mDefault = null) {
		return isset($this->m_aItems[$mKey]) ? $this->m_aItems[$mKey] : $mDefault;
	}

	/**
	 * Get the amount of items in this container.
	 * 
	 * @return int
	 */
	public function getItemCount() {
		return count($this->m_aItems);
	}
	
	/**
	 * Get the first item in the container.
	 * 
	 * @param mixed $mDefault
	 * @return IContainerItem|$mDefault
	 */
	public function getItemFirst($mDefault = null) {
		return array_first($this->m_aItems, $mDefault);
	}
	
	/**
	 * Get the last item in the container.
	 * 
	 * @param mixed $mDefault
	 * @return IContainerItem|$mDefault
	 */
	public function getItemLast($mDefault = null) {
		return array_last($this->m_aItems, $mDefault);
	}

	/**
	 * Get the array with all the items contained in this container.
	 * 
	 * @return array<IContainerItem>
	 */
	public function getItems() {
		return $this->m_aItems;
	}
	
	/**
	 * Sort the items.
	 * You might need to call this if the data on which the sort function depends has changed.
	 * 
	 * @return boolean
	 */
	public function sort() {
		if(!$this->m_cbSort)
			return false;
		
		uasort($this->m_aItems, $this->m_cbSort);
		return true;
	}

	/**
	 * Check if the given item exists in the container.
	 * It will use the user-specified key to search in the container-array.
	 * If you have multiple instances with identical keys, you might want to $bStrict = true.
	 * 
	 * @see IContainerItem::getKeyForContainer()
	 * @param IContainerItem $oItem
	 * @param bool $bStrict Check if besides the key, the actual instances are equal.
	 */
	public function containsItem(IContainerItem $oItem, $bStrict = false) {
		$sKey = $oItem->getKeyForContainer($this);
		return isset($this->m_aItems[$sKey]) && ($bStrict || $this->m_aItems[$sKey] === $oItem);
	}

	/**
	 * Check if an item with the given key exists in the container.
	 * 
	 * @param mixed $mKey
	 */
	public function containsKey($mKey) {
		$mKey = ''.$mKey;
		return isset($this->m_aItems[$mKey]);
	}
	
	/**
	 * Try to add the given item to the container.
	 * Checks if no items with an equal key exists, and (if set) checks the verify-add callback.
	 * Call the addedToContainer method on $oItem.
	 * 
	 * If a sorting function is set, auto sorts the items after adding, but before the callback.
	 * 
	 * @see IContainerItem::addedToContainer()
	 * @param IContainerItem $oItem
	 * @return boolean
	 */
	public function addItem(IContainerItem $oItem) {
		$sKey = $oItem->getKeyForContainer($this);
		if(isset($this->m_aItems[$sKey]) || ($this->m_cbVerifyAdd && !call_user_func($this->m_cbVerifyAdd, $oItem)))
			return false;
		
		$this->m_aItems[$sKey] = $oItem;
		if($this->m_cbSort) {
			uasort($this->m_aItems, $this->m_cbSort);
		}
		$oItem->addedToContainer($this);
		return true;
	}
	
	/**
	 * Try to remove the given item from the container.
	 * Checks if an item with an equal key exists.
	 * Call the removedFromContainer method on $oItem.
	 * 
	 * @see IContainerItem::removedFromContainer()
	 * @param IContainerItem $oItem
	 * @return boolean
	 */
	public function removeItem(IContainerItem $oItem) {
		$sKey = $oItem->getKeyForContainer($this);
		if(!isset($this->m_aItems[$sKey])) 
			return false;
		
		unset($this->m_aItems[$sKey]);
		$oItem->removedFromContainer($this);
		return true;
	}
	
	/**
	 * Clear all items in this container.
	 * 
	 * @see IContainerItem::removedFromContainer()
	 */
	public function clear() {
		foreach($this->m_aItems as $oItem) {
			$oItem->removedFromContainer($this);
		}
		$this->m_aItems = array();
	}
}
