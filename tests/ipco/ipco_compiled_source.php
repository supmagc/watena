
<?php
class IPCO_Compiled_source extends IPCO_Processor {

	public function __construct() {
		$_ob = '';
		$_comp = null;
		$_ob .= 'This template is beeing parsed !';
		if(parent::processMethod('isPartOneValid', array(parent::processSlices(array(0), parent::processMember('name', null))), null)) {
			$_ob .= 'within if';
		}

		echo $_ob;
	}
}
?>