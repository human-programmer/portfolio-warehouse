<?php
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../../lib/log/LogJSON.php';
require_once __DIR__ . '/../constants.php';

$logger = new LogJSON(get_referer(), \Lirax\WIDGET_NAME, 'pip');
$logger->set_container('');


require_once __DIR__ . '/Factory.php';
require_once __DIR__ . '/../../../lib/services/Factory.php';


$POST = $_POST;
if (!$POST)
    die();
$flag = $POST['flag'];
$account_id = intval($POST['account_id']);
$widget_code = $POST['widget_code'];
try {
    \Services\Factory::init($widget_code,get_referer(),$logger);
    $node = \Services\Factory::getNodesService()->findAmocrmNodeCode($widget_code, explode('.', get_referer())[0]);
    \Autocall\pragma\Factory::pragmaInit($node);
    \Autocall\pragma\Factory::setLogWriter($logger);

    $PIP = \Autocall\pragma\Factory::getPips();

    switch ($flag) {
        case 'save':
            $ACCOUNT_ID = intval($POST['account_id']);
            $ID = intval($POST['id']);
            $CHECK = $POST['CHECK'];

            switch ($CHECK) {
                case 'true':
                    $PIP->savePipe($ID);
                    break;
                case 'false':
                    $PIP->deletePipe($ID);
                    break;
            }
            break;
        case 'get':
            $ACCOUNT_ID = intval($POST['account_id']);
            $data = $PIP->getPips();
            echo json_encode($data);
            break;
    }
} catch (\Exception $exception) {
    $logger->send_error($exception);
}


function get_referer(): string
{
    return $_POST['subdomain'] . '.amocrm.ru';
}

