<?php


namespace FilesSystem\Pragma\Tests;


use Generals\CRMDB;
use Services\General\iNode;
use Services\Node;

class TestNodes extends CRMDB {
	static function uniqueNode(): iNode {
//		\Services\Tests\TestNodes::createUniqueNode();
//		$module = TestModules::uniqueModule();
//		$account = TestAccounts::uniqueAccount();
//		$model = [
//			'module' => $module,
//			'account' => $account,
//			'shutdown_time' => 0,
//			'is_unlimited' => false,
//			'is_once_installed' => true,
//			'is_pragma_active' => true,
//		];
//		$node = new Node($model);
//		self::installNode($node);
//		self::installAmoNode($node);
		return \Services\Tests\TestNodes::createUniqueNode();
	}

	static function clear(): void {
		TestAccounts::clear();
		TestModules::clear();
	}

	private static function installNode(iNode $node): void {
		$pragmaNodes = CRMDB::getPragmaAccountsModuleSchema();
		$module_id = $node->getModule()->getPragmaModuleId();
		$account_id = $node->getAccount()->getPragmaAccountId();
		$sql = "INSERT INTO $pragmaNodes (module_id, account_id)
				VALUES($module_id, $account_id)
				ON DUPLICATE KEY UPDATE module_id = VALUES(module_id)";
		self::executeSql($sql);
	}
}