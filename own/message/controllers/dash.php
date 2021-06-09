<?php
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../modules/smsp/smsP.php';

use market\smsP;


$POST = $_POST;
if (isset($POST['text'])) {
    $phone = intval($POST['phone']);
    $text = $POST['text'];
    LOGDash::create_log('DashBoard', 'message');
    LOGDash::LOG('send', "$phone=>$text");
    $smsP = new smsP($phone);
    $res = $smsP->sendCustom($text);
    LOGDash::LOG('answer', $res);
    echo json_encode($res);
    die();
};


require_once __DIR__ . '/../../../lib/log/LogJSON.php';

class LOGDash

{

    static private LogJSON $log;

    static public function create_log($refer, $prefix)
    {
        self::$log = new LogJSON($refer, 'DASHBOARD', $prefix);
        self::$log->set_container('');
    }

    static public function _log($name_class = null, $name_func = null, $params = null)
    {
        self::$log->add($name_class . " " . $name_func, $params);
    }

    static public function send_error(Exception $e, string $message = null)
    {
        self::$log->send_error($e, $message);
    }

    static public function LOG($name_class, $params)
    {
        self::_log($name_class, '', $params);
    }

}
