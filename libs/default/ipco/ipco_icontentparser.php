<?php

interface IPCO_IContentParser {

	/**
	 * The is the main callback method that provides you the possibility 
	 * to implement some custom emplating stuff.
	 * If desired, you can modify the content by reference.
	 * If desired you can return an array of IPCO_ContentParserPart-instances.
	 * Each of these instances represents a function call with a defines set of parameters.
	 * 
	 * @param string $sContent
	 * @return array (optional)
	 */
	public function parseContent(&$sContent);
}

?>