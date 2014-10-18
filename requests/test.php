<?php
include '../base/watena.php';
WatenaLoader::init();

dump(parse_url('http://localhost/?test=a&test=b'));
parse_str('test=a&test=b', $aData);
dump($aData);
?>