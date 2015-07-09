<?php namespace Watena\Core;

class Time extends Object {
	
	private static $s_sTimezoneSystem;
	private static $s_sDefaultFormat;
	
	private $m_oTimestamp;
	
	public function __construct($mTimestamp = 'now', $mTimezone = 'UTC') {
		$this->m_oTimestamp = new \DateTime(self::formatTimestamp($mTimestamp), new \DateTimeZone(self::formatTimezone($mTimezone)));
	}
	
	public function isTimezoneUtc() {
		return $this->getTimezone() == 'UTC';
	}
	
	public function isTimezoneSystem() {
		return $this->getTimezone() == self::getSystemTimezone();
	}
	
	public function getTimezone() {
		return $this->m_oTimestamp->getTimezone()->getName();
	}
	
	public function getTimestamp() {
		return $this->m_oTimestamp->getTimestamp();
	}
	
	public function getOffset() {
		return $this->m_oTimestamp->getTimezone()->getOffset($this->m_oTimestamp);
	}
	
	public function format($sFormat) {
		return $this->m_oTimestamp->format($sFormat);
	}
	
	public function formatDefault() {
		return $this->m_oTimestamp->format(self::getDefaultFormat());
	}
	
	public function formatSqlTimestamp() {
		return $this->format('Y-m-d H:i:s'); // Y-m-d H:i:s
	}
	
	public function formatSimple() {
		return $this->format('Y-m-d H:i:s'); // Y-m-d H:i:s
	}
	
	public function formatAtom() {
		return $this->format(\DateTime::ATOM); // Y-m-d\TH:i:sP
	}
	
	public function formatCookie() {
		return $this->format(\DateTime::COOKIE); // l, d-M-y H:i:s T
	}
	
	public function formatIso8601() {
		return $this->format(\DateTime::ISO8601); // Y-m-d\TH:i:sO
	}
	
	public function formatRfc822() {
		return $this->format(\DateTime::RFC822); // D, d M y H:i:s O
	}
	
	public function formatRfc850() {
		return $this->format(\DateTime::RFC850); // l, d-M-y H:i:s T
	}
	
	public function formatRfc1036() {
		return $this->format(\DateTime::RFC1036); // D, d M y H:i:s O
	}
	
	public function formatRfc1123() {
		return $this->format(\DateTime::RFC1123); // D, d M Y H:i:s O
	}
	
	public function formatRfc2822() {
		return $this->format(\DateTime::RFC2822); // D, d M Y H:i:s O
	}
	
	public function formatRfc3339() {
		return $this->format(\DateTime::RFC3339); // Y-m-d\TH:i:sP
	}
	
	public function formatRss() {
		return $this->format(\DateTime::RSS); // D, d M Y H:i:s O
	}
	
	public function formatW3c() {
		return $this->format(\DateTime::W3C); // Y-m-d\TH:i:sP
	}
	
	public function convert($sTimezone) {
		$oTime = new Time($this->formatSimple(), $this->getTimezone());
		$oTime->m_oTimestamp->setTimezone(new \DateTimeZone(self::formatTimezone($sTimezone)));
		return $oTime;
	}
	
	public function add(Interval $oInterval) {
		$oTime = new Time($this->formatSimple(), $this->getTimezone());
		$oTime->m_oTimestamp->add(new \DateInterval(sprintf('P%dY%dM%dDT%dH%dM%dS', $oInterval->getYears(), $oInterval->getMonths(), $oInterval->getDays(), $oInterval->getHours(), $oInterval->getMinutes(), $oInterval->getSeconds())));
		return $oTime;
	}
	
	public function subtract(Interval $oInterval) {
		$oTime = new Time($this->formatSimple(), $this->getTimezone());
		$oTime->m_oTimestamp->sub(new \DateInterval(sprintf('P%dY%dM%dDT%dH%dM%dS', $oInterval->getYears(), $oInterval->getMonths(), $oInterval->getDays(), $oInterval->getHours(), $oInterval->getMinutes(), $oInterval->getSeconds())));
		return $oTime;
	}
	
	public function difference(Time $oTime) {
		$oTimeInterval = $this->m_oTimestamp->diff($oTime->m_oTimestamp);
		NYI();
	}
	
	public static function getUtcTime() {
		return self::createUTCTime('now');
	}
	
	public static function getSystemTime() {
		return self::createSystemTime('now');
	}
	
	public static function getSystemTimezone() {
		return self::$s_sTimezoneSystem;
	}
	
	public static function getSystemOffset() {
		$oTimezone = new \DateTimeZone(self::$s_sTimezoneSystem);
		$oDateTime = new \DateTime('now', $oTimezone);
		return $oTimezone->getOffset($oDateTime);
	}
	
	/*
	public static function getTimestamp() {
		return time();
	}
	*/
	
	public static function getDefaultFormat() {
		return self::$s_sDefaultFormat;
	}

	public static function createUtcTime($mTime) {
		return new Time($mTime, 'UTC');
	}
	
	public static function createSystemTime($mTime) {
		return new Time($mTime, self::getSystemTimezone());
	}
	
	public static function init($sTimezone, $sDefaultFormat) {
		self::$s_sTimezoneSystem = $sTimezone;
		self::$s_sDefaultFormat = $sDefaultFormat;
		ini_set('date.timezone', $sTimezone);
	}
	
	public static function formatTimezone($mTimezone) {
		$mTimezoneUppercase = Encoding::toUpper($mTimezone);
		if(!$mTimezone) {
			return self::getSystemTimezone();
		}
		else if($mTimezoneUppercase == 'UTC' || $mTimezoneUppercase == 'GMT') {
			return 'UTC';
		}
		else if(is_numeric($mTimezone)) {
			if($mTimezone > 24 || $mTimezone < -24)
				$mTimezone /= 3600;
			$mTimezone %= 12;
			return $mTimezone ? 'Etc/GMT' . ($mTimezone < 0 ? '-' : '+') . abs($mTimezone) : 'UTC';
		}
		else if(Encoding::regMatch('^GMT[-+][0-9]*$', $mTimezoneUppercase)) {
			return "Etc/$mTimezone";
		}
		else if(!Encoding::indexOf($mTimezone, '/')) {
			$aZones = \DateTimeZone::listIdentifiers();
			foreach($aZones as $sZone) {
				if(Encoding::indexOf($sZone, "/$mTimezone", 0, true))
					return $sZone;
			}
		}
		return $mTimezone;
	}
	
	public static function formatTimestamp($mTimestamp) {
		if(is_numeric($mTimestamp)) return '@'.$mTimestamp;
		else return $mTimestamp;
	}
	
	public static function isValidTimezone($mTimezone) {
		return in_array($mTimezone, \DateTimeZone::listIdentifiers()) || Encoding::regMatch('^Etc/GMT[-+][0-9]*$', $mTimezone, 'i');
	}
}
