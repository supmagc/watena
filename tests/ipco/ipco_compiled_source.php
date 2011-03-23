
<?php
class IPCO_Compiled_source extends IPCO_Processor {

	public function __toString() {
		try {
			$_ob = '';
			$_ob .= 'This template is beeing parsed !
';
			if(parent::processMethod('getMPublic', array(parent::processMember('sPublic', null)), null)) {
				$_ob .= 'within if
';
			} else if(parent::processMember(0, parent::processMember('mPublic', parent::processMember(1, parent::processMember('mPublic', parent::processMember('', null)))))) {
				$_ob .= 'within elseif
';
			} else {
				$_ob .= 'within else
';
			}
			$_ob .= '
';
			foreach(parent::processMember('getForeach', null) as $_comp) {parent::componentPush($_comp);
				$_ob .= '<p>';
				$_ob .= parent::processMember('value', null);
				$_ob .= '</p>';
			parent::componentPop();}
			$_ob .= '
';
			$_ob .= parent::processMember('TEXT', null);

			return $_ob;
		}
		catch(Exception $e) {
			return $e;
		}
	}
}
?>