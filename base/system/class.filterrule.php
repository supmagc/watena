<?php

class FilterRule extends Object {
	
	private $m_aVariable;
	private $m_sCondition;
	private $m_sContent;
	
	public function __construct(array $aVariable, $sCondition, $sContent) {
		$this->m_aVariable = $aVariable;
		$this->m_sCondition = $sCondition;
		$this->m_sContent = $sContent;
	}
	
	public final function getVariable() {
		return $this->m_aVariable;
	}
	
	public final function getCondition() {
		return $this->m_sCondition;
	}
	
	public final function getContent() {
		return $this->m_sContent;
	}
}

?>