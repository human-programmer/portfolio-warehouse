<?php


namespace PragmaStorage;


class StatusesSchema extends PragmaStoreDB {
	private static array $models;
	private int $pragma_account_id;

	public function __construct(int $pragma_account_id) {
		parent::__construct();
		$this->pragma_account_id = $pragma_account_id;
	}

	static function getStatusModels(): array {
		if (isset(self::$models))
			return self::$models;
		self::loadModels();
		return self::$models;
	}

	static private function loadModels() {
		$statuses = self::getStorageStatusesSchema();
		$sql = "SELECT * FROM $statuses WHERE 1";
		self::$models = self::query($sql);
	}
}