<?php
include 'base/watena.php';
\Watena\Core\WatenaLoader::init();
\Watena\Core\WatenaLoader::load()->mvc();

/*
echo '<pre>';
$sPrefix = 'static';
$sDirA = PATH_BASE . '/static';
$sDirB = PATH_BASE . '/src';

$aFiles = scandir($sDirA);
$nPrefixLength = strlen($sPrefix) + 1;
foreach($aFiles as $sFile) {
    if(strncmp($sFile, $sPrefix.'.', $nPrefixLength) === 0) {
        $sFileA = $sDirA . '/' . $sFile;
        $sFileB = $sDirB . '/' . ucfirst(substr($sFile, $nPrefixLength));
        echo "$sFileA => $sFileB\n";
        rename($sFileA, $sFileB);
    }
}
echo '</pre>';
*/

