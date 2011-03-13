
<?php
class IPCO_Compiled_source extends IPCO_Processor {

	private $m_sOutput;

	public function __construct() {
		$_ob = '';
		$_comp = null;
		$_ob .= 'This template is beeing parsed !
';
		if(parent::processMember('variable', null)) {
			$_ob .= '
	within if
';
		} elseif(parent::processMember('variableNot', null)) {
			$_ob .= '
	within elseif
';
		} else {
			$_ob .= '
	within else
';
		}

		$this->m_sOutput = $_ob;
	}
	
	public function __toString() {
		return $this->m_sOutput;
	}
?>