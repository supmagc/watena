<?php

abstract class AdminModuleContent extends Object {
	
	public abstract function generate(AdminModuleData $oData);
	
	public static function load($sType, $sData) {
		switch($sType) {
			case 'text' : return new AdminModuleContentText($sData);
			case 'function' : return new AdminModuleContentFunction($sData);
			case 'custom' : return new AdminModuleContentCustom($sData);
			default : return new AdminModuleContentText($sData);
		}
	}
}

class AdminModuleContentText extends AdminModuleContent {
	
	private $m_sText;
	
	public function __construct($sData) {
		$this->m_sText = $sData;
	}
	
	public function generate(AdminModuleData $oData) {
		$oData->setContentText($this->m_sText);
	}
}

class AdminModuleContentFunction extends AdminModuleContent {

	private $m_sFunction;
	
	public function __construct($sData) {
		$this->m_sFunction = $sData;
	}
	
	public function generate(AdminModuleData $oData) {
		if(function_exists($this->m_sFunction)) {
			ob_start();
			$sReturn = '' . call_user_func($this->m_sFunction);
			$sReturn .= ob_get_contents();
			ob_end_clean();
			return $sReturn;
		}
		else {
			return null;
		}
	}
}

class AdminModuleContentCustom extends AdminModuleContent {

	public function __construct($sData) {
	}
	
	public function generate(AdminModuleData $oData) {
		return null;
	}
}

?>