<?php

namespace Autocall\Amocrm;

use LogJSON;

require_once __DIR__ . '/../Factory.php';
require_once __DIR__ . '/../../../../lib/log/LogJSON.php';
require_once __DIR__ . '/../../constants.php';


class Missed
{

    private int $Phone;
    private string $referrer;


    public function __construct(array $REQUEST, $logger)
    {
        $this->Phone = $REQUEST['ani'] * 1;
        if (strlen($this->Phone) > 10) {
            $this->referrer = $REQUEST['subdomain'];
            Factory::amocrmInit($this->referrer, $logger);
            $PipelineId = Factory::getLirax()->getIdPipelineByPhone($this->Phone);
            Factory::getLiraxCore($this->Phone)->setMode(true);
            $isTimeWork = Factory::getLiraxCore($this->Phone)->getWorkTime();
            Factory::getLogWriter()->add('$isTimeWork', $isTimeWork);
            switch ($isTimeWork) {
                case -1:
                    $this->core();
                    break;

                default:
                    $TimeSleep = ($isTimeWork * 60 * 60) + random_int(100, 1000);
                    $DATA = self::DataFile($REQUEST);
                    Factory::getLiraxCore($this->Phone)->getLiraxCoreStorage()->initFile('missed', $DATA, $TimeSleep);
                    break;
            }
        }

    }

    function core()
    {
        $SETTING_RESPONSIBLE = Factory::getLirax()->getLiraxSettingsStruct()->getApplication();
        $LEAD = Factory::getGateway()->getResponsibleIdLead($this->Phone);
        \Autocall\Pragma\Factory::getLogWriter()->add('$LEAD', $LEAD);
        $LEAD_RESPONSIBLE = $LEAD->getIdResponsible();
        $ARE_THE_RESPECTIVE = $SETTING_RESPONSIBLE == $LEAD_RESPONSIBLE;
        if ($SETTING_RESPONSIBLE || $LEAD_RESPONSIBLE) {
            switch ($ARE_THE_RESPECTIVE) {
                case true:
                    self::isFreeUsersAMOFALSE($this->referrer, $this->Phone);
                    break;
                case false:
                    self::isFreeUsersAMOTRUE($this->referrer, $this->Phone, 0);
                    break;
            }
        }
    }


    static function isFreeUsersAMOFALSE(string $referrer, int $Phone)
    {
        $logger = new LogJSON($referrer . '.amocrm.ru', \Lirax\WIDGET_NAME, 'HOOK_isFreeUsers');
        $logger->set_container('');
        Factory::amocrmInit($referrer, $logger);
        $ArrSIPs = Factory::getLirax()->getUserSIPs();
        $res = Factory::getLirax()->IsFreeUsers($ArrSIPs, '0');
        switch ($res['result']) {
            case null:
                $DATA = self::DataIsFreeUsersAMOFALSE($referrer, $Phone);
                Factory::getLiraxCore($Phone)->getLiraxCoreStorage()->initFile('MissedIFUAMOFALSE', $DATA, 60);
                break;
            default:
                $NUMBERAPD = self::searchNumberANDUPD($Phone);
                $speech = "АвтоЗвонок набор на номер плюс $NUMBERAPD";
                Factory::getLirax()->getLiraxSettingsStruct()->setSpeech($speech);
                Factory::getLirax()->getLiraxSettingsStruct()->setTargetNumber($Phone);
                Factory::getLirax()->getLiraxSettingsStruct()->setInnerNumber(5000);
                Factory::getLirax()->call();
                break;
        }
    }

    static function DataIsFreeUsersAMOFALSE(string $referrer, int $Phone): string
    {
        return '
require_once __DIR__ . "/../../../../../../../lib/log/LogJSON.php";
require_once __DIR__ . "/../../../../../constants.php";
require_once __DIR__ . "/../../../../../amocrm/Controller/Missed.php";
$referrer = \'' . $referrer . '\';
$Phone = ' . $Phone . ';
\Autocall\Amocrm\Missed::isFreeUsersAMOFALSE($referrer,$Phone);
';
    }


