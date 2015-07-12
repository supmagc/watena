<?php namespace Watena\Core;
/**
 * Created by PhpStorm.
 * User: Jelle
 * Date: 11/07/2015
 * Time: 17:01
 */

class ClassLoader {

    private $m_sPrefix;
    private $m_sDirectory;
    private $m_nPrefixLength;

    public function __construct($sPrefix, $sDirectory) {
        $this->m_sPrefix = $sPrefix;
        $this->m_sDirectory = $sDirectory;
        $this->m_nPrefixLength = strlen($sPrefix);

        spl_autoload_register(array($this, 'loadClass'));
    }

    public function loadClass($sClassName) {

        echo "$sClassName $this->m_sPrefix<br />";

        if(strncmp($this->m_sPrefix, $sClassName, $this->m_nPrefixLength) !== 0)
            return;

        $sClassNameRelative = substr($sClassName, $this->m_nPrefixLength);
        $sClassPath = str_replace('\\', DIRECTORY_SEPARATOR, $this->m_sDirectory . $sClassNameRelative . '.php');

        if(!is_readable($sClassPath))
            return;

        require $sClassPath;
    }
}
