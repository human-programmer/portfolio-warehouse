<?php


namespace Generals;


use Configs\Configs;

trait ModulesDB
{
    static function getCoreCrmModulesSchema () : string {
        return '`' . self::getModulesDb() . '`.`modules`';
    }
    static function getCoreCrmModulesGroupsSchema () : string {
        return '`' . self::getModulesDb() . '`.`groups`';
    }
    static function getCoreCrmModulesToGroupsSchema () : string {
        return '`' . self::getModulesDb() . '`.`modules_to_groups`';
    }
    static function getCoreCrmRedirectsSchema () : string {
        return '`' . self::getModulesDb() . '`.`redirect_links`';
    }
    static function getCoreCrmAccountsModuleSchema () : string {
        return '`' . self::getModulesDb() . '`.`module_to_account`';
    }

    static protected function getModulesDb () : string {
        return Configs::getDbNames()->getModules();
    }
}