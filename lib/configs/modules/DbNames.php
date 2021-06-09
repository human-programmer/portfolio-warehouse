<?php


namespace Configs;

require_once __DIR__ . '/../interface/iDbNames.php';

class DbNames implements iDbNames {
	private array $names;

	public function __construct(array $names) {
		$this->names = $names;
	}

	function getAmocrmInterface(): string {
		return $this->names['amocrm_interface'];
	}

	function getBitrix24Interface(): string {
		return $this->names['bitrix24_interface'];
	}

	function getDashboard(): string {
		return $this->names['dashboard'];
	}

	function getCalculator(): string {
		return $this->names['calculator'];
	}

	function getCoreCrm(): string {
		return $this->names['pragmacrm'];
	}

	function getModules(): string {
		return $this->names['modules'];
	}

	function getUsers(): string {
		return $this->names['users'];
	}

	function getStorage(): string {
		return $this->names['storage'];
	}

	function getAdditionalStorage(): string {
		return $this->names['additional_storage'];
	}

	function getMarket(): string {
		return $this->names['market'];
	}

	function getFiles(): string {
		return $this->names['files'];
	}

	function getModulesSettings(): string {
		return $this->names['module_settings'];
	}
}