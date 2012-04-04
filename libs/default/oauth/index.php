<?php
define('OAUTH_PROVIDER_REQUEST_TOKEN', 1);
define('OAUTH_PROVIDER_AUTHENTICATE', 2);
define('OAUTH_PROVIDER_ACCESS_TOKEN', 3);

require_once dirname(__FILE__) . '/oauth_client.php';
require_once dirname(__FILE__) . '/oauth_provider.php';
require_once dirname(__FILE__) . '/oauth_consumer.php';
require_once dirname(__FILE__) . '/oauth_token.php';
require_once dirname(__FILE__) . '/oauth_signatureproviders.php';
require_once dirname(__FILE__) . '/oauth_exception.php';
require_once dirname(__FILE__) . '/oauth_request.php';
require_once dirname(__FILE__) . '/oauth_server.php';
require_once dirname(__FILE__) . '/oauth_util.php';
?>