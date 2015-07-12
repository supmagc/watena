<?php namespace Watena\Libs\Base;

class STable_Skeleton {
	
	private $m_sIdentifier = '';
	private $m_bSelectable = false;
	private	$m_bColorize = true;
	private $m_aFields = array();

	public function __construct() {
		$this->m_sIdentifier = time() % rand(10, 20);
	}
	
	public function addField(STable_Field $oField) {
		
	}
	
	public function getLoader() {
		
	}
}

?>