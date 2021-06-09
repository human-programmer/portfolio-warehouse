<?php


namespace Generals;


use Configs\Configs;

trait AmocrmInterfaceDB
{
    static function getAmocrmAccountsSchema() : string {
        return '`' . self::getAmocrmInterfaceDb() . '`.`account`';
    }

    static function getAmocrmEntitiesSchema() : string {
        return '`' . self::getAmocrmInterfaceDb() . '`.`entity`';
    }

    static function getAmocrmFieldsSchema() : string {
        return '`' . self::getAmocrmInterfaceDb() . '`.`field`';
    }

    static function getAmocrmEnumsSchema() : string {
        return '`' . self::getAmocrmInterfaceDb() . '`.`fields_options`';
    }

    static function getAmocrmPipelinesSchema() : string {
        return '`' . self::getAmocrmInterfaceDb() . '`.`pipelines`';
    }

    static function getAmocrmStatusesSchema() : string {
        return '`' . self::getAmocrmInterfaceDb() . '`.`statuses`';
    }

    static function getAmocrmUsersSchema() : string {
        return '`' . self::getAmocrmInterfaceDb() . '`.`users`';
    }

    static function getAmocrmModulesSchema() : string {
        return '`' . self::getAmocrmInterfaceDb() . '`.`modules`';
    }

    static function getAmocrmModuleTokensSchema() : string {
        return '`' . self::getAmocrmInterfaceDb() . '`.`modules_tokens`';
    }

    static function getAmocrmInterfaceDb () : string {
        return Configs::getDbNames()->getAmocrmInterface();
    }
}