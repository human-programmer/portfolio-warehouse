<?php

namespace Autocall\Amocrm;

use LogJSON;

require_once __DIR__ . '/../../Factory.php';
require_once __DIR__ . '/../../../constants.php';

class File
{
    private int $MAX_QUANTITY;

    public function __construct(private string $referrer, private int $Phone)
    {
        $logger = new LogJSON($referrer . '.amocrm.ru', \Lirax\WIDGET_NAME, 'File');
        $logger->set_container('');
        Factory::amocrmInit($this->referrer, $logger);
        $QUANTITY = Factory::getLiraxAdditionallySettings()->getSettingsStruct()->getNumber_of_call_attempts();
        $this->MAX_QUANTITY = $QUANTITY['quantity'] * 1;
        $Status = Factory::getLiraxCore($this->Phone)->getStatus();


        switch ($Status) {
            case  $this->MAX_QUANTITY:
                Factory::getLiraxCore($this->Phone)->setStatus(0);
                Factory::getLiraxCore($this->Phone)->setMode(false);
                break;
            default:
                $this->_DEFAULT();
                break;
        }
    }

    private function _DEFAULT()
    {
        $application_id = Factory::getLirax()->getLiraxSettingsStruct()->getApplication();
        $LEAD = Factory::getGateway()->getResponsibleIdLead($this->Phone);
        $res = $LEAD->getIdResponsible();

        $result = $res == $application_id;

        if ($res == null) {
            $NUMBERAPD = self::searchNumberANDUPD($this->Phone);
            $speech = "АвтоЗвонок набор на номер плюс $NUMBERAPD";
            Factory::getLirax()->getLiraxSettingsStruct()->setSpeech($speech);
            Factory::getLirax()->getLiraxSettingsStruct()->setTargetNumber($this->Phone);
            Factory::getLirax()->getLiraxSettingsStruct()->setInnerNumber(5000);
            Factory::getLirax()->call();
            Factory::log($this->Phone . " NO RESPONSIBLE res = 0 call5000", true);
        } else {
            switch ($result) {
                case true:
                    $this->trueIsFreeUsers();
                    break;
                case false:
                    $this->falseIsFreeUsers($res, 0);
                    break;
            }
        }

    }

    function trueIsFreeUsers(): void
    {
        $arrInnerNumber = Factory::getLirax()->getUserSIPs();
        $data_res = Factory::getLirax()->IsFreeUsers($arrInnerNumber, 0);
        switch ($data_res) {
            case 'null':
            case null:
                $data = $this->Render_DATA_NULL_();
                Factory::getLiraxCore($this->Phone)->getLiraxCoreStorage()->initFile('trueIsFreeUsersNULL_', $data, 60);
                break;
            default :
                $this->trueIsFreeUsersRESULT($data_res['ext']);
                break;
        }
    }


    function trueIsFreeUsersRESULT(string $ext): void
    {
        $status = Factory::getLiraxCore($this->Phone)->getStatus();
        $MStatus = $this->MAX_QUANTITY;

        switch ($status) {
            case $MStatus:
                Factory::getLiraxCore($this->Phone)->setStatus(0);
                Factory::getLiraxCore($this->Phone)->setMode(false);
                Factory::log('trueIsFreeUsersRESULT', [
                    'MaxNumberOfCalls' => $this->MAX_QUANTITY,
                    $this->Phone => 'OFF'
                ]);
                break;
            default:
                $new_status = $status + 1;
                Factory::getLiraxCore($this->Phone)->setStatus($new_status);
                $NUMBERAPD = self::searchNumberANDUPD($this->Phone);
                $speech = "АвтоЗвонок набор на номер плюс $NUMBERAPD";
                Factory::getLirax()->getLiraxSettingsStruct()->setSpeech($speech);
                Factory::getLirax()->getLiraxSettingsStruct()->setTargetNumber($this->Phone);
                Factory::getLirax()->getLiraxSettingsStruct()->setInnerNumber($ext);
                Factory::getLirax()->call();
                Factory::log('trueIsFreeUsersRESULT', [
                    'New Status' => $new_status,
                ]);
                break;

        }

    }

    function falseIsFreeUsers(string $responsibility, $N): void
    {
        $res = Factory::getLirax()->IsFreeUsers($responsibility, 1);
        $data_res = $res['result'];

        switch ($data_res) {
            case null:
            case 'null':
                if ($N < 30) {
                    $data = $this->IsFreeUsersNULLandNO30($responsibility, $N);
                    Factory::getLiraxCore($this->Phone)->getLiraxCoreStorage()->initFile('falseIsFreeUsersNULLandNO30', $data, 60);
                } else $this->trueIsFreeUsers();
                break;
            default:
                $NUMBERAPD = self::searchNumberANDUPD($this->Phone);
                $speech = "АвтоЗвонок набор на номер плюс $NUMBERAPD";
                Factory::getLirax()->getLiraxSettingsStruct()->setSpeech($speech);
                Factory::getLirax()->getLiraxSettingsStruct()->setTargetNumber($this->Phone);
                Factory::getLirax()->getLiraxSettingsStruct()->setInnerNumber($res['ext']);
                Factory::getLirax()->call();
                break;
        }

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


    function IsFreeUsersNULLandNO30(string $responsibility, int $n): string
    {
        return '
require_once __DIR__ . "/../../../../../amocrm/Controller/Outgoing/File.php";
$referrer = \'' . $this->referrer . '\';
$Phone = ' . $this->Phone . ';
$responsibility = ' . $responsibility . ';
$n = ' . $n . ';
$FILE = new \Autocall\Amocrm\File ($referrer, $Phone);
$FILE->falseIsFreeUsers($responsibility, $n);
';

    }

    function Render_DATA_NULL_(): string
    {
        return '
require_once __DIR__ . "/../../../../../amocrm/Controller/Outgoing/File.php";
$referrer = \'' . $this->referrer . '\';
$Phone = ' . $this->Phone . ';
$FILE = new \Autocall\Amocrm\File($referrer, $Phone);
$FILE->trueIsFreeUsers();
';
    }


}