<?php namespace Watena\Libs\Base\Ipco;

class BuilderBlock extends Base {

    private $m_aSubBlocks = array();
    private $m_oParentBlock;
    private $m_aBuffer = array();
    private $m_sName;

    public function __construct($sName, IPCO $ipco) {
        parent::__construct($ipco);
        $this->m_sName = $sName;
    }

    public function __toString() {
        return 'public function region_' . $this->m_sName . '() {' . implode('', $this->m_aBuffer) . '}';
    }

    public function addToBuffer($sContent) {
        $this->m_aBuffer []= $sContent;
    }

    public function addSubBlock(BuilderBlock $oBlock) {
        $this->m_aSubBlocks []= $oBlock;
        $oBlock->m_oParentBlock = $this;
    }
}
