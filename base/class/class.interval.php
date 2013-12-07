<?php

class Interval extends Object {
	
	private $m_nYears;
	private $m_nMonts;
	private $m_nDays;
	private $m_nHours;
	private $m_nMinutes;
	private $m_nSeconds;
	
	public function __construct($nYears, $nMonths, $nDays, $nHours, $nMinutes, $nSeconds) {
		$this->m_nYears = (int)$nYears;
		$this->m_nMonts = (int)$nMonths;
		$this->m_nDays = (int)$nDays;
		$this->m_nHours = (int)$nHours;
		$this->m_nMinutes = (int)$nMinutes;
		$this->m_nSeconds = (int)$nSeconds;
	}
	
	public function getYears() {
		return $this->m_nYears;
	}
	
	public function getMonths() {
		return $this->m_nMonts;
	}
	
	public function getDays() {
		return $this->m_nDays;
	}
	
	public function getHours() {
		return $this->m_nHours;
	}
	
	public function getMinutes() {
		return $this->m_nMinutes;
	}
	
	public function getSeconds() {
		return $this->m_nSeconds;
	}
}

?>