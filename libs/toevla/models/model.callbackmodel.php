<?php

class CallbackModel extends Model {
	
	private $m_sHash;
	
	public function setHash($sHash) {
		$this->m_sHash = $sHash;
	}
	
	public function getHash() {
		return $this->m_sHash;
	}
}

?>