<?php namespace Watena\Core;


class SourceDirectory extends CacheableDirectory {

    private $m_sNamespace;

    private $m_aFiles = array();
    private $m_aDirectories = array();

    public function make(array $aMembers) {
        $this->m_sNamespace = $aMembers['namespace'];
    }

    public function init() {
        foreach($this->getFiles('php', true) as $sFile) {
            $oFile = SourceFile::create($sFile, array('namespace' => $this->m_sNamespace));
            $this->m_aFiles []= $oFile;
        }

        foreach($this->getDirectories(true) as $sDirectory) {
            $sNamespace = $this->m_sNamespace . '\\' . $sDirectory;
            $oDirectory = SourceDirectory::create($sDirectory, array('namespace' => $sNamespace));
            $this->m_aDirectories []= $oDirectory;
        }
    }

    /**
     * @return SourceFile[]
     */
    public function getSourceFiles() {
        return $this->m_aFiles;
    }

    /**
     * @return SourceDirectory[]
     */
    public function getSourceDirectories() {
        return $this->m_aDirectories;
    }
}