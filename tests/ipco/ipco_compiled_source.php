
<?php
class IPCO_Compiled_source extends IPCO_Processor {


	public function __construct() { }

	public function __toString() {
		$_ob = '';
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
	
		return $_ob;
	}
}
?>