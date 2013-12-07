<?php

class ResultModel extends Model implements IResult {

	private $m_oException;
	private $m_mResult;
	
	public final function setResult($mResult) {
		$this->m_mResult = $mResult;
	}
	
	public final function getResult() {
		return $this->m_mResult;
	}
	
	public final function hasException() {
		return !empty($this->m_oException);
	}
	
	public final function setException(Exception $oException) {
		$this->m_oException = $oException;
	}
	
	public final function getException() {
		return $this->m_oException;
	}
}

?>