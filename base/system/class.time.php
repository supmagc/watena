<?php

class Time extends Object {
	
	const ZONE_UTC = 'UTC';
	
	private static $s_sTimezoneSystem;
	
	private $m_nTimestamp;
	private $m_sTimezone;
	
	public function __construct($nTimestamp, $sTimeZone) {
		parent::__construct();
		$this->m_nTimestamp = $nTimestamp;
		$this->m_sTimezone = $sTimeZone;
	}
	
	public function isTimezoneUtc() {
		return $this->getTimezone() == Time::ZONE_UTC;
	}
	
	public function isTimeZoneSystem() {
		return $this->getTimezone() == self::getTimezoneSystem();
	}
	
	public function getTimezone() {
		return $this->m_sTimezone;
	}
	
	public function getTimestamp() {
		return $this->m_nTimestamp;
	}
	
	public function getTimestampSystem() {
		return $this->isTimeZoneSystem() ? $this->getTimestampLocal() : 0;
	}
	
	public function getTimestampUtc() {
		return $this->isTimezoneUtc() ? $this->getTimestampLocal() : 0;
	}
	
	public static function getTimezoneSystem() {
		return self::$s_sTimezoneSystem;
	}
	
	public static function init($sTimezone) {
		self::$s_sTimezoneSystem = $sTimezone;
		ini_set('date.timezone', $sTimezone);
	}
}

?>