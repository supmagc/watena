<?php
define('PATH_ADMIN', realpath(dirname(__FILE__)));

require_once PATH_ADMIN . '/system/interface.iadmingeneratable.php';

require_once PATH_ADMIN . '/system/class.adminmodule.php';
require_once PATH_ADMIN . '/system/class.adminmoduletab.php';
require_once PATH_ADMIN . '/system/class.adminmoduleitem.php';
require_once PATH_ADMIN . '/system/class.adminmodulecontent.php';
require_once PATH_ADMIN . '/system/class.adminmodulecontentrequest.php';
require_once PATH_ADMIN . '/system/class.adminmodulecontentresponse.php';
require_once PATH_ADMIN . '/system/class.adminplugin.php';
?>