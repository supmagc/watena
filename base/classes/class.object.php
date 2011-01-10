<?php

class Object {
	
	private static $s_oSingleton;
	private $m_aConfig;
	
	protected function __construct(array $aConfig = array()) {
		if(get_class($this) == "Watena") self::$s_oSingleton = $this;
		$this->m_aConfig = $aConfig;
		$sHostKey = strtoupper($_SERVER['HTTP_HOST']);
		if(isset($this->m_aConfig[$sHostKey])) $this->m_aConfig = array_merge($this->m_aConfig, $this->m_aConfig[$sHostKey]);
	}

	public final function getConfig($sKey, $mDefault = null) {
		return isset($this->m_aConfig[$sKey]) ? $this->m_aConfig[$sKey] : $mDefault;
	}
	
	protected final function terminate($sMessage) {
		die($sMessage);
	}

	/**
	 * @return Watena
	 */
	public final function getWatena() {
		return self::$s_oSingleton;
	}

	public function toString() {
		return get_class($this);
	}
	
	public final function __toString() {
		return $this->toString();
	}
}

?>