<?php


namespace PragmaStorage;


class CategoriesSchema extends PragmaStoreDB {
	private int $pragma_account_id;

	public function __construct(int $pragma_account_id) {
		parent::__construct();
		$this->pragma_account_id = $pragma_account_id;
	}

	protected function create_category(string $title): int {
		$title = trim($title);
		$this->validTitle($title);
		$categories_schema = parent::getStorageCategoriesSchema();
		$sql = "INSERT INTO $categories_schema (`account_id`, `title`)
                VALUES ($this->pragma_account_id, :title)";
		if (!self::execute($sql, ['title' => $title]))
			throw new \Exception('Failed to create new Category');
		return self::last_id();
	}

	private function validTitle (string $title) : void {
		if(!$title)
			throw new \Exception('Title cannot be empty');
		if ($this->findCategory($title))
			throw new \Exception('Such a category with this name already exists');
	}

	public function updateCategory(int $category_id, string $title): bool {
		$title = trim($title);
		$categories_schema = parent::getStorageCategoriesSchema();
		$sql = "UPDATE $categories_schema SET `title` = :title WHERE `id` = $category_id";
		self::executeSql($sql, ['title' => $title]);
		return true;
	}

	public function deleteCategory(int $category_id): bool {
		$categories_schema = parent::getStorageCategoriesSchema();
		$sql = "DELETE FROM $categories_schema WHERE `account_id` = $this->pragma_account_id AND `id` = $category_id";
		self::executeSql($sql);
		return true;
	}

	public function findCategory(string $title) {
		$categories_schema = parent::getStorageCategoriesSchema();
		$sql = $this->sql() . "AND $categories_schema.`title` = $title";
		return self::querySql($sql)[0] ?? null;
	}

	protected function getCategoryModel(int $category_id): array {
		$sql = $this->sql("id = $category_id");
		return self::querySql($sql)[0];
	}

	public function getCategoryModels(): array {
		return self::querySql($this->sql());
	}

	private function sql(string $condition = ''): string {
		$condition = $condition ? "AND $condition" : '';
		$categories_schema = parent::getStorageCategoriesSchema();
		return "SELECT 
                    $categories_schema.`id` AS `category_id`,
                    $categories_schema.`account_id` AS `pragma_account_id`,
                    $categories_schema.`title`
                FROM $categories_schema
                WHERE `account_id` = $this->pragma_account_id $condition";
	}

	public function getPragmaAccountId(): int {
		return $this->pragma_account_id;
	}

	public static function getCategoriesSchema(): string {
		return parent::getStorageCategoriesSchema();
	}
}