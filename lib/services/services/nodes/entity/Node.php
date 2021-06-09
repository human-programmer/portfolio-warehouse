<?php


namespace Services;


use Services\General\Account;
use Services\General\iAccount;
use Services\General\iModule;
use Services\General\iUser;
use Services\General\Module;

require_once __DIR__ . '/../../../business_rules/general/node/iNode.php';
require_once __DIR__ . '/PragmaNode.php';
require_once __DIR__ . '/AmocrmNode.php';
require_once __DIR__ . '/Bitrix24Node.php';

class Node extends General\iNode {
	use PragmaNode, AmocrmNode, Bitrix24Node;

	private iAccount $account;
	private iModule $module;
	private iUser|null $user;
	private array $loaders;

	public function __construct(array $model) {
		$this->mainInit($model);
		$this->pragmaInit($model);
		$this->amocrmInit($model);
		$this->bitrix24Init($model);
	}

	private function mainInit(array $model): void {
		$this->account = self::formattingAccount($model['account']);
		$this->module = self::formattingModule($model['module']);
		$this->user = self::formattingUser($model['user'] ?? null);
		$loaders = $model['loaders'] ?? [];
		$this->loaders = is_array($loaders) ? $loaders : [];
	}

	private static function formattingAccount(iAccount|array $account): iAccount {
		if($account instanceof iAccount)
			return $account;
		return new Account($account);
	}

	private static function formattingModule(iModule|array $module):iModule {
		if($module instanceof iModule)
			return $module;
		return new Module($module);
	}

	private static function formattingUser(iUser|array|null $user):iUser|null {
		if($user instanceof iUser || is_null($user))
			return $user;
		return new User($user);
	}

	function getPragmaUserId(): int|null {
		return $this->getUser()?->getPragmaUserId();
	}

	function getUser(): iUser|null {
		return $this->user;
	}

	function getAccount(): iAccount {
		return $this->account;
	}

	function getModule(): iModule {
		return $this->module;
	}

	function isActive(): bool {
		return $this->isAmocrmEnable() && $this->isPragmaActive();
	}

	function toArray(): array {
		$model = array_merge(
			[
				'account' => $this->getAccount()->toArray(),
				'module' => $this->getModule()->toArray(),
				'user' => $this->getUser()?->toArray(),
			],
			$this->getPragmaModel(),
			$this->getAmocrmModel(),
		);
		$model['is_active'] = $this->isActive();
		return $model;
	}
	function getLoaders(): array {
		return $this->loaders;
	}
}