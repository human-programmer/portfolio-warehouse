<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../business_rules/products/IProductCreationHandler.php';

class ProductsToCategory extends PragmaStoreDB implements IProductCreationHandler {
	private array $links;
	function __construct(private int $account_id) {
		parent::__construct();
	}

	function productCreateEvent(iProduct $product): void {
		if(!isset($this->links) || array_search($product->getProductId(), $this->links[$product->getCategoryId()] ?? []) !== false) return;
		$this->links[$product->getCategoryId()][] = $product->getProductId();
	}

	function getCategoryId(int $product_id): int {
		$result = $this->findCategoryId($product_id);
		if($result) return $result;
		$this->loadTargetProductId($product_id);
		return $this->findCategoryId($product_id) ?? throw new \Exception("category_id not found");
	}

	private function findCategoryId(int $product_id): int|null {
		foreach ($this->getAllLinks() as $category_id => $products_id)
			if(array_search($product_id, $products_id) !== false)
				return $category_id;
		return null;
	}

	function getProductsIdOfCategory(int $category_id): array {
		return $this->getAllLinks()[$category_id] ?? [];
	}

	function getAllLinks(): array {
		if(isset($this->links)) return $this->links;
		$this->loadLinks();
		return $this->links;
	}

	private function loadLinks(): void {
		$products = $this->getAccountsProducts();
		foreach ($products as $product)
			$this->addLinkInBuffer($product);
		$this->links = $this->links ?? [];
	}

	private function addLinkInBuffer(array $product): void {
		$this->links[$product['category_id']][] = (int) $product['id'];
	}

	function getAccountsProducts(): array {
		$sql = $this->sql();
		return self::querySql($sql);
	}

	private function loadTargetProductId(int $product_id): void {
		$row = $this->getAccountsProductRow($product_id);
		$this->addLinkInBuffer($row);
	}

	private function getAccountsProductRow(int $product_id): array {
		$sql = $this->sql("id = $product_id");
		return self::querySql($sql)[0] ?? throw new \Exception("product_id not found");
	}

	private function sql(string $condition = ''): string {
		$products = self::getStorageProductsSchema();
		$condition = $condition ? "AND $condition": '';
		return "SELECT id, category_id FROM $products WHERE account_id = $this->account_id $condition";
	}
}