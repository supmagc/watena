<?php
require_plugin('DatabaseManager');

class Configuration extends Plugin {
	
	private $m_oTable;
	private $m_aConfig = array();
	
	public function make() {
		$oConnection = DatabaseManager::getConnection($this->getConfig('DATABASE_CONNECTION', 'default'));
		$this->m_oTable = $oConnection->getMultiTable($this->getConfig('DATABASE_TABLE', 'config'), array('category', 'key'));
	}
	
	public function init() {
		
	}
	
	public function setValue($sCategory, $sKey, $mValue) {
		$oStatement = $this->m_oTable->select(array($sCategory, $sKey));
		if($oStatement->rowCount() > 0) {
			$this->m_oTable->update(array('value' => serialize($mValue)), array($sCategory, $sKey));
		}
		else {
			$this->m_oTable->insert(array('category' => $sCategory, 'key' => $sKey, 'value' => serialize($mValue)));
		}
		$this->getWatena()->getCache()->set("CONF.$sCategory.$sKey", $mValue);
		array_assure($this->m_aConfig, array($sCategory, $sKey), $mValue);
	}
	
	public function getValue($sCategory, $sKey, $mDefault = null) {
		$mReturn = array_value($this->m_aConfig, array($sCategory, $sKey), $mDefault);
		if($mReturn === $mDefault) {
			$mReturn = $this->getWatena()->getCache()->get("CONF.$sCategory.$sKey", $mDefault);
		}
		if($mReturn === $mDefault) {
			$oStatement = $this->m_oTable->select(array($sCategory, $sKey));
			if(($oData = $oStatement->fetchObject()) !== false) {
				$mReturn = unserialize($oData->value);
				$this->getWatena()->getCache()->set("CONF.$sCategory.$sKey", $mReturn);
			}
		}
		array_assure($this->m_aConfig, array($sCategory, $sKey), $mReturn);
		return $mReturn;
	}
	
	public function getVersion() {
		return array(
			'major' => 0,
			'minor' => 1,
			'build' => 1,
			'state' => 'dev'
		);
	}
}

?>