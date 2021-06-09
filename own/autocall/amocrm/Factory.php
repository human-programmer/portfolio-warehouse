<?php

namespace Autocall\Amocrm;


use Services\General\iAccount;

require_once __DIR__ . '/../../../lib/log/LogJSON.php';
require_once __DIR__ . '/../../../lib/generals/amocrm/Factory.php';
require_once __DIR__ . '/../pragma/Factory.php';
require_once __DIR__ . '/modules/Lirax.php';
require_once __DIR__ . '/modules/getway/Gateway.php';
require_once __DIR__ . '/modules/settings/LiraxSettings.php';


class Factory extends \Autocall\Pragma\Factory
{
    static private string $widget_code = 'pmLirax';
    static private iLiraxSettings $lirax_settings;
    static private iGateway $gateway;
    static private iLirax $Lirax;
    static private null|iAccount $Account;

	static function getWidgetName():string {
		return self::$widget_code;
	}

	static function amocrmInit(string $referer, \LogWriter $logger): void {
		self::setLogWriter($logger);
		\Services\Factory::init(self::$widget_code, $referer, $logger);
		$node = \Services\Factory::getNodesService()->findAmocrmNodeCode(self::$widget_code, explode('.', $referer)[0]);
		self::$Account = $node->getAccount();

		parent::pragmaInit($node);

		self::$gateway = new Gateway($node, Factory::getLogWriter());
	}

    static function init(int $amocrm_account_id): void
    {
//    	$logger = new \LogJSON(self::$widget_code, $amocrm_account_id);
//    	\Services\Factory::init(self::$widget_code, '', $logger);
//    	self::setLogWriter($logger);
//        $node = \Services\Factory::getNodesService()->findAmocrmNodeAccId(self::$widget_code, $amocrm_account_id);
//
//        parent::pragmaInit($node);
//        self::$gateway = new Gateway($node, Factory::getLogWriter());
//        self::$Account = \Services\Factory::getAccountsService()->findAmocrmById($amocrm_account_id);
    }

    static function getLirax(): iLirax
    {
        if (isset(self::$Lirax))
            return self::$Lirax;
        self::$Lirax = new Lirax(self::getLiraxSettings()->getSettingsStruct());
        return self::$Lirax;
    }

    static function getGateway(): iGateway
    {
        return self::$gateway;
    }


    static function getLiraxSettings(): iLiraxSettings
    {
        if (isset(self::$lirax_settings))
            return self::$lirax_settings;

        $id =  self::$Account->getPragmaAccountId();
        self::$lirax_settings = new LiraxSettings($id);
        return self::$lirax_settings;
    }
}