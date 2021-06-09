<?php


namespace Generals;


use Configs\Configs;

trait MarketDB
{

    static function getExternalLinksSchema () : string {
        return '`' . self::getMarketDb() . '`.`external_links`';
    }

    static function getFilesSchema () : string {
        return '`' . self::getMarketDb() . '`.`files`';
    }

    static function getModulesDataSchema () : string {
        return '`' . self::getMarketDb() . '`.`modules_data`';
    }

    static function getUserConnectionsSchema () : string {
        return '`' . self::getMarketDb() . '`.`user_connections`';
    }

    static function getUserAdminSchema () : string {
        return '`' . self::getMarketDb() . '`.`user_admin`';
    }

    static function getMarketDb () : string {
        return Configs::getDbNames()->getMarket();
    }

}