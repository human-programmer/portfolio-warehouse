<?php

require_once __DIR__ . '/../.././lib/log/LogJSON.php';

class LogAutoCall
{

    static private LogJSON $log;

    static public function create_log($refer, $prefix)
    {
        self::$log = new LogJSON($refer, 'AutoCall', $prefix);
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

    static function return__referrer(int $account_id): string
    {
        $referrer = match ($account_id) {
            28967665 => 'pragmaintegrations',
//            21647575 => "guzall",
            13818423 => "obivaem",
            28576573 => "okonkont",
            28603537 => "usaavto",
            28967662 => "pragmadev",
        };
        return $referrer . ".amocrm.ru";
    }

    static public function LOG($name_class, $params)
    {
        self::_log($name_class, '', $params);
    }

}

// missed      https://smart.pragma.by/api/own/lirax/hook.php?id_hook=missed&id_account=28967665
// adopted     https://smart.pragma.by/api/own/lirax/hook.php?id_hook=adopted&id_account=28967665
// outgoing    https://smart.pragma.by/api/own/lirax/hook.php?id_hook=outgoing&id_account=28967665


