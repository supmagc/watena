<?php namespace Watena\Libs\Base;

class UserManagerVerifiable extends DbObject {
	
	public function isVerified() {
		return (bool)$this->getDataValue('verified');
	}
	
	public function makeVerifier() {
		$sVerifier = md5(mt_rand() . $this->getId() . microtime());
		if($this->setDataValue('verifier', $sVerifier))
			return $sVerifier;
		else
			return false;
	}
	
	public function verify($sVerifier) {
		if($sVerifier && $sVerifier === $this->getDataValue('verifier', false)) {
			$this->setDataValue('verifier', null);
			$this->setDataValue('verified', 1);
			return true;
		}
		return false;
	}
	
	public function resetVerifier() {
		$this->setDataValue('verified', 0);
		$this->setDataValue('verifier', null);
	}
}
