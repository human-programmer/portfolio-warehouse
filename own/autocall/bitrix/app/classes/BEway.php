<?php
namespace Autocall\Bitrix\BClass;

require_once __DIR__ . '/../../../../../lib/generals/bitrix24/Factory.php';

class BEway
{
    static function getEway($metod)
    {
        $accounts_module = \Generals\Bitrix24\Factory::getAccountsModules()::bitrix24AccountsModules()::getByMemberId('pmLirax', $_REQUEST['member_id']);
        $gateway = new \RestApi\Bitrix24\Bitrix24Gateway($accounts_module);

        return $gateway->query($metod);
    }
}
?>