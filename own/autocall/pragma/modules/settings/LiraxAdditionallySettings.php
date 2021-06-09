<?php


namespace Autocall\Pragma;
require_once __DIR__ . '/../../business_rules/settings/iLiraxAdditionallySettings.php';
require_once __DIR__ . '/LiraxAdditionallySettingsStruct.php';
require_once __DIR__ . '/LiraxAdditionallySettingsSchema.php';


class LiraxAdditionallySettings implements iLiraxAdditionallySettings
{
    protected LiraxAdditionallySettingsSchema $GENERAL;

    public function __construct(int $pragma_account_id)
    {
        $this->GENERAL = new LiraxAdditionallySettingsSchema($pragma_account_id);
    }

    function getSettingsStruct(): iLiraxAdditionallySettingsStruct
    {
        $LiraxAdditionallySettingsStructModel = $this->GENERAL->getGeneralSettingsModel();
        return new LiraxAdditionallySettingsStruct(
            $LiraxAdditionallySettingsStructModel['TimeResponsible'],
            $LiraxAdditionallySettingsStructModel['WorkTime'],
            $LiraxAdditionallySettingsStructModel['QuantityCallClient'],
            $LiraxAdditionallySettingsStructModel['NumberOfCallAttempts'],
            $LiraxAdditionallySettingsStructModel['ArrayUsePipelineShops'],
            $LiraxAdditionallySettingsStructModel['ArrayUsePipelineNumbers'],
            $LiraxAdditionallySettingsStructModel['ArrayUsePriority'],
        );
    }

    function saveTimeResponsible(int $quantity): void
    {
        $this->GENERAL->setTimeResponsible($quantity);
    }

    function saveWorkTime(array $quantity): void
    {
        $this->GENERAL->setWorkTime($quantity);

    }

    function saveQuantityCallClient(int $quantity): void
    {
        $this->GENERAL->setQuantityCallClient($quantity);
    }


    function saveNumberOfCallAttempts(array $array_calls): void
    {
        $this->GENERAL->setNumberOfCallAttempts($array_calls);
    }


    function saveArrayUsePipelineShops(array $array_pipeline): void
    {
        $this->GENERAL->setArrayUsePipelineShops($array_pipeline);
    }

    function saveArrayUsePipelineNumbers(array $array_pipeline): void
    {
        $this->GENERAL->setArrayUsePipelineNumbers($array_pipeline);
    }

    function saveArrayUsePriority(array $array_pipeline): void
    {
        $this->GENERAL->setArrayUsePriority($array_pipeline);
    }
}