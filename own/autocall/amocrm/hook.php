<?php

namespace Autocall\Amocrm;

use LogJSON;

header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../../../lib/log/LogJSON.php';
require_once __DIR__ . '/../constants.php';
$logger = new LogJSON($_REQUEST['subdomain'] . '.amocrm.ru', \Lirax\WIDGET_NAME, 'HOOK');
$logger->set_container('');
if ($_REQUEST['subdomain'] == "pragmaintegrations")
    die();


$logger->add('$_REQUEST', $_REQUEST);
require_once __DIR__ . '/Controller/Missed.php';
require_once __DIR__ . '/Controller/Outgoing.php';
require_once __DIR__ . '/Controller/Adopted.php';


if (isset($_REQUEST['subdomain'])) {
    switch ($_REQUEST['id_hook']) {
        case 'missed':
            new Missed($_REQUEST, $logger);
            break;
        case 'adopted':
            new Adopted($_REQUEST, $logger);
            break;
        case 'outgoing':
            new Outgoing($_REQUEST, $logger);
            break;


    }
}

