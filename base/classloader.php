<?php namespace Watena\Core;

/**
 * Class ClassLoader
 *
 * @author Jelle Voet
 * @version 0.1.0
 * @package Watena\Core
 */
class ClassLoader {

    /** @var string */
    private $m_sPrefix;
    /** @var string */
    private $m_sDirectory;
    /** @var int */
    private $m_nPrefixLength;

    /**
     * Create a new class auto-loader.
     * All classes beginning with $sPrefix will be searched in $sDirectory.
     *
     * @param string $sPrefix
     * @param string $sDirectory
     */
    public function __construct($sPrefix, $sDirectory) {
        $this->m_sPrefix = $sPrefix;
        $this->m_sDirectory = $sDirectory;
        $this->m_nPrefixLength = strlen($sPrefix);

        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Try to load the class $sClassName.
     *
     * @param string $sClassName
     */
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
