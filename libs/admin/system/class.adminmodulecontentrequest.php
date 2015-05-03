<?php

class AdminModuleContentRequest extends Object {
	
	private $m_oTab;
	private $m_sAction;
	private $m_aData;
	private $m_aState;
	
	public function __construct(AdminModuleTab $oTab, $sAction, array $aData, array $aState) {
		$this->m_oTab = $oTab;
		$this->m_sAction = $sAction;
		$this->m_aData = $aData;
		$this->m_aState = $aState;
	}
	
	/**
	 * Get the AdminModuleTab associated with this request.
	 * 
	 * @return AdminModuleTab
	 */
	public final function getTab() {
		return $this->m_oTab;
	}

	/**
	 * Shorthand function for getTab()->getMapping().
	 * 
	 * @return string
	 */
	public final function getMapping() {
		return $this->m_oTab->getMapping();
	}
	
	public final function getAction() {
		return $this->m_sAction;
	}
	
	public final function getData() {
		return $this->m_aData;
	}
	
	public final function getState() {
		return $this->m_aState;
	}
}

?>