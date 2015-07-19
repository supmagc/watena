<?php
/**
 * Created by PhpStorm.
 * User: Jelle
 * Date: 19/07/2015
 * Time: 20:56
 */

namespace Watena\Core;


class Library extends Object {

    private $m_oPluginIndex;
    private $m_oModelIndex;
    private $m_oViewIndex;
    private $m_oControllerIndex;
    private $m_oThemeIndex;
    private $m_oFilesIndex;

    private $m_sLibraryName;
    private $m_sLibraryDirectory;
    private $m_sLibraryPath;

    public function __construct($sLibrary) {
        $this->m_sLibraryName = $sLibrary;
        $this->m_sLibraryDirectory = Encoding::toLower($this->m_sLibraryName);
        $this->m_sLibraryPath = realpath(PATH_LIBS . DIRECTORY_SEPARATOR . $this->m_sLibraryDirectory);
    }

    public function getName() {
        return $this->m_sLibraryName;
    }

    public function getDirectory() {
        return $this->m_sLibraryDirectory;
    }

    public function getPath() {
        return $this->m_sLibraryPath;
    }


}