<?php

namespace PragmaStorage;

require_once __DIR__ . '/../../../CONSTANTS.php';


class DiscountSchema extends PragmaStoreDB
{
    private int $pragma_account_id;

    protected function __construct(int $pragma_account_id)
    {
        parent::__construct();
        $this->pragma_account_id = $pragma_account_id;
    }


    function CreateUpdateDiscount(int $idExportProduct, int $discount, float $full_price): int
    {

        $discount_schema = self::getStorageDiscountSchema();
        $account_id = $this->pragma_account_id;


        $sql = "INSERT INTO $discount_schema (`account_id`, `product_export_id`, `discount`, `full_price`)
                VALUES ( :account_id, :product_export_id, :discount, $full_price)
                ON DUPLICATE KEY UPDATE `discount` = $discount";
        return self::execute($sql,
            ['account_id' => $account_id, 'product_export_id' => $idExportProduct, 'discount' => $discount]);

    }

    function getDiscountIdSql(int $id): array
    {
        $discount_schema = self::getStorageDiscountSchema();
        $account_id = $this->pragma_account_id;

        $sql = "SELECT $discount_schema.`discount`,
        $discount_schema.`full_price`
        FROM $discount_schema WHERE `account_id` = $account_id AND `product_export_id` = $id ";
        return self::query($sql);
    }


    function getDiscountSql(): array
    {
        $discount_schema = self::getStorageDiscountSchema();
        $account_id = $this->pragma_account_id;

        $sql = "SELECT $discount_schema.`discount`,
        $discount_schema.`product_export_id`
        FROM $discount_schema WHERE `account_id` = $account_id";

        return self::query($sql);
    }

    function deleteSql(int $id): void
    {
        $discount_schema = self::getStorageDiscountSchema();
        $account_id = $this->pragma_account_id;
        $sql = "DELETE FROM $discount_schema WHERE 
        $discount_schema.`product_export_id`= $id";
        self::query($sql);
    }


}