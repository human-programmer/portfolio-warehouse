<?php


namespace PragmaStorage;

require_once __DIR__ . '/../../business_rules/settings/iSettings.php';
require_once __DIR__ . '/SettingsSchema.php';

class Settings extends SettingsSchema implements iSettings
{
    private SettingsSchema $SettingsSchema;

    function __construct(int $pragma_account_id)
    {
        parent::__construct($pragma_account_id);
        $this->SettingsSchema = new SettingsSchema($pragma_account_id);
    }


    function setFractional(string $fractional): void
    {
        $this->SettingsSchema->saveFractional($fractional);
    }

    function getFractional(): bool
    {
        return $this->SettingsSchema->getFractional();
    }

    function setStock(int $id): void
    {
        $this->SettingsSchema->saveStock($id);
    }

    function getStock(): int
    {
        $array = $this->SettingsSchema->getStockId();
        if (count($array) < 1)
            return -1;
        return $array[0]['stock_id'];
    }

    function getFirstStockId(): int
    {
        $id = current($this->SettingsSchema->FirstStockId()[0]);
        Factory::getLogWriter()->add('$id', $id);
        return $id;
    }
}