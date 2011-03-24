<?php

/**
 * Chmod: original source file from martin at aarhof dot eu, and editted by Voet Jelle from ToMo-design
 * 
 * @author Voet Jelle [ToMo-design.be]
 * @version 1.0.0 RC1
 *
 */
class TMD_Chmod {
   
    private $m_Modes = array('owner' => 0 , 'group' => 0 , 'public' => 0);
   
    public function setOwnermodes($read,$write,$execute) {
        $this->m_Modes['owner'] = $this->setModes($read,$write,$execute);
    }
   
    public function setGroupmodes($read,$write,$execute) {
        $this->m_Modes['group'] = $this->setModes($read,$write,$execute);
    }

    public function setPublicmodes($read,$write,$execute) {
        $this->m_Modes['public'] = $this->setModes($read,$write,$execute);
    }
   
    public function getMode() {
        return 0 . $this->m_Modes['owner'] . $this->m_Modes['group'] . $this->m_Modes['public'];
    }
    
    public function getOwnermodes() {
    	return $this->getModes($this->m_Modes['owner']);
    }
    
    public function getGroupmodes() {
    	return $this->getModes($this->m_Modes['group']);
    }
        
    public function getPublicmodes() {
    	return $this->getModes($this->m_Modes['public']);
    }
    
    private function setModes($r,$w,$e) {
        $mode = 0;
        if($r) $mode+=4;
        if($w) $mode+=2;
        if($e) $mode+=1;
        return $mode;
    }
    
    private function getModes($mode) {
    	$return = array(false, false, false);
    	if($mode >= 4) {$return[0] = true; $mode -= 4;}
    	if($mode >= 2) {$return[1] = true; $mode -= 2;}
    	if($mode >= 1) {$return[2] = true;}
    	return $return;
    }
}
?>