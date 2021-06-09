<?php


namespace Generals;


use Configs\Configs;

trait StorageAdditionalDB
{
    static function getStorageCustomizationSchema(): string
    {
        return '`' . self::getAdditionalDB() . '`.`customization`';
    }

    static function getStorageFilesSchema(): string
    {
        return '`' . self::getAdditionalDB() . '`.`files`';
    }

    static function getStorageFilesToProductSchema(): string
    {
        return '`' . self::getAdditionalDB() . '`.`files_to_product`';
    }

    static function getStorageDiscountSchema(): string
    {
        return '`' . self::getAdditionalDB() . '`.`discount`';
    }

    static function getStorageSettings_stockSchema(): string
    {
        return '`' . self::getAdditionalDB() . '`.`settings_stock`';
    }

    static function getAdditionalDB(): string
    {
        return Configs::getDbNames()->getAdditionalStorage();
    }
}