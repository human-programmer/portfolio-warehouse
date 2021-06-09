<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../core_crm/PragmaFactory.php';
require_once __DIR__ . '/../../../pragmacrm/amocrm/Factory.php';
require_once __DIR__ . '/AmocrmCatalog.php';
require_once __DIR__ . '/AmocrmStorage.php';
require_once __DIR__ . '/modules/AmoEntityInterface.php';


class Factory extends PragmaFactory {
	private static iAmocrmInterface $interface;
	private static AmocrmStorage $amo_storage;
	private static iAmocrmCatalog $catalog;

	public static function init(string $widget_code, string $referer, \LogWriter $log_writer) {
		\Services\Factory::init($widget_code, $referer, $log_writer);
		$account = \PragmaCRM\Factory::getAccountByReferer($referer);
		\PragmaCRM\Factory::init($widget_code, $account, $log_writer);
	}

	public static function initById(string $widget_code, $amocrm_account_id,  \LogWriter $log_writer) {
		\Services\Factory::init($widget_code, '', $log_writer);
		$account = \PragmaCRM\Factory::getAccountByAmocrmId($amocrm_account_id);
		\PragmaCRM\Factory::init($widget_code, $account, $log_writer);
	}

	public static function getAmocrmEntityInterface(): iAmocrmEntityInterfaceForStorage {
		return \PragmaCRM\Factory::getAmoStorage();
	}

	public static function getAmocrmInterface(): iAmocrmInterface {
		if (isset(self::$interface))
			return self::$interface;

		self::$interface = new AmoEntityInterface();

		return self::$interface;
	}

	//Все геттеры
	public static function getCatalog(): iAmocrmCatalog {
		if (isset(self::$catalog))
			return self::$catalog;

		self::$catalog = new AmocrmCatalog(self::getPragmaAccountId());

		return self::$catalog;
	}

	//Создание, изменение, удаление
	public static function getAmocrmStorage(): iAmocrmStorage {
		return self::getStoreApp();
	}

	static function getStoreApp() {
		if (isset(self::$amo_storage))
			return self::$amo_storage;

		self::$amo_storage = new AmocrmStorage();

		return self::$amo_storage;
	}
}