
<?php
class IPCO_Compiled_source extends IPCO_Processor {

	public function __toString() {
		try {
			$_ob = '';
			$_ob .= 'This template is beeing parsed !
';
			if(parent::processMethod('getMPublic', array(parent::processMember('sPublic', null)), null)) {
				$_ob .= '
	within if
';
			} elseif(parent::processMember('0', parent::processMember('mPublic', parent::processMember('1', parent::processMember('mPublic', null))))) {
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
		catch(Exception $e) {
			return $e;
		}
	}
}
?>