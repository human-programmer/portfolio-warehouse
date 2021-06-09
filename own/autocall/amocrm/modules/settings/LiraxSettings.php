<?php

namespace Autocall\Amocrm;

require_once __DIR__ . '/../../business_rules/iLiraxSettings.php';
require_once __DIR__ . '/LiraxShop.php';
require_once __DIR__ . '/LiraxFrom.php';


use Autocall\Pragma\iLiraxSettingsStruct;

class LiraxSettings extends \Autocall\Pragma\LiraxSettings implements iLiraxSettings
{
    private int $pipeline_id = 1;


    function getSettingsStruct(): iLiraxSettingsStruct
    {
        $struct = parent::getSettingsStruct();
        $struct->useShops() && $struct->setIdShop($this->getIdShops());
        $struct->usePipelineNumber() && $struct->setFromNumber($this->getIdFromNumber());
        return $struct;
    }

    private function getIdShops(): int
    {
        $SHOP = new LiraxShop($this->pragma_account_id);
        return $SHOP->findIdShop($this->pipeline_id);
    }

    private function getIdFromNumber(): int
    {
        $FROM = new LiraxFrom($this->pragma_account_id);// если 1 есть найти по воронке
        return $FROM->findIdFrom($this->pipeline_id);
    }

    function setPipelineId(int $pipeline_id): void
    {
        $this->pipeline_id = $pipeline_id;
    }


}