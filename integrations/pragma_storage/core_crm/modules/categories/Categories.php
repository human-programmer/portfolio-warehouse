<?php


namespace PragmaStorage;


require_once __DIR__ . '/CategoriesSchema.php';
require_once __DIR__ . '/../../business_rules/categories/iCategories.php';
require_once __DIR__ . '/Category.php';


class Categories extends CategoriesSchema implements iCategories {
	private array $categories;

	public function __construct(private IStoreApp $app) {
		parent::__construct($this->app->getPragmaAccountId());
	}

	public function getCategory(int $category_id): iCategory {
		return $this->findInBuffer($category_id) ?? $this->getFromDb($category_id);
	}

	private function getFromDb(int $category_id): iCategory {
		$model = $this->getCategoryModel($category_id);
		return $this->getCategoryInstance($model);
	}

	public function getCategories(): array {
		if (isset($this->categories))
			return $this->categories;
		$this->loadCategories();
		return $this->categories;
	}

	public function createCategory(string $title, array $stores_id = []): iCategory {
		$this->validStoresId($stores_id);
		$category = $this->insertCategory($title);
		$this->saveStoresLinks($category->getCategoryId(), $stores_id);
		return $category;
	}

	private function insertCategory(string $title): iCategory {
		$title = substr(trim($title), 0, 256);
		$category_id = parent::create_category($title);
		return $this->getCategory($category_id);
	}

	private function validStoresId(array $stores_id): void {
		foreach ($stores_id as $store_id)
			!$this->app->getStores()->getStore($store_id);
	}

	private function saveStoresLinks(int $category_id, array $stores_id): void {
		$stores_id = count($stores_id) ? $stores_id : $this->getDefaultStoresId();
		$this->app->getCategoriesToStores()->saveCategoryLinks($category_id, $stores_id);
	}

	private function getDefaultStoresId(): array {
		$stores = $this->app->getStores()->getStores();
		foreach ($stores as $store)
			$result[] = $store->getStoreId();
		return $result ?? throw new \Exception("Stores not found");
	}

	public function findCategory(string $title) {
		$title = trim($title);

		foreach ($this->getCategories() as $category)
			if ($category->getTitle() === $title)
				return $category;

		return null;
	}

	protected function loadCategories() {
		$models = $this->getCategoryModels();
		foreach ($models as $category_model)
			$this->getCategoryInstance($category_model);
		$this->categories = $this->categories ?? [];
	}

	private function getCategoryInstance(array $model): iCategory {
		$category = $this->findInBuffer($model['category_id']) ?? new Category($this, $model);
		$this->categories[$category->getCategoryId()] = $category;
		return $category;
	}

	private function findInBuffer(int $category_id): iCategory|null {
		if(!isset($this->categories)) return null;
		return $this->categories[$category_id] ?? null;
	}

	function save(ICategoryStruct $category): void {
		parent::updateCategory($category->getCategoryId(), $category->getTitle());
	}

	function delete(iCategory $category): bool {
		if (!parent::deleteCategory($category->getCategoryId()))
			throw new \Exception("Failed to delete Category: " . $category->getCategoryId());
		$this->deleteFromBuffer($category);
		return true;
	}

	private function deleteFromBuffer(iCategory $category): void {
		if(isset($this->categories)) unset($this->categories[$category->getCategoryId()]);
	}
}