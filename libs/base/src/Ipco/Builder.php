<?php namespace Watena\Libs\Base\Ipco;

// TODO: this file can be deleted ?

class Builder extends Base {
	
	private $m_oMainBlock;
	private $m_oCurrentBlock;
	private $m_sExtends = 'Compiled';
	private $m_aEndingStack = array();
	private $m_nForeachCount = 0;
	private $m_aForeachStack = array();
	private $m_aComponentStack = array();
	private $m_nLine;
	
	public function __construct(IPCO $ipco) {
		$this->m_oMainBlock = new BuilderBlock('MAIN', $ipco);
		$this->m_oCurrentBlock = $this->m_oMainBlock;
	}

	public function __toString() {
		
	}
	
	public function onBase($sName) {
		// TODO: this needs some work
		$this->m_sExtends = $sName;
	}
	
	public function onIf($sExpression) {
		array_push($this->m_aEndingStack, 'if');
		$this->m_oCurrentBlock->addToBuffer('if(' . new Expression($sExpression, parent::getIpco()) . ') {');
	}
	
	public function onElseif($sExpression) {
		$this->m_oCurrentBlock->addToBuffer('else if(' . new Expression($sExpression, parent::getIpco()) . ') {');
	}
	
	public function onElse() {
		$this->m_oCurrentBlock->addToBuffer('else {');
	}
	
	public function onWhile() {
		array_push($this->m_aEndExpected, 'while');
		$this->m_oCurrentBlock->addToBuffer('while(' . new Expression($sExpression, parent::getIpco()) . ') {');
	}
	
	public function onFor() {
		++$this->m_nForeachCount;
		array_push($this->m_aEndingStack, 'for');
		array_push($this->m_aForeachStack, $this->m_nForeachCount);
		$this->m_oCurrentBlock->addToBuffer('for(' . new Expression($sExpression, parent::getIpco()) . ' as $_feobj_'.$this->m_nForeachCount.') {');
		$this->m_oCurrentBlock->addToBuffer('parent::_addComponent($_feobj_'.$this->m_nForeachCount.');');
	}
	
	public function onTemplate($sExpression) {
		$this->m_oCurrentBlock->addToBuffer('$_ob .= parent::_addTemplate(\'' . new Expression($sExpression, parent::getIpco()) . '\');');
	}
	
	public function onComponent($sName) {
		
	}
	
	public function onBlock($sName) {
		$oBlock = new BuilderBlock($sName);
		$this->m_oCurrentBlock->addSubBlock($oBlock);
		$this->m_oCurrentBlock = $oBlock;
		array_push($this->m_aEndExpected, "block_$sName");
	}
	
	public function onEnd($sName) {
		$sExpected = array_pop($this->m_aEndingStack);
		if($sName) {
			if($sExpected === $sName) {
				if($sExpected === 'for') {
					$nCount = array_pop($this->m_aForeachStack);
					$this->m_oCurrentBlock->addToBuffer('parent::_removeComponent($_feobj_'.$nCount.');');
				}
				if($sExpected === 'component') {
					
				}
				die('implement this');
			}
		}
	}
}