    static function isFreeUsersAMOTRUE(string $referrer, int $Phone, int $N)
    {
        $LEAD = Factory::getGateway()->getResponsibleIdLead($Phone);
        $LEAD_RESPONSIBLE = $LEAD->getIdResponsible();
        $res = Factory::getLirax()->IsFreeUsers($LEAD_RESPONSIBLE, '1');
        $data = $res['result'];
        $QUANTITY = Factory::getLirax()->getLiraxSettingsStruct()->getQuantityResponsible() * 1;
        Factory::getLogWriter()->add('IsFreeUsers', $res);

        if ($data != null) {
            $to1 = $res['ext'] * 1;

            $NUMBERAPD = self::searchNumberANDUPD($Phone);
            $speech = "АвтоЗвонок набор на номер плюс $NUMBERAPD";
            Factory::getLirax()->getLiraxSettingsStruct()->setSpeech($speech);
            Factory::getLirax()->getLiraxSettingsStruct()->setInnerNumber($to1);
            Factory::getLirax()->getLiraxSettingsStruct()->setTargetNumber($Phone);
            Factory::getLirax()->call();
        } else {
            switch ($N) {
                case  $QUANTITY :
                    self::isFreeUsersAMOFALSE($referrer, $Phone);
                    break;
                default:
                    $DATA = self::DataIsFreeUsersAMOTRUE($referrer, $Phone, $N);
                    Factory::getLiraxCore($Phone)->getLiraxCoreStorage()->initFile('MissedIFUAMOTRUE', $DATA, 60);
                    break;

            }
        }


    }

    static function DataIsFreeUsersAMOTRUE(string $referrer, int $Phone, int $N): string
    {
        return '
require_once __DIR__ . "/../../../../../amocrm/Controller/Missed.php";
require_once __DIR__ . "/../../../../../../../lib/log/LogJSON.php";
require_once __DIR__ . "/../../../../../constants.php";

$referrer = \'' . $referrer . '\';
$Phone = ' . $Phone . ';
$N = ' . $N + 1 . ';
$logger = new LogJSON($referrer . ".amocrm.ru", \Lirax\WIDGET_NAME, "HOOK");
$logger->set_container("");
unlink(__FILE__);
Autocall\Amocrm\Missed::initMissing($referrer, $logger);
Autocall\Amocrm\Missed::isFreeUsersAMOTRUE($referrer, $Phone, $N);
';

    }


    private function DataFile($REQUEST): string
    {
        $serializeData = serialize($REQUEST);
        return '
$referrer = "' . $this->referrer . '";
$number_phone = ' . $this->Phone . ';
$serializeData = \'' . $serializeData . '\';

require_once __DIR__ . "/../../../../../../../lib/log/LogJSON.php";
require_once __DIR__ . "/../../../../../constants.php";
require_once __DIR__ . "/../../../../../amocrm/Controller/Missed.php";

$logger = new LogJSON($referrer . ".amocrm.ru", \Lirax\WIDGET_NAME, "HOOK_Sleep");
$logger->set_container("");

$unserialezeData = unserialize($serializeData);
unlink(__FILE__);

(new \Autocall\Amocrm\Missed($unserialezeData, $logger));';
    }

    static function searchNumberANDUPD($str): string
    {
        $newStr = '';
        $pieces = explode(" ", $str);
        foreach ($pieces as $item) {
            if (preg_match('([0-9-]+)', $item)) {
                $newStr .= self::PhoneD($item);
            } else {
                $newStr .= $item . ' ';
            }
        }
        return $newStr;
    }

    static function PhoneD(string $str): string
    {
        $newStr = '';
        for ($i = 0; $i < strlen($str); $i++) {
            $dit = $str[$i];
            switch ($i) {
                case 2:
                case 4:
                case 7:
                case 9:
                    $newStr .= $dit . " ";
                    break;
                default:
                    $newStr .= $dit;
                    break;
            }
        }
        return $newStr;
    }

    static function initMissing($referrer, $logger)
    {
        Factory::amocrmInit($referrer, $logger);
    }

}