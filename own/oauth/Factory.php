<?php


namespace oAuth;

use market\iIntegration;
use market\Integration;
use Services\General\iNode;
use Services\General\iNodesService;
use Services\General\iUsersService;


require_once __DIR__ . '/../../integrations/store/core_crm/modules/Integration/Integration.php';
require_once __DIR__ . '/../../lib/services/Factory.php';

class Factory
{

    static private $code = 'market';
    static private string $integration_id;
    static private string $subdomain;
    private static iIntegration $Integration;
    private static \LogWriter $log;


    static function init(string $subdomain, string $integration_id, \LogWriter $logWriter)
    {
        self::$integration_id = $integration_id;
        self::$subdomain = $subdomain;
        self::$log = $logWriter;
        \Services\Factory::init(self::$code, '', $logWriter);

    }

    static function getNodessService(): iNodesService
    {
        return \Services\Factory::getNodesService();
    }

    static function getNode(): iNode|null
    {
        return self::getNodessService()->findAmocrmNode(self::$integration_id, self::$subdomain);
    }

    static function getUsersService(): iUsersService
    {
        return \Services\Factory::getUsersService();
    }

    static function getLog(): \LogWriter
    {
        return self::$log;
    }


    static function getIntegration(): iIntegration
    {
        if (isset(self::$Integration))
            return self::$Integration;
        self::$Integration = new Integration(self::getNode()->getAccount()->getPragmaAccountId());
        return self::$Integration;
    }

}