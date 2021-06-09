<?php

namespace Autocall\Pragma;
require_once __DIR__ . '/../../business_rules/settings/iLiraxSettings.php';
require_once __DIR__ . '/LiraxSettingsSchema.php';
require_once __DIR__ . '/LiraxSettingsStruct.php';


class LiraxSettings implements iLiraxSettings
{
    protected LiraxSettingsSchema $general;
    protected int $pragma_account_id;

    public function __construct(int $pragma_account_id)
    {
        $this->pragma_account_id = $pragma_account_id;
        $this->general = new LiraxSettingsSchema($pragma_account_id);
    }

    function getSettingsStruct(): iLiraxSettingsStruct
    {
        $settings_model = $this->general->getGeneralSettingsModel();


        return new LiraxSettingsStruct(
            $settings_model['token'],
            $settings_model['use_store'],
            $settings_model['use_number'],
            $settings_model['use_priory'],
            $settings_model['use_responsible'],
            $settings_model['application'],
        );
    }


    function saveToken(string $token): void
    {
        $this->general->setToken($token);
    }

    function saveUseStore(string $use): void
    {
        $this->general->setUseStore($use);
    }

    function saveUseNumber(string $use): void
    {
        $this->general->setUseNumber($use);
    }

    function saveUsePriory(string $use): void
    {
        $this->general->setUsePriory($use);
    }

    function saveUseResponsible(string $quantity): void
    {
        $this->general->setUseResponsible($quantity);
    }

    function saveReferrer(string $referrer): void
    {
        $this->general->setReferrer($referrer);
    }

    function saveApplication(int $id): void
    {
        $this->general->setApplication($id);
    }

}