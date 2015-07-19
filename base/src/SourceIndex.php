<?php
/**
 * Created by PhpStorm.
 * User: Jelle
 * Date: 14/07/2015
 * Time: 20:57
 */

namespace Watena\Core;


class SourceIndex extends Object {

    private $m_aImplementers = array();
    private $m_aExtenders = array();

    public function __construct() {
    }

    public function scanDirectory($sNamespace, $sDirectory) {
        $aEntries = scandir($sDirectory);
        foreach($aEntries as $sEntry) {
            if(substr($sEntry, 0, 1) != '.') {
                $sPath = $sDirectory . DIRECTORY_SEPARATOR . $sEntry;

                if(is_dir($sPath))
                    $this->scanDirectory($sNamespace . '\\' . $sEntry, $sPath);
                if(is_file($sPath))
                    $this->scanFile($sNamespace, $sPath);
            }
        }
    }

    public function scanFile($sNamespace, $sFile) {
        $sName = pathinfo($sFile, PATHINFO_FILENAME);
        $sClass = $sNamespace . '\\' . $sName;

        if(class_exists($sClass)) {
            $tClass = new \ReflectionClass($sClass);

            $aInterfaces = $tClass->getInterfaceNames();
            foreach($aInterfaces as $sInterface) {
                if(!isset($this->m_aImplementers[$sInterface]))
                    $this->m_aImplementers[$sInterface] = array();

                $this->m_aImplementers[$sInterface] []= $sClass;
            }

            $tParent = $tClass->getParentClass();
            while(null != $tParent) {
                if(!isset($this->m_aExtenders[$tParent->getName()]))
                    $this->m_aExtenders[$tParent->getName()] = array();

                $this->m_aExtenders[$tParent->getName()] []= $sClass;

                $tParent = $tParent->getParentClass();
            }

            $sAuthor = null;
            $sVersion = null;
            $sDescription = null;
            $sComment = $tClass->getDocComment();
            $aLines = explode_trim("\n", $sComment);
            foreach($aLines as $sLine) {
                $aMatches = array();
                if(preg_match('/^\* (\@([a-z]+) )?(.*)$/', $sLine, $aMatches)) {
                    if($aMatches[2] == 'author')
                        $sAuthor = $aMatches[3];
                    if($aMatches[2] == 'version')
                        $sVersion = $aMatches[3];
                    if(!$aMatches[2] && $aMatches[3])
                        $sDescription .= $aMatches[3] . "<br />\n";
                }
            }

            if($sAuthor && $sVersion) {
                echo "$sClass: $sAuthor [$sVersion]<br />\n$sDescription<br />\n";
            }
        }
    }
}