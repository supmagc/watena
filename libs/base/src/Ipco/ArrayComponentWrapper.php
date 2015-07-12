<?php namespace Watena\Libs\Base\Ipco;


class ArrayComponentWrapper extends ComponentWrapper {

    private $m_aComponent;

    public function __construct($mComponent, IPCO $oIpco) {
        parent::__construct($oIpco);

        $this->m_aComponent = $mComponent;
    }

    public  function tryGetProperty(&$mValue, $sName, $bFirstCall = true) {
        if(array_key_exists($sName, $this->m_aComponent)) {
            $mValue = $this->m_aComponent[$sName];
            return true;
        }
        else {
            return $bFirstCall ? self::tryGetMethod($mValue, $sName, array(), false) : false;
        }
    }

    public function tryGetMethod(&$mValue, $sName, array $aParams, $bFirstCall = true) {
        return false;
    }

    public function getFirst() {
        return array_first($this->m_aComponent);
    }

    public function getLast() {
        return array_last($this->m_aComponent);
    }

    public function current() {
        return current($this->m_aComponent);
    }

    public function key() {
        return key($this->m_aComponent);
    }

    public function next() {
        return next($this->m_aComponent);
    }

    public function rewind() {
        return reset($this->m_aComponent);
    }

    public function valid() {
        return $this->current();
    }
}