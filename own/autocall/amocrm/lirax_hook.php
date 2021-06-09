<?php

namespace Autocall\Amocrm;

use LogJSON;

header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../../lib/log/LogJSON.php';
require_once __DIR__ . '/../constants.php';
$logger = new LogJSON($_REQUEST['subdomain'] . '.amocrm.ru', \Lirax\WIDGET_NAME, 'AMO_HOOK');
$logger->set_container('');
$logger->add('time', date('l jS \of F Y h:i:s A'));
$logger->add('$_REQUEST', $_REQUEST);
require_once __DIR__ . '/Controller/Hook.php';

$REQUEST = $_REQUEST;
$subdomain = $REQUEST['subdomain'];
try {
    if ($REQUEST) {
        if (isset($REQUEST['account_id'])) {

            (new Hook($REQUEST, $subdomain, $logger))->run();
        };
    }
} catch (\Exception $exception) {
    $logger->send_error($exception);
}

