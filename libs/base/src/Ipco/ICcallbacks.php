<?php namespace Watena\Libs\Base\Ipco;

interface ICallbacks {
	
	public function getFilePathForTemplate($sTemplate);
	public function getTemplateClassForFilePath($sFilePath);
}
