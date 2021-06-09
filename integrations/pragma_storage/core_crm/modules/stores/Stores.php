<?php


namespace PragmaStorage;


require_once __DIR__ . '/StoresSchema.php';
require_once __DIR__ . '/Store.php';
require_once __DIR__ . '/../../business_rules/stores/iStores.php';

class Stores extends StoresSchema implements iStores {
	private array $stores;

	public function __construct(private IStoreApp $app) {
		parent::__construct($this->app->getPragmaAccountId());
	}

	public function getStores(int $archive_status = UNARCHIVED_STATUS): array {
		$this->loadStores();
		return $this->filterExistsStores($archive_status);
	}

	private function filterExistsStores(int $archive_status = UNARCHIVED_STATUS): array {
		switch ($archive_status){
			case ALL_ARCHIVE_STATUS:
				return $this->getAllStores();
			case UNARCHIVED_STATUS:
				return self::filterStoresByArchiveStatus($this->getAllStores(), false);
			case ARCHIVED_STATUS:
				return self::filterStoresByArchiveStatus($this->getAllStores(), true);
			default:
				throw new \Exception("Invalid archive status '$archive_status'");
		}
	}

	private function getAllStores(): array {
		return array_values($this->stores);
	}

	private static function filterStoresByArchiveStatus(array $stores, bool $isDeleted): array {
		foreach ($stores as $store)
			if($store->isDeleted() === $isDeleted)
				$result[] = $store;
		return $result ?? [];
	}

	public function loadStores() {
		if(isset($this->stores) && count($this->stores)) return;
		$this->stores = [];
		$models = $this->get_store_models();
		foreach ($models as $store_model)
			$this->createInstance($store_model);
		$this->stores = $this->stores ?? [];
	}

	function deleteStore(iStore $store): bool {
		return $this->archiveStore($store);
	}

	private function checkAvailableToDelete(iStore $store): void {
		if($store->isDeleted()) return;
		$unarchivedStores = $this->getStores(UNARCHIVED_STATUS);
		if(count($unarchivedStores) < 2)
			throw new \Exception("This is last unarchived store");
	}

	private function deleteFromDb(iStore $store): void {
		if (!parent::remove_store($store->getStoreId()))
			throw new \Exception('Failed to delete Store: ' . $store->getStoreId());
		$store->setDeleted();
	}

	function createStore(string $title, string $address): iStore {
		$title = self::formattingAsVarchar($title);
		$address = self::formattingAsVarchar($address);
		$store_id = $this->create_store($title, $address);
		return $this->getStore($store_id);
	}

	function save(iStore $store): void {
		parent::updateStoreInDb($store);
	}

	function archiveStore(iStore $store): bool {
		$this->checkAvailableToDelete($store);
		$store->setDeleted();
		$this->save($store);
		return true;
	}

	function getStore(int $store_id): iStore {
		return $this->findInBuffer($store_id) ?? $this->getFromDb($store_id);
	}

	private function getFromDb(int $store_id): iStore {
		$model = $this->getStoreModel($store_id);
		return $this->createInstance($model);
	}

	private function createInstance(array $model): iStore {
		$store = $this->findInBuffer($model['store_id']) ?? new Store($this->app, $this, $model);
		$this->addInBuffer($store);
		return $store;
	}

	private function addInBuffer(iStore $store): void {
		$this->stores[$store->getStoreId()] = $store;
	}

	private function deleteFromBuffer(iStore $store): void {
		if(isset($this->stores)) unset($this->stores[$store->getStoreId()]);
	}

	private function findInBuffer(int $store_id): iStore|null {
		if(!isset($this->stores[$store_id])) return null;
		return $this->stores[$store_id] ?? null;
	}

	function getAvailableStores(int $product_id): array {
		$stores = $this->getStores();
		foreach ($stores as $store)
			if($this->allowedForStore($store->getStoreId(), $product_id))
				$result[] = $store;
		return $result ?? $stores;
	}

	function allowedForStore(int $store_id, int $product_id): bool {
		$product = $this->app->getProducts()->getProduct($product_id);
		return array_search($product->getCategoryId(), $this->getCategoriesId($store_id)) !== false;
	}

	private function getCategoriesId(int $store_id): array {
		return $this->app->getCategoriesToStores()->getCategoriesIdInStore($store_id);
	}
}