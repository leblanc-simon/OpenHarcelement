<?php

define('LIB_DIR', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib');
define('CONFIG_DIR', __DIR__);
define('APP_DIR', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'app');

require_once LIB_DIR.DIRECTORY_SEPARATOR.'Config.php';
require_once LIB_DIR.DIRECTORY_SEPARATOR.'Connection.php';
require_once LIB_DIR.DIRECTORY_SEPARATOR.'Harcelement.php';
require_once LIB_DIR.DIRECTORY_SEPARATOR.'Response.php';

require_once CONFIG_DIR.DIRECTORY_SEPARATOR.'config.inc.php';

require_once APP_DIR.DIRECTORY_SEPARATOR.'Api.php';
require_once APP_DIR.DIRECTORY_SEPARATOR.'Task.php';