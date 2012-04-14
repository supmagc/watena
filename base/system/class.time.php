<?php

class Time extends Object {
	
	const ZONE_UTC = 'UTC';
	
	private static $s_sTimezoneSystem;
	
	private $m_oTimestamp;
	private $m_oTimezone;
	
	public function __construct($nTimestamp, $sTimezone) {
		parent::__construct();
		$this->m_oTimezone = new DateTimeZone($sTimezone);
		$this->m_oTimestamp = new DateTime('@' . $nTimestamp, $this->m_oTimezone);
	}
	
	public function isTimezoneUtc() {
		return $this->getTimezone() == Time::ZONE_UTC;
	}
	
	public function isTimeZoneSystem() {
		return $this->getTimezone() == self::getSystemTimezone();
	}
	
	public function getTimezone() {
		return $this->m_oTimestamp->getTimezone()->getName();
	}
	
	public function getTimestamp() {
		return $this->m_oTimestamp->getTimestamp();
	}
	
	public function setTimezone($sTimezone) {
		$this->m_oTimestamp->setTimezone(new DateTimeZone($sTimezone));
	}
	
	public function setTimestamp($nTimestamp) {
		$this->m_oTimestamp->setTimestamp($nTimestamp);
	}
	
	public function setDate($nYear, $nMonth, $nDay) {
		$this->m_oTimestamp->setDate($nYear, $nMonth, $nDay);
	}
	
	public function setTime($nHour, $nMinutes, $nSeconds = null) {
		$this->m_oTimestamp->setTime($nHour, $nMinutes, $nSeconds);
	}
	
	public static function getSystemTimezone() {
		return self::$s_sTimezoneSystem;
	}
	
	public static function create($mTime) {
		return new Time(time(), self::getSystemTimezone());
	}
	
	public static function init($sTimezone) {
		self::$s_sTimezoneSystem = $sTimezone;
		ini_set('date.timezone', $sTimezone);
	}
}

?>