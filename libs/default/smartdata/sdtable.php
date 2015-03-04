<?php

class SdTable extends Object {
	
	private $m_aFields;

	public function addField(SdField $oField) {
		$this->m_aFields []= $oField;
	}
	
	public function verifyData(array $aData) {
		
	}
}
