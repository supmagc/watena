<?php

define('REQERROR_EXTENSIONNOTFOUND', 1);
define('REQERROR_EXTENSIONUNLOADABLE', 2);
define('REQERROR_PLUGINUNLOADABLE', 3);
define('REQERROR_PEARUNLOADABLE', 4);
define('REQERROR_INCLUDENOTFOUND', 5);
define('REQERROR_INCLUDEONCENOTFOUND', 6);
define('REQERROR_FILENOTFOUND', 7);
define('REQERROR_DIRECTORYNOTFOUND', 8);
define('REQERROR_CONSTANTUNDEFINED', 9);
define('REQERROR_LIBRARYNOTFOUND', 10);
define('REQERROR_DATAFILENOTFOUND', 11);
define('REQERROR_DATADIRECTORYNOTFOUND', 12);
define('REQERROR_MODELNOTFOUND', 13);
define('REQERROR_MODELUNLOADABLE', 14);
define('REQERROR_VIEWNOTFOUND', 15);
define('REQERROR_VIEWUNLOADABLE', 16);
define('REQERROR_CONTROLLERNOTFOUND', 17);
define('REQERROR_CONTROLLERUNLOADABLE', 18);

function require_error($nCode, $sName) {
	$sMessage = 'Requirement error occured';
	switch($nCode) {
		case REQERROR_EXTENSIONNOTFOUND : $sMessage = 'The required extension \'{name}\' was not loaded with you php-build.'; break;
		case REQERROR_EXTENSIONUNLOADABLE : $sMessage = 'The required extension \'{name}\' could not be dynamically loaded.'; break;
		case REQERROR_PLUGINUNLOADABLE : $sMessage = 'The required Watena-plugin \'{name}\' could not be loaded.'; break;
		case REQERROR_PEARUNLOADABLE : $sMessage = 'The required PEAR-plugin \'{name}\' could not be loaded.'; break;
		case REQERROR_INCLUDENOTFOUND : $sMessage = 'The required include-file \'{name}\' does not exist.'; break;
		case REQERROR_INCLUDEONCENOTFOUND : $sMessage = 'The required include-once-file \'{name}\' does not exist.'; break;
		case REQERROR_FILENOTFOUND : $sMessage = 'The required file \'{name}\' does not exist.'; break;
		case REQERROR_DIRECTORYNOTFOUND : $sMessage = 'The required directory \'{name}\' does not exist.'; break;
		case REQERROR_CONSTANTUNDEFINED : $sMessage = 'The required constant \'{name}\' was not defined.'; break;
		case REQERROR_LIBRARYNOTFOUND : $sMessage = 'The required library \'{name}\' does not exists.'; break;
		case REQERROR_DATAFILENOTFOUND : $sMessage = 'The required data-file \'{name}\' does not exists.'; break;
		case REQERROR_DATADIRECTORYNOTFOUND : $sMessage = 'The required data-directory \'{name}\' does not exists.'; break;
		case REQERROR_MODELNOTFOUND : $sMessage = 'The required model \'{name}\' could not be found in any of the libraries.'; break;
		case REQERROR_MODELUNLOADABLE : $sMessage = 'A file matching the required model \'{name}\' exists, but no class could be loaded.'; break;
		case REQERROR_VIEWNOTFOUND : $sMessage = 'The required view \'{name}\' could not be found in any of the libraries.'; break;
		case REQERROR_VIEWUNLOADABLE : $sMessage = 'A file matching the required view \'{name}\' exists, but no class could be loaded.'; break;
		case REQERROR_CONTROLLERNOTFOUND : $sMessage = 'The required controller \'{name}\' could not be found in any of the libraries.'; break;
		case REQERROR_CONTROLLERUNLOADABLE : $sMessage = 'A file matching the required cntroller \'{name}\' exists, but no class could be loaded.'; break;
	}
	require_logger()->error($sMessage, array('code' => $nCode, 'name' => $sName));
	return false;
}

function require_logger() {
	return Logger::getInstance('Requirement');
}

