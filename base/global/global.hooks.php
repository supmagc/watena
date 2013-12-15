<?php

function RegisterComponent($sClass, $sRelativePath, $sPreferredLibrray = null) {
	watena()->getContext()->getComponentFactory()->registerComponent($sClass, $sRelativePath, $sPreferredLibrray);
}

function UnregisterComponent($sClass) {
	watena()->getContext()->getComponentFactory()->unregisterComponent($sClass);
}

?>