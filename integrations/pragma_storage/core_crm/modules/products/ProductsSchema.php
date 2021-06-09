<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../../CONSTANTS.php';


class ProductsSchema extends PragmaStoreDB {
	private int $pragma_account_id;

	protected function __construct(int $pragma_account_id) {
		parent::__construct();
		$this->pragma_account_id = $pragma_account_id;
	}

	protected function update_product(iProduct $product): bool {
		$this->checkProduct($product);
		$products_schema = self::getStorageProductsSchema();
		$id = $product->getProductId();
		$sql = "UPDATE $products_schema 
					SET article = :article,
					title = :title,
					unit = :unit,
					selling_price = :selling_price,
					deleted = :deleted
				WHERE account_id = $this->pragma_account_id AND id = $id";
		self::executeSql($sql, [
			'article' => $product->getArticle(),
			'title' => $product->getTitle(),
			'unit' => $product->getUnit(),
			'selling_price' => $product->getSellingPrice(),
			'deleted' => (int) $product->isDeleted(),
		]);
		return true;
	}

	private function checkProduct(iProduct $product): void {
		$id = $this->find_product_model($product->getArticle())['id'] ?? null;
		if ($id && (int) $id !== $product->getProductId())
			throw new \Exception('This product already exists');
	}

	protected function create_product(int $category_id, string $article, string $title, float $selling_price, array $model): int {
		$article = self::formattingAsVarchar($article);
		$title = self::formattingAsVarchar($title);
		$model['unit'] = self::formattingAsVarchar($model['unit'] ?? '');
		$selling_price = self::formattingAsPrice($selling_price);

		if ($this->find_product_model($article))
			throw new \Exception('This product already exists');

		$products_schema = $this->getStorageProductsSchema();
		$pragma_account_id = $this->getPragmaAccountId();

		$sql = "INSERT INTO $products_schema (`account_id`, `category_id`, `article`, `title`, `selling_price`, `unit`)
                VALUES ($pragma_account_id, $category_id, :article, :title, $selling_price, :unit)";

		$flag = self::execute($sql, ['article' => $article, 'title' => $title, 'unit' => $model['unit'] ?? '']);

		if (!$flag)
			throw new \Exception('Failed to create product: ' . $title);

		return self::last_id();
	}

	protected function delete_product(int $product_id): bool {
		$products_schema = $this->getStorageProductsSchema();
		$pragma_account_id = $this->getPragmaAccountId();

		$sql = "DELETE FROM $products_schema 
                WHERE `id` = $product_id AND `account_id` = $pragma_account_id";

		return self::execute($sql);
	}

	protected function fetchProductModels(array $product_id): array {
		if(!count($product_id)) return [];
		$ids_str = implode(',', $product_id);
		$products_schema = $this->getStorageProductsSchema();
		$sql = $this->sql() . "AND $products_schema.`id` IN($ids_str)";
		return self::query($sql);
	}

	protected function get_product_model(int $product_id): array {
		$products_schema = $this->getStorageProductsSchema();
		$sql = $this->sql() . "AND $products_schema.`id` = $product_id";
		$result = self::query($sql)[0] ?? null;
		if (!$result)
			throw new \Exception('Product not found');
		return $result;
	}

	protected function find_product_model(string $article) {
		$products_schema = $this->getStorageProductsSchema();
		$article = trim($article);
		$article = $article ? self::escape(trim($article)) : null;
		if(is_null($article))
			$sql = $this->sql() . "AND $products_schema.`article` IS NULL";
		else
			$sql = $this->sql() . "AND LOCATE($article, $products_schema.`article`)";
		return self::query($sql)[0] ?? null;
	}

	protected function get_category_products($category_id): array {
		if ($category_id)
			$sql = $this->sql() . "AND `category_id` = $category_id";
		else
			$sql = $this->sql();

		return self::query($sql);
	}

	private function sql(): string {
		$products_schema = $this->getStorageProductsSchema();
		$pragma_account_id = $this->getPragmaAccountId();

		return "SELECT 
                    $products_schema.`id` AS `id`,
                    $products_schema.`category_id` AS `category_id`,
                    $products_schema.`title` AS `title`,
                    $products_schema.`article` AS `article`,
                    $products_schema.`unit` AS `unit`,
                    $products_schema.`selling_price` AS `selling_price`
                FROM $products_schema
                WHERE $products_schema.`account_id` = $pragma_account_id ";
	}

	public function getPragmaAccountId(): int {
		return $this->pragma_account_id;
	}

	static function formattingModel(array $model): array {
		foreach ($model as $key => $value)
			if (gettype($value) === 'string')
				$model[$key] = self::formattingAsVarchar($value);

		$model['selling_price'] = round($model['selling_price'], 3);

		return $model;
	}

	static function formattingAsVarchar(string $string): string {
		return trim(substr(trim($string), 0, 256));
	}

	static function formattingUnit(string $string): string {
		return trim(substr(trim($string), 0, 12));
	}

	static function formattingAsPrice(float $price): float {
		return round($price, 3);
	}
}