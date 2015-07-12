<?php namespace Watena\Libs\Base\Ipco;


class ObjectComponentWrapper extends ComponentWrapper {

    private $m_aProperties = array();
    private $m_aMethods = array();
    private $m_oComponent = null;
    private $m_oReflector = null;

    public function __construct($mComponent, IPCO $oIpco) {
        parent::__construct($oIpco);
        $this->m_aMethods = get_class_methods($mComponent);
        $this->m_oComponent = $mComponent;
        $this->m_oReflector = new ReflectionClass($mComponent);
    }

    public function tryGetProperty(&$mValue, $sName, $bFirstCall = true) {
        if(!array_key_exists($sName, $this->m_aProperties)) {
            $oProperty = $this->m_oReflector->hasProperty($sName) ? $this->m_oReflector->getProperty($sName) : false;
            $this->m_aProperties[$sName] = $oProperty && $oProperty->isPublic() ? $oProperty : false;
        }
        if(($oProperty = $this->m_aProperties[$sName]) !== false) {
            $mValue = $oProperty->getValue($this->m_oComponent);
            return true;
        }
        else {
            return $bFirstCall ? self::tryGetMethod($mValue, $sName, array(), false) : false;
        }
    }

    public function tryGetMethod(&$mValue, $sName, array $aParams, $bFirstCall = true) {
        if(in_array($sName, $this->m_aMethods)) {
            $mValue = call_user_func_array(array($this->m_oComponent, $sName), $aParams);
            return true;
        }
        else {
            return $bFirstCall && count($aParams) == 0 ? self::tryGetProperty($mValue, $sName, false) : false;
        }
    }

    public function getFirst() {
        if($this->m_oComponent instanceof Iterator) {
            $this->m_oComponent->rewind();
            return $this->m_oComponent->valid() ? $this->m_oComponent->current() : false;
        }
        return false;
    }

    public function getLast() {
        if($this->m_oComponent instanceof Iterator) {
            $mReturn = false;
            while($this->m_oComponent->next()) {
                $mReturn = $this->m_oComponent->current();
            }
            return $mReturn;
        }
        return false;
    }

    public function current() {
        return $this->m_oComponent instanceof Iterator ? $this->m_oComponent->current() : false;
    }

    public function key() {
        return $this->m_oComponent instanceof Iterator ? $this->m_oComponent->key() : false;
    }

    public function next() {
        return $this->m_oComponent instanceof Iterator ? $this->m_oComponent->next() : false;
    }

    public function rewind() {
        return $this->m_oComponent instanceof Iterator ? $this->m_oComponent->rewind() : false;
    }

    public function valid() {
        return $this->m_oComponent instanceof Iterator ? $this->m_oComponent->valid() : false;
    }
}