<?php
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../../lib/log/LogJSON.php';
require_once __DIR__ . '/../constants.php';
$logger = new LogJSON(get_referer(), \Lirax\WIDGET_NAME, 'settings');
$logger->set_container('');
require_once __DIR__ . '/../pragma/Controller/AutoCallSettings.php';
require_once __DIR__ . '/../LogAutoCall.php';

try{
    \Autocall\Amocrm\Factory::amocrmInit(get_referer(), $logger);
    (new AutoCallSettings($_REQUEST));
} catch (\Exception $exception) {
    $logger->send_error($exception);
}

function get_referer (){
    $request = parse_url($_SERVER['HTTP_REFERER']);
    return $request['host'] ?? 'undefined';
}