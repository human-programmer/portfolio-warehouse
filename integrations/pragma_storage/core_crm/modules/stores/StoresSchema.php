<?php


namespace PragmaStorage;


require_once __DIR__ . '/../db/PragmaStoreDB.php';


class StoresSchema extends PragmaStoreDB {
	private int $pragma_account_id;

	public function __construct(int $pragma_account_id) {
		parent::__construct();
		$this->pragma_account_id = $pragma_account_id;
	}

	protected function create_store(string $title, string $address): int {
		$title = self::formattingAsVarchar($title);
		$address = self::formattingAsVarchar($address);
		$stores_schema = parent::getStorageStoresSchema();

		self::validTitle($title);

		$sql = "INSERT INTO $stores_schema (`account_id`, `title`, `address`)
                VALUES ($this->pragma_account_id, :title, :address)";

		$params = ['title' => $title, 'address' => $address];

		if (!self::execute($sql, $params))
			throw new \Exception('Failed to create new store');

		return self::last_id();
	}

	protected function updateStoreInDb(iStore $store): bool {
		$title = self::formattingAsVarchar($store->getTitle());
		$address = self::formattingAsVarchar($store->getAddress());
		$store_id = $store->getStoreId();
		$stores_schema = parent::getStorageStoresSchema();
		self::validTitle($title);
		$sql = "UPDATE $stores_schema 
                SET `title` = :title, `address` = :address, deleted = :deleted
                WHERE `id` = $store_id AND `account_id` = $this->pragma_account_id";
		$params = ['title' => $title, 'address' => $address, 'deleted' => (int) $store->isDeleted()];
		self::executeSql($sql, $params);
		return true;
	}

	static private function validTitle (string $title) : void {
		if(!$title)
			throw new \Exception('Поле "название" обязательно');
	}

	protected function remove_store(int $store_id): bool {
		$stores_schema = parent::getStorageStoresSchema();

		$sql = "DELETE FROM $stores_schema 
                WHERE `id` = $store_id
                AND `account_id` = $this->pragma_account_id";

		return self::execute($sql);
	}

	protected function getStoreModel(int $store_id): array {
		return $this->get_store_models("id = $store_id")[0] ?? throw new \Exception("Store not found '$store_id'");
	}

	protected function get_store_models(string $condition = ''): array {
		$stores_schema = parent::getStorageStoresSchema();
		$condition = $condition ? "AND $condition" : '';
		$sql = "SELECT 
                    $stores_schema.`id` AS `store_id`,
                    $stores_schema.`account_id` AS `pragma_account_id`,
                    $stores_schema.`title`,
                    $stores_schema.`address`,
                    $stores_schema.`deleted`
                FROM $stores_schema
                WHERE $stores_schema.`account_id` = $this->pragma_account_id $condition";

		return self::query($sql);
	}

	public function getPragmaAccountId(): int {
		return $this->pragma_account_id;
	}

	static protected function formattingAsVarchar (string $string) : string {
		return substr(trim($string), 0, 256);
	}
}