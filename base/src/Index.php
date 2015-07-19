<?php
/**
 * Created by PhpStorm.
 * User: Jelle
 * Date: 19/07/2015
 * Time: 20:56
 */

namespace Watena\Core;


class Index extends Object {

    private $m_aData;

    protected function __construct(array $aData) {
        $this->m_aData = $aData;
    }

    public static function load($sId) {
        $oFile = new DataFile('indices/' . $sId);
        return $oFile->includeFile();
    }

    public static function create($sId, array $aData) {
        $oIndex = new Index($aData);
        $oFile = new DataFile('indices/' . $sId);
        $sContent = '<?php return ' . var_export($oIndex, true);
        $oFile->writeContent($sContent);
        return $oIndex;
    }
}