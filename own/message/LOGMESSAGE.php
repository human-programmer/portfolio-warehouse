<?php

require_once __DIR__ . '/../.././lib/log/LogJSON.php';

class LOGMESSAGE

{

    static private LogJSON $log;

    static public function create_log($refer, $prefix)
    {
        self::$log = new LogJSON($refer, 'MESSAGE', $prefix);
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
