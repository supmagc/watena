<?php

class ObjectUniquenessException extends WatCeption {

	private $m_sOjectUnique;
	
	public function __construct($sObjectUnique, $sMessage, Exception $oInnerException = null) {
		parent::__construct('{class} ObjectUnique error: '.$sMessage, array('class' => $sObjectUnique), null, $oInnerException);
		$this->m_sOjectUnique = $sObjectUnique;
	}
	
	public function getObjectUnique() {
		return $this->m_sOjectUnique;
	}
}
