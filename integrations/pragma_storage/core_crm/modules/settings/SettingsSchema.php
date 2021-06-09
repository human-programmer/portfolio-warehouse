<?php


namespace PragmaStorage;

require_once __DIR__ . '/../../../CONSTANTS.php';


class SettingsSchema extends PragmaStoreDB
{

    protected function __construct(private int $pragma_account_id)
    {
        parent::__construct();
    }

    function saveFractional(string $double)
    {
        $doubled = $double === 'true' ? 'true' : 'false';

        $settings_schema = self::getStorageCustomizationSchema();
        $pragma_account_id = $this->pragma_account_id;
        $sql = "INSERT INTO $settings_schema (`account_id`, `doubled`)
                VALUES ($pragma_account_id, :doubled)
                ON DUPLICATE KEY UPDATE `doubled` = $doubled";

        self::execute($sql, ['doubled' => $doubled]);
    }

    function getFractional(): bool
    {
        $fractional = $this->fractionalScheme();
        return !!$fractional[0]['doubled'];

    }

    function fractionalScheme(): array
    {
        $settings_schema = self::getStorageCustomizationSchema();
        $pragma_account_id = $this->pragma_account_id;

        $sql = "SELECT $settings_schema.`doubled` FROM $settings_schema WHERE `account_id` = $pragma_account_id";

        return self::query($sql);
    }

    function saveStock(int $stock_id): void
    {
        $settings_schema = self::getStorageSettings_stockSchema();

        $sql = "INSERT INTO $settings_schema (`account_id`, `stock_id`)
                VALUES ($this->pragma_account_id, :stock_id)
                ON DUPLICATE KEY UPDATE `stock_id` = $stock_id";
        self::execute($sql, ['stock_id' => $stock_id]);

    }

    function getStockId(): array
    {
        $settings_schema = self::getStorageSettings_stockSchema();
        $pragma_account_id = $this->pragma_account_id;
        $sql = "SELECT $settings_schema.`stock_id` FROM $settings_schema WHERE `account_id` = $pragma_account_id";
        return self::query($sql);
    }

    function FirstStockId(): array
    {
        $settings_schema = self::getStorageStoresSchema();
        $pragma_account_id = $this->pragma_account_id;
        $sql = "SELECT MIN($settings_schema.`id`) FROM $settings_schema WHERE `account_id` = $pragma_account_id";
        return self::query($sql);
    }


}