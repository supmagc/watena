<?php

interface IResult {
	
	public function getResult();
	public function hasException();
	public function setException(Exception $oException);
	public function getException();
}

?>