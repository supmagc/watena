<?php namespace Watena\Core;

class WatenaConfig {

	const CONFIGNAME_DEFAULT = 'default';

	private $m_aConfig;
	private $m_sConfigName;
	
	private static $s_aConfig = array(
		'libraries' => array(),
		'charset' => 'UTF-8',
		'timezone' => 'UTC',
		'timeformat' => 'Y/m/d H:i:s',
		'webroot' => 'watena',
		'cachengine' => 'CacheMemcache',
		'cachexpiration' => 30,
		'loglevel' => 'WARNING',
		'logprocessors' => array(),
		'debugdefault' => false,
		'debugdefine' => false,
		'debugsession' => false,
		'debugrequest' => false,
		'compression' => true
	);
	
	public final function __construct(array $aConfig, $sConfigName = self::CONFIGNAME_DEFAULT) {
		$this->m_sConfigName = $sConfigName;
		
		$this->m_aConfig = self::$s_aConfig;
		if(isset($aConfig[self::CONFIGNAME_DEFAULT]) && is_array($aConfig[self::CONFIGNAME_DEFAULT])) {
			$this->m_aConfig = array_merge($this->m_aConfig, $aConfig[self::CONFIGNAME_DEFAULT]);
		}
		if(isset($aConfig[$sConfigName]) && is_array($aConfig[$sConfigName])) {
			$this->m_aConfig = array_merge($this->m_aConfig, $aConfig[$sConfigName]);
		}
	}
	
	public final function config() {
		return $this->m_aConfig;
	}
	
	public final function configName() {
		return $this->m_sConfigName;
	}
	
	public function libraries() {
		return $this->m_aConfig['libraries'];
	}
	
	public function charset() {
		return $this->m_aConfig['charset'];
	}
	
	public function timeZone() {
		return $this->m_aConfig['timezone'];		
	}
	
	public function timeFormat() {
		return $this->m_aConfig['timeformat'];		
	}

	public function webRoot() {
		return $this->m_aConfig['webroot'];
	}

	public function domain() {
		return $this->m_aConfig['domain'];
	}
	
	public function cacheEngine() {
		return $this->m_aConfig['cachengine'];		
	}
	
	public function cacheExpiration() {
		return $this->m_aConfig['cachexpiration'];		
	}
	
	public function loggerLevel() {
		return $this->m_aConfig['loglevel'];		
	}
	
	public function loggerProcessors() {
		return $this->m_aConfig['logprocessors'];		
	}

	public function debugDefault() {
		return $this->m_aConfig['debugdefault'];
	}
	
	public function debugDefine() {
		return $this->m_aConfig['debugdefine'];
	}
	
	public function debugSession() {
		return $this->m_aConfig['debugsession'];
	}
	
	public function debugRequest() {
		return $this->m_aConfig['debugrequest'];
	}
	
	public function compression() {
		return $this->m_aConfig['compression'];
	}
}
