<?php

namespace Autocall\Amocrm;

class DurationEqualsZero extends Duration
{
    static int $MAX_QUANTITY;
    static int $Phone;
    static int $Status;
    static array $ARRAY_QUANTITY;

    public static function DurationEqualsZeroDenisMoreFive(int $Phone)
    {
        self::$Phone = $Phone;

        $check_mode = Factory::getLiraxCore(self::$Phone)->getMode();
        Factory::log('$check_mode', $check_mode);

        if ($check_mode) {
            $QUANTITY = Factory::getLiraxAdditionallySettings()->getSettingsStruct()->getNumber_of_call_attempts();
            Factory::log('$QUANTITY', $QUANTITY);

            self::$ARRAY_QUANTITY = $QUANTITY['data_q'];
            self::$MAX_QUANTITY = $QUANTITY['quantity'] * 1;
            self::$Status = Factory::getLiraxCore(self::$Phone)->getStatus();
            match (self::$Status) {
                self::$MAX_QUANTITY => self::MaxNumberOfCalls(),
                default => self::default_file()
            };
        }
    }

    private static function MaxNumberOfCalls(): int
    {
        $MAX_QUANTITY = self::$MAX_QUANTITY;
        Factory::getLiraxCore(self::$Phone)->setStatus(0);
        Factory::getLiraxCore(self::$Phone)->setMode(false);
        Factory::log("MaxNumberOfCalls status $MAX_QUANTITY", 0);
        return $MAX_QUANTITY;
    }

    private static function default_file()
    {
        $isExistFile = Factory::getLiraxCore(self::$Phone)->getLiraxCoreStorage()->getExistGeneralFile();

        switch ($isExistFile) {
            case true:
                Factory::log("file exist", true);
                break;
            default:
                $new_status = self::$Status + 1;

                Factory::getLiraxCore(self::$Phone)->setStatus($new_status);
                $timer = self::save_timer();
                Factory::log("timer", $timer);
                $data = self::RenderFile();
                Factory::getLiraxCore(self::$Phone)->getLiraxCoreStorage()->initFile('general', $data, $timer);
                break;
        }
        return null;

    }

    private static function RenderFile(): string
    {
        $referrer = Factory::getAccountsModule()->getAccount()->getAmocrmReferer();
        $referrer = explode('.', $referrer)[0];
        $Phone = self::$Phone;
        return '
use Autocall\Amocrm\File;
require_once __DIR__ . "/../../../../../amocrm/Controller/Outgoing/File.php";
$referrer = "' . $referrer . '";
$Phone = ' . $Phone . ';
(new File ($referrer, $Phone));
';
    }

    private static function save_timer(): int
    {
        $status = self::$Status;
        $data = self::$ARRAY_QUANTITY;
        Factory::log('$status', $status);
        Factory::log('data', $data);

        $time = [];
        foreach ($data as $item) {
            $st = intval($item['q'] - 1);
            if ($st == $status) {
                $time = $item;
            }
        }

        $data = explode(":", $time['time']);
        $var1 = intval($data[0]);
        $var2 = intval($data[1]);

        return $var1 * 3600 + $var2 * 60;
    }


    public static function DurationEqualsZeroDenisLessFive(int $phone)
    {
        $data = null;
        $STR = Factory::getLirax()->getUserSIPs();
        Factory::log('ARRAY', $STR);
        $res = Factory::getLirax()->IsFreeUsers($STR, 0);
        if (isset($res)) {
            $data = $res['result'];
        }

        Factory::log('DurationEqualsZeroDenisLessFive', [
            'phone' => $phone,
            'IsFreeUsers' => $res,
            'result' => $data,
        ]);


        switch ($data) {
            case null:
                $data = self::Render_DurationEqualsZeroDenisLessFive($phone);
                Factory::getLiraxCore($phone)->getLiraxCoreStorage()->initFile('DurationEqualsZeroDenisLessFive_', $data, 60);
                Factory::log('DurationEqualsZeroDenisLessFiveNULL', $phone);
                break;
            default:
                $NUMBERAPD = File::searchNumberANDUPD($phone);
                $speech = "АвтоЗвонок набор на номер плюс $NUMBERAPD";
                Factory::getLirax()->getLiraxSettingsStruct()->setSpeech($speech);
                Factory::getLirax()->getLiraxSettingsStruct()->setTargetNumber($phone);
                Factory::getLirax()->getLiraxSettingsStruct()->setInnerNumber(5000);
                Factory::getLirax()->call();
                Factory::log('$phone', $phone);
                Factory::log('call5000', $res);
                break;
        }

    }

    static function Render_DurationEqualsZeroDenisLessFive(int $Phone): string
    {
        $referrer = Factory::getAccountsModule()->getAccount()->getAmocrmReferer();
        return '
namespace Autocall\Amocrm;
use DurationEqualsZero;
require_once __DIR__ . "/../../../../../amocrm/Controller/Outgoing/DurationEqualsZero.php";
require_once __DIR__ . "/../../../../lib/log/LogJSON.php";
use LogJSON;
require_once __DIR__ . "/../../constants.php";
$referrer = ' . $referrer . ';
$Phone = ' . $Phone . ';
$logger = new LogJSON($referrer . ".amocrm.ru", \Lirax\WIDGET_NAME, "HOOK_isFreeUsers");
$logger->set_container("");
Factory::amocrmInit($referrer, $logger);
DurationEqualsZero::DurationEqualsZeroDenisLessFive($Phone);
';
    }
}