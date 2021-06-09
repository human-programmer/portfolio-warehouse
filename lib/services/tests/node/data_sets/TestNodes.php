<?php


namespace Services\Tests;


use Services\General\iAccount;
use Services\General\iModule;
use Services\General\iNode;
use Services\General\iUser;
use Services\Node;

require_once __DIR__ . '/PragmaNodes.php';
require_once __DIR__ . '/AmocrmNodes.php';

class TestNodes extends \Generals\CRMDB {
	use PragmaNodes, AmocrmNodes;

	static function createUniqueNodes(): array {
		return [
			self::createUniqueNode(),
			self::createUniqueNode(),
			self::createUniqueNode(),
			self::createUniqueNode(),
		];
	}

	static function createUniqueNode(iModule $module = null, iAccount $account = null): iNode {
		$module = $module ?? self::creatUniqueModule();
		$account = $account ?? self::createUniqueAccount();
		self::createNodeInDb($module, $account);
		return self::getNode($module, $account);
	}

	static function createUniqueNodesWithUsers(): array {
		return [
			self::createUniqueNodeWithUser(),
			self::createUniqueNodeWithUser(),
			self::createUniqueNodeWithUser(),
			self::createUniqueNodeWithUser(),
		];
	}

	static function createUniqueNodeWithUser(iUser $user = null,iModule $module = null, iAccount $account = null): iNode {
		$module = $module ?? self::creatUniqueModule();
		$account = $account ?? self::createUniqueAccount();
		$user = $user ?? self::createUniqueUser();
		self::createNodeInDb($module, $account, $user);
		return self::getNode($module, $account, $user);
	}

	private static function createNodeInDb(iModule $module, iAccount $account, iUser $user = null): void {
		self::createPragmaNodeInDb($module, $account, $user);
		self::createAmocrmNodeInDb($module, $account);
	}

	static function getNode(iModule $module, iAccount $account, iUser|null $user = null): iNode {
		$model = self::getModel($module, $account, $user);
		return new Node($model);
	}

	private static function getModel(iModule $module, iAccount $account, iUser|null $user = null): array {
		$pragma = self::getPragmaModel($module, $account);
		$amocrm = self::getAmocrmModel($module->getPragmaModuleId(), $account->getPragmaAccountId());
		$model = array_merge($pragma, $amocrm);
		$model['module'] = $module;
		$model['account'] = $account;
		$model['user'] = $user;
		return $model;
	}

	private static function creatUniqueModule(): iModule {
		return TestModules::createUniqueModule();
	}

	private static function createUniqueAccount(): iAccount {
		return TestAccounts::createUniqueAccount();
	}

	private static function createUniqueUser(): iUser {
		return TestUsers::createUniqueUser();
	}

	static function removeTestEntities(): void {
		TestModules::removeTestModules();
		TestAccounts::removeTestAccounts();
	}
}