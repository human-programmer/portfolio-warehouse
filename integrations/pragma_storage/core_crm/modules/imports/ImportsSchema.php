<?php


namespace PragmaStorage;


use Generals\Functions\Date;

require_once __DIR__ . '/../../../CONSTANTS.php';


class ImportsSchema extends PragmaStoreDB {
	private int $pragma_account_id;

	protected function __construct(int $pragma_account_id) {
		parent::__construct();
		$this->pragma_account_id = $pragma_account_id;
	}

	protected function update_import(IImportStruct $struct): bool {
		$imports = self::getStorageImportsSchema();
		$id = $struct->getImportId();
		$sql = "UPDATE $imports SET provider = :provider, date = :import_date WHERE id = $id";
		self::executeSql($sql, [
			'provider' => trim(substr(trim($struct->getProvider()), 0, 256)),
			'import_date' => Date::getStringTimeStamp($struct->getImportTime()),
		]);
		return true;
	}

	protected function create_import(int $store_id, array $import_model = []): int {
		unset($import_model['date_create']);
		unset($import_model['source']);
		$sql = $this->getSqlForInsert($store_id, $import_model);
		if (!self::execute($sql, $import_model))
			throw new \Exception('Failed to create import record');

		return self::last_id();
	}

	private function getSqlForInsert(int $store_id, array $model): string {
		return $this->getInsertSqlString($model) . $this->getValuesSqlString($store_id, $model);
	}

	private function getInsertSqlString(array $model): string {
		$import_schema = parent::getStorageImportsSchema();
		$sql = "INSERT INTO $import_schema (`store_id`, `number`";
		$titles = implode(', ', array_keys($model));
		$sql .= $titles ? ', ' . $titles : '';
		return $sql . ') ';
	}

	protected function getDeficitImportModel(int $store_id): array {
		return $this->find_deficit_import($store_id) ?? $this->createAndGetDeficitModel($store_id);
	}

	private function createAndGetDeficitModel(int $store_id): array {
		$id = $this->createDeficitImportRow($store_id);
		return $this->get_import($id);
	}

	private function createDeficitImportRow(int $store_id): int {
		$import_schema = parent::getStorageImportsSchema();
		$number = $this->getNextImportNumber();
		$source = DEFICIT_SOURCE;
		$sql = "INSERT INTO $import_schema (`store_id`, `number`, `source`)
				VALUES($store_id, $number, $source)";
		self::executeSql($sql);
		return self::last_id();
	}

	protected function createTravelsImportRow(ICreationTravelModel $travel): int {
		$import_schema = parent::getStorageImportsSchema();
		$sql = "INSERT INTO $import_schema (`store_id`, `number`, `source`, `user_id`)
				VALUES(:store_id, :number, :source, :user_id)";
		self::executeSql($sql, [
			'store_id' => $travel->getEndStoreId(),
			'number' =>  $this->getNextImportNumber(),
			'source' => STORE_SOURCE,
			'user_id' => $travel->getUserId(),
		]);
		return self::last_id();
	}

	private function getValuesSqlString(int $store_id, array $model): string {
		$values_string = implode(', :', array_keys($model));
		$number = $this->getNextImportNumber();

		if ($values_string)
			$values_string = ', :' . $values_string;

		return "VALUES ($store_id, $number" . $values_string . ')';
	}

	protected function delete_import(int $import_id): bool {
		$schema_name = parent::getStorageImportsSchema();
		$sql = "DELETE FROM $schema_name WHERE `id` = $import_id";
		return self::execute($sql, []);
	}

	protected function get_import(int $import_id) {
		$imports = $this->getStorageImportsSchema();

		$sql = $this->sql() . " AND $imports.`id` = $import_id";

		return self::query($sql)[0] ?? null;
	}

	protected function find_deficit_import(int $store_id): array|null{
		$imports = $this->getStorageImportsSchema();
		$deficit_source = DEFICIT_SOURCE;
		$sql = $this->sql() . " AND $imports.`source` = $deficit_source AND $imports.`store_id` = $store_id";
		return self::query($sql)[0] ?? null;
	}

	protected function get_store_import(int $store_id) {
		$imports = $this->getStorageImportsSchema();

		$sql = $this->sql() . " AND $imports.`store_id` = $store_id";

		return self::query($sql);
	}

	private function sql(): string {
		$imports = $this->getStorageImportsSchema();
		$stores = $this->getStorageStoresSchema();

		return "SELECT 
                    $imports.`id` AS `import_id`,
                    $imports.`date` AS `import_date`,
                    $imports.`date_create` AS `date_create`,
                    $imports.`provider` AS `provider`,
                    $imports.`source` AS `source`,
                    $imports.`number` AS `number`,
                    $stores.`id` AS `store_id`,
                    $stores.`title` AS `store_title`,
                    $stores.`address` AS `store_address`
                FROM $imports
                INNER JOIN $stores ON $imports.`store_id` = $stores.`id`
                WHERE $stores.`account_id` = " . $this->getPragmaAccountId();
	}

	private function getNextImportNumber(): int {
		$imports = parent::getStorageImportsSchema();
		$stores = parent::getStorageStoresSchema();

		$sql = "SELECT 
                    MAX($imports.`number`) AS `max_number` 
                FROM $stores
                    INNER JOIN $imports ON $imports.`store_id` = $stores.`id`
                WHERE $stores.`account_id` = $this->pragma_account_id";

		$number = self::query($sql)[0]['max_number'] ?? 0;
		return $number + 1;
	}

	public function getPragmaAccountId(): int {
		return $this->pragma_account_id;
	}

	static function formattingAsVarchar(string $string, int $length = 256) : string {
		return trim(substr(trim($string), 0, $length));
	}
}