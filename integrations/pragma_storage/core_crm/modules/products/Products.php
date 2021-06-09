<?php


namespace PragmaStorage;


require_once __DIR__ . '/ProductsSchema.php';
require_once __DIR__ . '/../../business_rules/products/iProducts.php';
require_once __DIR__ . '/Product.php';
require_once __DIR__ . '/TProductsBuffer.php';
require_once __DIR__ . '/TProductDependencies.php';


class Products extends ProductsSchema implements iProducts {
	use TProductsBuffer, TProductDependencies;

	private array $create_event_handlers = [];

	function __construct(IStoreApp $app) {
		parent::__construct($app->getPragmaAccountId());
		$this->dependenciesInit($app);
	}


	function deleteProduct(int $product_id): bool {
		$this->deleteFiles($product_id);
		$this->deleteFromDb($product_id);
		$this->deleteFromBuffer($product_id);
		return true;
	}

	private function deleteFromDb(int $product_id): void {
		if (!$this->delete_product($product_id))
			throw new \Exception("Failed to delete product: $product_id");
	}

	private function deleteFiles(int $product_id) : void {
//		PragmaFactory::getFiles()->deleteProductsFiles($product_id);
	}

	function getProducts(int $category_id = null): array {
		$models = $this->get_category_products($category_id);
		foreach ($models as $product_model)
			$products[] = $this->findInBuffer($product_model['id']) ?? $this->_create($product_model);
		return $products;
	}

	function findProduct(string $article) {
		$article = trim($article);
		foreach ($this->products as $product)
			if ($product->getArticle() === $article)
				return $product;
		$model = $this->find_product_model($article);
		return $model ? $this->_create($model) : null;
	}

	function getProduct(int $product_id): iProduct {
		return $this->findInBuffer($product_id) ?? $this->loadProduct($product_id);
	}

	function createProduct(int $category_id, string $article, string $title, float $selling_price, array $model = []): iProduct {
		$product_model = $this->createProductRow($category_id, $article, $title, $selling_price, $model);
		$product = $this->_create($product_model);
		$this->createProductTrigger($product);
		return $product;
	}

	private function createProductRow(int $category_id, string $article, string $title, float $selling_price, array $model = []): array {
		$article = self::formattingAsVarchar($article);
		$title = self::formattingAsVarchar($title);
		$model['unit'] = self::formattingUnit($model['unit'] ?? '');
		$selling_price = self::formattingAsPrice($selling_price);

		$product_id = $this->create_product($category_id, $article, $title, $selling_price, $model);
		return [
			'id' => $product_id,
			'category_id' => $category_id,
			'article' => $article,
			'title' => $title,
			'selling_price' => $selling_price,
			'unit' => $model['unit'] ?? '',
		];
	}

	private function loadProduct(int $product_id): iProduct {
		$model = $this->get_product_model($product_id);
		return $this->_create($model);
	}

	private function _create(array $model): iProduct {
		$model = self::formattingModel($model);
		$product = $this->findInBuffer($model['id']) ?? new Product($this->app, $this, $model);
		$this->addInBuffer($product);
		return $product;
	}

	function save(iProduct $product): void {
		parent::update_product($product);
	}

	function setDeleted(iProduct $product): bool {
		return true;
	}

	function preloadProducts(array $product_id): void {
		$fetchFromDb = $this->filterNotExistsInBuffer($product_id);
		$this->loadProducts($fetchFromDb);
	}

	private function filterNotExistsInBuffer(array $product_id): array {
		foreach ($product_id as $id)
			if(!$this->findInBuffer($id))
				$result[] = $id;
		return $result ?? [];
	}

	private function loadProducts(array $product_id): void {
		$models = $this->fetchProductModels($product_id);
		foreach ($models as $model)
			$this->_create($model);
	}

	function addHandler(IProductCreationHandler $handler): void {
		$this->create_event_handlers[] = $handler;
	}

	private function createProductTrigger(iProduct $product): void {
		foreach ($this->create_event_handlers as $handler)
			$handler->productCreateEvent($product);
	}
}