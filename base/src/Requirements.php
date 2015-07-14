<?php namespace Watena\Core;

class Requirements {

    const ERROR_EXTENSIONNOTFOUND = 1;
    const ERROR_EXTENSIONUNLOADABLE = 2;
    const ERROR_PLUGINUNLOADABLE = 3;
    const ERROR_PEARUNLOADABLE = 4;
    const ERROR_INCLUDENOTFOUND = 5;
    const ERROR_INCLUDEONCENOTFOUND = 6;
    const ERROR_FILENOTFOUND = 7;
    const ERROR_DIRECTORYNOTFOUND = 8;
    const ERROR_CONSTANTUNDEFINED = 9;
    const ERROR_LIBRARYNOTFOUND = 10;
    const ERROR_DATAFILENOTFOUND = 11;
    const ERROR_DATADIRECTORYNOTFOUND = 12;
    const ERROR_MODELNOTFOUND = 13;
    const ERROR_MODELUNLOADABLE = 14;
    const ERROR_VIEWNOTFOUND = 15;
    const ERROR_VIEWUNLOADABLE = 16;
    const ERROR_CONTROLLERNOTFOUND = 17;
    const ERROR_CONTROLLERUNLOADABLE = 18;

    /**
     * Trigger an error.
     *
     * @param int $nCode
     * @param string $sName
     * @return bool
     */
    private static function setError($nCode, $sName) {
        $sMessage = 'Requirement error occured';
        switch($nCode) {
            case ERROR_EXTENSIONNOTFOUND : $sMessage = 'The required extension \'{name}\' was not loaded with you php-build.'; break;
            case ERROR_EXTENSIONUNLOADABLE : $sMessage = 'The required extension \'{name}\' could not be dynamically loaded.'; break;
            case ERROR_PLUGINUNLOADABLE : $sMessage = 'The required Watena-plugin \'{name}\' could not be loaded.'; break;
            case ERROR_PEARUNLOADABLE : $sMessage = 'The required PEAR-plugin \'{name}\' could not be loaded.'; break;
            case ERROR_INCLUDENOTFOUND : $sMessage = 'The required include-file \'{name}\' does not exist.'; break;
            case ERROR_INCLUDEONCENOTFOUND : $sMessage = 'The required include-once-file \'{name}\' does not exist.'; break;
            case ERROR_FILENOTFOUND : $sMessage = 'The required file \'{name}\' does not exist.'; break;
            case ERROR_DIRECTORYNOTFOUND : $sMessage = 'The required directory \'{name}\' does not exist.'; break;
            case ERROR_CONSTANTUNDEFINED : $sMessage = 'The required constant \'{name}\' was not defined.'; break;
            case ERROR_LIBRARYNOTFOUND : $sMessage = 'The required library \'{name}\' does not exists.'; break;
            case ERROR_DATAFILENOTFOUND : $sMessage = 'The required data-file \'{name}\' does not exists.'; break;
            case ERROR_DATADIRECTORYNOTFOUND : $sMessage = 'The required data-directory \'{name}\' does not exists.'; break;
            case ERROR_MODELNOTFOUND : $sMessage = 'The required model \'{name}\' could not be found in any of the libraries.'; break;
            case ERROR_MODELUNLOADABLE : $sMessage = 'A file matching the required model \'{name}\' exists, but no class could be loaded.'; break;
            case ERROR_VIEWNOTFOUND : $sMessage = 'The required view \'{name}\' could not be found in any of the libraries.'; break;
            case ERROR_VIEWUNLOADABLE : $sMessage = 'A file matching the required view \'{name}\' exists, but no class could be loaded.'; break;
            case ERROR_CONTROLLERNOTFOUND : $sMessage = 'The required controller \'{name}\' could not be found in any of the libraries.'; break;
            case ERROR_CONTROLLERUNLOADABLE : $sMessage = 'A file matching the required cntroller \'{name}\' exists, but no class could be loaded.'; break;
        }
        self::getLogger()->error($sMessage, array('code' => $nCode, 'name' => $sName));
        return false;
    }

    /**
     * Get the logger for the requirements.
     *
     * @return Logger
     */
    private static function getLogger() {
        return Logger::getInstance('Requirement');
    }

    /**
     * Check if the extension is loaded.
     * If not, try to auto-load it.
     *
     * @param string|string[] $mName
     * @return bool
     */
    public static function extensionLoaded($mName) {
        if(is_array($mName)) return array_all(array(self, 'extensionLoaded'), $mName);
        else {
            if(!extension_loaded($mName)) {
                if(function_exists('dl')) if(!@dl($mName)) return self::setError(ERROR_EXTENSIONUNLOADABLE, $mName);
                else return self::setError(ERROR_EXTENSIONNOTFOUND, $mName);
            }
            return true;
        }
    }

    /**
     * Check if the plugin is loaded.
     * If not, try to auto-load it.
     *
     * @param string|string[] $mName
     * @return bool
     */
    public static function pluginLoaded($mName) {
        if(is_array($mName)) return array_all(array(self, 'pluginLoaded'), $mName);
        else return watena()->getContext()->loadPlugin($mName) || self::setError(ERROR_PLUGINUNLOADABLE, $mName);
    }

    /**
     * Check if pear and the given extension are included.
     * If not, try to auto-include them.
     *
     * @param string|string[] $mName
     * @return bool
     */
    public static function pearLoaded($mName) {
        if(is_array($mName)) return array_all(array(self, 'pearLoaded'), $mName);
        else {
            $nOld = error_reporting(E_ERROR);
            $bReturn = @include_once('PEAR.php') && @include_once($mName.'.php');
            error_reporting($nOld);
            return $bReturn || self::setError(ERROR_PEARUNLOADABLE, $mName);
        }
    }

    /**
     * Check if the file exists.
     *
     * @param string|string[] $mName
     * @return bool
     */
    public static function fileExists($mName) {
        if(is_array($mName)) return array_all(array(self, 'fileExists'), $mName);
        else return is_file($mName) || self::setError(ERROR_FILENOTFOUND, $mName);
    }

    /**
     * Check if the file directory.
     *
     * @param string|string[] $mName
     * @return bool
     */
    public static function directoryExists($mName) {
        if(is_array($mName)) return array_all(array(self, 'directoryExists'), $mName);
        else return is_dir($mName) || self::setError(ERROR_DIRECTORYNOTFOUND, $mName);
    }

    /**
     * Check if the file exists in the data-directory exists.
     *
     * @param string|string[] $mName
     * @return bool
     */
    public static function dataFileExists($mName) {
        if(is_array($mName)) return array_all(array(self, 'dataFileExists'), $mName);
        else return is_file(PATH_DATA . '/' . $mName) || self::setError(ERROR_DATAFILENOTFOUND, $mName);
    }

    /**
     * Check if the directory exists in the data-directory exists.
     *
     * @param string|string[] $mName
     * @return bool
     */
    public static function dataDirectoryExists($mName) {
        if(is_array($mName)) return array_all(array(self, 'dataDirectoryExists'), $mName);
        else return is_dir(PATH_DATA . '/' . $mName) || self::setError(ERROR_DATADIRECTORYNOTFOUND, $mName);
    }

    /**
     * Check if the constant is defined.
     *
     * @param string|string[] $mName
     * @return bool
     */
    public static function constantDefined($mName) {
        if(is_array($mName)) return array_all(array(self, constantDefined), $mName);
        else return defined($mName) || self::setError(ERROR_CONSTANTUNDEFINED, $mName);
    }
}