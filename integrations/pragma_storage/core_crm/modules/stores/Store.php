<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../business_rules/stores/iStore.php';
require_once __DIR__ . '/../../PragmaFactory.php';


class Store implements iStore {
	private iStores $stores;
	private int $store_id;
	private string $title;
	private string $address;
	private bool $deleted;

	public function __construct(private IStoreApp $app, iStores $store, array $model) {
		$this->stores = $store;
		$this->store_id = $model['store_id'];
		$this->title = $model['title'];
		$this->address = $model['address'];
		$this->deleted = isset($model['deleted']) && !!$model['deleted'];
	}

	public function getStoreId(): int {
		return $this->store_id;
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function getAddress(): string {
		return $this->address;
	}

	function isDeleted(): bool {
		return $this->deleted;
	}

	function delete(): bool {
		return $this->stores->deleteStore($this);
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	function toArray(): array {
		return [
			'store_id' => $this->getStoreId(),
			'title' => $this->getTitle(),
			'address' => $this->getAddress(),
			'deleted' => $this->isDeleted(),
		];
	}

	function update(array $model): bool {
		foreach ($model as $field_name => $val)
			switch ($field_name) {
				case 'title':
					$this->title = $val;
					break;
				case 'address':
					$this->address = $val;
					break;
			}
		$this->stores->save($this);
		return true;
	}

	function getOwnImports(): array {
		return self::getImports()->getImports($this);
	}

	function getOwnProductImports(): array {
		$imports = $this->getOwnImports();
		foreach ($imports ?? [] as $import)
			$ids[] = $import->getImportId();
		return isset($ids) ? $this->getProductImports()->getAllImportProductImports($ids) : [];
	}

	private function getProductImports(): iProductImports {
		return $this->app->getProductImports();
	}

	private function getImports(): iImports {
		return $this->app->getImports();
	}

	function getOwnCategoriesId(): array {
		return $this->app->getCategoriesToStores()->getCategoriesIdInStore($this->getStoreId());
	}

	function getOwnProductsId(): array {
		return $this->app->getCategoriesToStores()->getProductsIdInStore($this->getStoreId());
	}

	function setDeleted(): void {
		$this->deleted = true;
	}

	function addCategory(iCategory $category): void {
		$this->app->getCategoriesToStores()->saveCategoryLinks($category->getCategoryId(), [$this->getStoreId()]);
	}
}