<?php


namespace Autocall\Bitrix;


class Factory extends \Autocall\Pragma\Factory
{
    static private iBitrixSettings $bitrix_settings ;

    static function init(int $member_id) : void {
        \Generals\Amocrm\Factory::getAccountsModules()::bitrix24AccountsModules()::getByMemberId('pmLirax',$member_id);
    }

    static function getBitrix() : iBitrix {
        return new Bitrix(self::getBitrixSettingsStruct());
    }




    static function getLiraxSetting(): iBitrixSettings
    {
        if (isset(self::$bitrix_settings))
            return self::$bitrix_settings;
        self::$bitrix_settings = new BitrixSettings(self::getAccountsModule()->getPragmaAccountId());
        return self::$bitrix_settings;
    }

}