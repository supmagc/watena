<?php

class UserManagerVerifiable extends DbObject {
	
	public function isVerified() {
		return (bool)$this->getDataValue('verified');
	}
	
	public function makeVerifier() {
		$sVerifier = md5(mt_rand() . $this->getId() . microtime());
		$this->setDataValue('verifier', $sVerifier);
		return $sVerifier;
	}
	
	public function verify($sVerifier) {
		if($sVerifier !== null && $sVerifier === $this->getDataValue('verifier', false)) {
			$this->setDataValue('verifier', null);
			$this->setDataValue('verified', 1);
			return true;
		}
		return false;
	}
	
	public function reset() {
		$this->setDataValue('verifier', null);
		$this->setDataValue('verified', 0);
	}
}

?>