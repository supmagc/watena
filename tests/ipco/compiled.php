
<?php
class _Source_CV extends IPCO_Compiled {

	public function __construct() {
		$_ob = '';
		$_comp = null;
		$_ob .= '';
		$_ob .= '';		while(false) {
		$_ob .= 'within while';
		$_ob .= '';		if(true) {
		$_ob .= 'within if';		}
		elseif(true) {
		$_ob .= 'within elseif';		}
		else {
		$_ob .= 'within else';		}
		$_ob .= '';		}
		$_ob .= '';
		foreach(array() as $a) {
		$_ob .= 'within foreach';		}
		$_ob .= '';
		$_ob .= '';

		echo $_ob;
	}
}
?>