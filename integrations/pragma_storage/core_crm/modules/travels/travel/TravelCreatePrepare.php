<?php


namespace PragmaStorage;


trait TravelCreatePrepare {
	private array $new_added_products = [];

	private function initChangeTravelStage(array $products): void {
		$this->initProducts($products);
		$this->preloadEntities();
		$this->validProducts();
	}

	private function initProducts(array $products): void {
		foreach ($products as $product)
			$this->new_added_products[$product['product_id']] = $product;
	}

	private function preloadEntities(): void {
		$products = $this->getTargetProductsId();
		$this->app->getProducts()->preloadProducts($products);
		$this->app->getProductImports()->preloadProductImports($products);
	}

	protected function validProducts(): void {
		$products = $this->getTargetProductsId();
		foreach ($products as $product_id)
			$this->validProduct($product_id);
	}

	protected function validProduct(int $id): void {
		$successFlag = $this->app->getStores()->allowedForStore($this->getStartStoreId(), $id) ||
            $this->app->getStores()->allowedForStore($this->getEndStoreId(), $id);
		if(!$successFlag) throw new \Exception("This item is not admitted to warehouses");
	}

	protected function getTargetProductsId(): array {
		return array_keys($this->new_added_products);
	}

	private function getNewAddedProducts(): array {
		return $this->new_added_products;
	}

	private function resetNewAddedProducts(): void {
		$this->new_added_products = [];
	}
}