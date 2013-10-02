<?php

class Mapping extends Object {
	
	private $m_aData;
	
	protected function __construct(array $aData = array()) {
		parent::__construct();
		$this->m_aData = $aData;
	}
	
	public function Matches(Filter $oFilter) {
		foreach($oFilter->getRules() as $oRule) {
			$mVariable = array_value($this->m_aData, $oRule->getVariable());
			switch($oRule->getCondition()) {
				case 'pattern': if(!Encoding::regMatch($oRule->getContent(), $mVariable)) return false; break;
				case 'null': if(null !== $mVariable) return false; break;
				case 'notnull': if(null === $mVariable) return false; break;
				case 'false': if($mVariable) return false; break;
				case 'true': if(!$mVariable) return false; break;
				case 'set': if(empty($mVariable)) return false; break;
				case 'notset': if(!empty($mVariable)) return false; break;
				case 'gt': 
				case 'greaterthan': if($oRule->getContent() >= $mVariable) return false; break;
				case 'lt': 
				case 'lessthan': 
				case 'lesserthan': if($oRule->getContent() <= $mVariable) return false; break;
				case 'eq': 
				case 'equal': 
				case 'equals': if($oRule->getContent() != $mVariable) return false; break;
				default: return false;
			}
		}
		return true;
	}
	
	public static function LoadFromRequest() {
		return new Mapping(array(
			'useragent' => Request::useragent(),
			'scheme' => Request::scheme(),
			'host' => Request::host(),
			'port' => Request::port(),
			'offset' => Request::offset(),
			'path' => Request::path(),
			'mapping' => Request::mapping(),
			'session' => $_SESSION,
			'cookie' => $_COOKIE,
			'post' => $_POST,
			'get' => $_GET
		));
	}
	
	public static function LoadFromUrl(Url $oUrl) {
		return new Mapping(array(
			'scheme' => $oUrl->getScheme(),
			'host' => $oUrl->getHost(),
			'port' => $oUrl->getPort(),
			'path' => $oUrl->getPath(),
			'get' => $oUrl->getParameters()
		));
	}
}

?>