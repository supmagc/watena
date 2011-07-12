<?php

class IPCO_Exception extends Exception {

	const UNKNOWN = 0;
	const INVALIDCOMPONENTTYPE = 1;
	const INVALIDEXPRESSION = 2;
	const INVALIDNESTING = 3;
	const TEMPLATETOFILE_UNCALLABLE = 4;
	const TEMPLATETOFILE_INVALID_FILE = 5;
}

?>