function require_extension($mName) {
	if(is_array($mName)) return array_all('require_extension', $mName);
	else {
		if(!extension_loaded($mName)) {
			if(function_exists('dl')) if(!@dl($mName)) return require_error(REQERROR_EXTENSIONUNLOADABLE, $mName);
			else return require_error(REQERROR_EXTENSIONNOTFOUND, $mName);
		}
		return true;
	}
}

function require_plugin($mName) {
	if(is_array($mName)) return array_all('require_plugin', $mName);
	else return watena()->getContext()->loadPlugin($mName) || require_error(REQERROR_PLUGINUNLOADABLE, $mName);
}

function require_pear($mName) {
	if(is_array($mName)) return array_all('require_pear', $mName);
	else {
		$nOld = error_reporting(E_ERROR);
		$bReturn = @include_once('PEAR.php') && @include_once($mName.'.php');
		error_reporting($nOld);
		return $bReturn || require_error(REQERROR_PEARUNLOADABLE, $mName);
	}
}

function require_include($mName) {
	if(is_array($mName)) return array_all('require_include', $mName);
	else return (is_file($mName) && include($mName)) || require_error(REQERROR_INCLUDENOTFOUND, $mName);
}

function require_includeonce($mName) {
	if(is_array($mName)) return array_all('require_include', $mName);
	else {
		return (is_file($mName) && include_once($mName)) || require_error(REQERROR_INCLUDEONCENOTFOUND, $mName);
	}
}

function require_file($mName) {
	if(is_array($mName)) return array_all('require_file', $mName);	
	else return is_file($mName) || require_error(REQERROR_FILENOTFOUND, $mName);
}

function require_directory($mName) {
	if(is_array($mName)) return array_all('require_directory', $mName);
	else return is_dir($mName) || require_error(REQERROR_DIRECTORYNOTFOUND, $mName);
}

function require_constant($mName) {
	if(is_array($mName)) return array_all('require_define', $mName);
	else return defined($mName) || require_error(REQERROR_CONSTANTUNDEFINED);
}

function require_library($mName) {
	if(is_array($mName)) return array_all('require_library', $mName);
	else return file_exists(PATH_LIBS . '/' . $mName) || require_error(REQERROR_LIBRARYNOTFOUND, $mName);
}

function require_datafile($mName) {
	if(is_array($mName)) return array_all('require_data', $mName);
	else return is_file(PATH_DATA . '/' . $mName) || require_error(REQERROR_DATAFILENOTFOUND, $mName);
}

function require_datadirectory($mName) {
	if(is_array($mName)) return array_all('require_data', $mName);
	else return is_dir(PATH_DATA . '/' . $mName) || require_error(REQERROR_DATADIRECTORYNOTFOUND, $mName);
}

function require_model($mName) {
	if(is_array($mName)) return array_all('require_model', $mName);
	else {
		if($sPath = watena()->getContext()->getLibraryFilePath('models', 'model.'.Encoding::toLower($mName).'.php')) {
			include_once $sPath;
			return class_exists($mName) || require_error(REQERROR_MODELUNLOADABLE, $mName);
		}
		else return require_error(REQERROR_MODELNOTFOUND, $mName);
	}
}

function require_view($mName) {
	if(is_array($mName)) return array_all('require_view', $mName);
	else {
		if($sPath = watena()->getContext()->getLibraryFilePath('views', 'view.'.Encoding::toLower($mName).'.php')) {
			@include_once $sPath;
			return class_exists($mName) || require_error(REQERROR_VIEWUNLOADABLE, $mName);
		}
		else return require_error(REQERROR_VIEWNOTFOUND, $mName);
	}
}

function require_controller($mName) {
	if(is_array($mName)) return array_all('require_controller', $mName);
	else {
		if($sPath = watena()->getContext()->getLibraryFilePath('controllers', 'controller.'.Encoding::toLower($mName).'.php')) {
			@include_once $sPath;
			return class_exists($mName) || require_error(REQERROR_CONTROLLERUNLOADABLE, $mName);
		}
		else return require_error(REQERROR_CONTROLLERNOTFOUND, $mName);
	}
}
