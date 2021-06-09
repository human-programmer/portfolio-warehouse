<?php


namespace PragmaStorage;



require_once __DIR__ . '/../../business_rules/products/iProduct.php';
require_once __DIR__ . '/../../../CONSTANTS.php';
require_once __DIR__ . '/TProductDependencies.php';


class Product implements iProduct {
	use TProductDependencies;

	private Products $products;
	private int $product_id;
	private int $category_id;
	private string $article;
	private string $title;
	private string $unit;
	private float $selling_price;
	private bool $deleted;
	private array $linkedStores;

	function __construct(IStoreApp $app, Products $products, array $model) {
		$this->dependenciesInit($app);
		$this->products = $products;
		$this->product_id = $model['id'];
		if ($model['category_id'])
			$this->category_id = $model['category_id'];
		$this->article = $model['article'];
		$this->title = $model['title'];
		$this->unit = $model['unit'] ?? '';
		$this->deleted = isset($model['deleted']) && !!$model['deleted'];
		$this->selling_price = $model['selling_price'];
	}


	function getProductId(): int {
		return $this->product_id;
	}

	function getCategoryId() {
		return $this->category_id ?? null;
	}

	function getArticle(): string {
		return $this->article;
	}

	function getTitle(): string {
		return $this->title;
	}

	function getUnit() : string {
		return $this->unit;
	}

	function getSellingPrice(): float {
		return $this->selling_price;
	}

	function isDeleted(): bool {
		return $this->deleted;
	}

	function delete(): bool {
		$product_imports = $this->getOwnedProductImports();

		if (count($product_imports)) {
			throw new \Exception('Невозможно удалить товар', PRODUCT_IS_USED_DELETE_ERROR);
//            if (!$this->products->setDeleted($this))
//                throw new \Exception('Failed to archive Product');
		} else if (!$this->products->deleteProduct($this->getProductId()))
			throw new \Exception('Failed to delete Product');

		$this->deleted = true;

		return true;
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	function toArray(): array {
		return [
			'id' => $this->getProductId(),
			'category_id' => $this->getCategoryId() ?? null,
			'article' => $this->getArticle(),
			'title' => $this->getTitle(),
			'selling_price' => $this->getSellingPrice(),
			'unit' => $this->getUnit(),
		];
	}

	function update(array $model): bool {
		foreach ($model as $field_name => $value)
			switch ($field_name) {
				case 'category_id':
					if ((int) $value)
						$this->category_id = (int)$value;
					break;

				case 'article':
					$value = self::formattingAsVarchar($value);

					if ($value !== $this->getArticle() && $this->products->findProduct($value))
						throw new \Exception('This product already exists "' . $value . '"', PRODUCT_ARTICLE_EXISTS);

					$this->article = $value;

					break;

				case 'title':
					$this->title = self::formattingAsVarchar($value);
					break;

				case 'selling_price':
					$this->selling_price = round((float) $value, 3);;
					break;

				case 'unit':
					$this->unit = substr(trim($value), 0, 12);
					break;
			}
		$this->products->save($this);
		return true;
	}

	function getExports(): array {
		$product_imports = $this->getOwnedProductImports();

		foreach ($product_imports as $product_import)
			$exports = array_merge($exports ?? [], $product_import->getOwnedExports());

		return $exports ?? [];
	}

	function getOwnedProductImports(): array {
		return $this->getProductImports()->getProductImports($this->getProductId());
	}

	static function formattingAsVarchar (string $string) : string {
		return substr(trim($string), 0, 256);
	}

	function getFreeQuantity(int $store_id): float {
		$product_imports = $this->getProductImports()->getProductImports($this->getProductId(), $store_id);
		$result = 0.0;
		foreach ($product_imports as $import)
			$result += $import->getFreeBalanceQuantity();
		return $result;
	}

	function getLinkedStores(): array {
		return $this->app->getCategoriesToStores()->getStoresForCategory($this->getCategoryId());
	}
}