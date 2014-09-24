<?php

abstract class AdminModuleContent extends Object {
	
	private $m_aParams;
	
	public abstract function generate(AdminModuleContentRequest $oRequest, AdminModuleContentResponse $oResponse);
	
	protected function __construct(array $aParams) {
		$this->m_aParams = $aParams;
	}
	
	public final function getParams() {
		return $this->m_aParams;
	}
	
	public static final function load($sType, $sData) {
		switch($sType) {
			case 'text' : return new AdminModuleContentText($sData);
			case 'eval' : return new AdminModuleContentEval($sData);
			case 'plugin' : return new AdminModuleContentPlugin($sData);
			case 'callback' : return new AdminModuleContentPlugin($sData);
			default : return new AdminModuleContentText($sData);
		}
	}
}

class AdminModuleContentText extends AdminModuleContent {
	
	private $m_sText;
	
	public function __construct($sData) {
		$this->m_sText = $sData;
	}
	
	public function generate(AdminModuleContentRequest $oRequest, AdminModuleContentResponse $oResponse) {
		$oResponse->setContentText($this->m_sText);
	}
}

class AdminModuleContentEval extends AdminModuleContent {

	private $m_sEval;
	
	public function __construct($sData) {
		$this->m_sEval = $sData;
	}
	
	public function generate(AdminModuleContentRequest $oRequest, AdminModuleContentResponse $oResponse) {
		ob_start();
		$sData = '' . eval($this->m_sEval);
		if(ob_get_length() > 0)
			$sData = ob_get_contents();
		ob_end_clean();
		$oResponse->setContentText($sData);
	}
}

class AdminModuleContentPlugin extends AdminModuleContent {

	private $m_sPlugin;
	
	public function __construct($sData) {
		$this->m_sPlugin = $sData;
	}
	
	public function generate(AdminModuleContentRequest $oRequest, AdminModuleContentResponse $oResponse) {
		$oPlugin = parent::getWatena()->getContext()->getPlugin($this->m_sPlugin, 'IAdminGeneratable');
		if(!empty($oPlugin))
			$oPlugin->generate($oRequest, $oResponse);
		else
			$oResponse->setError('AdminModule plugin not found', 'The requested content is supposed to be provided by '.$this->m_sPlugin.' but the plugin could not be found.');
	}
}

?>