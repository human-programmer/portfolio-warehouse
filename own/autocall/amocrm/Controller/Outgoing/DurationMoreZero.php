<?php
namespace Autocall\Amocrm;

class DurationMoreZero extends Duration
{

    static function DurationMoreZeroDenisMoreFive(int $Phone)
    {
        $isExistFile = Factory::getLiraxCore($Phone)->getLiraxCoreStorage()->getExistGeneralFile();

        switch ($isExistFile) {
            case true:

                $MAX_QUANTITY = Factory::getLiraxAdditionallySettings()->getSettingsStruct()->getNumber_of_call_attempts()['quantity'] * 1;
                Factory::getLiraxCore($Phone)->setStatus($MAX_QUANTITY);
                Factory::log("change_status_phone $Phone", $MAX_QUANTITY);

                break;
            case false:
                Factory::getLiraxCore($Phone)->setStatus(0);
                Factory::getLiraxCore($Phone)->setMode(false);
                Factory::log("change_status_phone $Phone", false);
                break;
        }

    }

}