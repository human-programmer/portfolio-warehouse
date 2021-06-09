<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../business_rules/categories/iCategory.php';


class Category implements iCategory {
	private iCategories $categories;
	private int $category_id;
	private string $title;
	private bool $deleted;

	public function __construct(iCategories $categories, array $model) {
		$this->categories = $categories;
		$this->category_id = $model['category_id'];
		$this->title = $model['title'];
		$this->deleted = isset($model['deleted']) && !!$model['deleted'];
	}

	public function getCategoryId(): int {
		return $this->category_id;
	}

	public function getTitle(): string {
		return $this->title;
	}

	function isDeleted(): bool {
		return $this->deleted;
	}

	function delete(): bool {
		if (!$this->getCategories()->delete($this))
			throw new \Exception("Failed to delete Category");

		$this->deleted = true;

		return true;
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	function toArray(): array {
		return ['category_id' => $this->getCategoryId(), 'title' => $this->getTitle(),];
	}

	function update(array $model): bool {
		foreach ($model as $field_name => $val)
			switch ($field_name) {
				case 'title':
					$this->title = trim($val);
					break;
			}
		$this->getCategories()->save($this);
		return true;
	}

	private function getCategories(): iCategories {
		return $this->categories;
	}

	function linkedStoreId(): array {
		return PragmaFactory::getStoreApp()->getCategoriesToStores()->getStoresForCategory($this->getCategoryId());
	}
}