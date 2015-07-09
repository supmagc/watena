<?php namespace Watena\Core;

function registerComponent($sClass, $sRelativePath, $sPreferredLibrray = null) {
	watena()->getContext()->getComponentFactory()->registerComponent($sClass, $sRelativePath, $sPreferredLibrray);
}
