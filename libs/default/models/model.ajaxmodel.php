<?php

class AjaxModel extends Model {
	
	public final function alert($sMessage) {
		echo "alert(decodeURIComponent('".rawurlencode($sMessage)."'));\n";
	}
}

?>