<?php


namespace PragmaStorage;


trait TProductsBuffer {
	private array $products = [];

	private function deleteFromBuffer(int $product_id): void {
		unset($this->products[$product_id]);
	}

	private function findInBuffer(int $product_id): iProduct|null {
		return $this->products[$product_id] ?? null;
	}

	private function addInBuffer(iProduct $product): void {
		$this->products[$product->getProductId()] = $product;
	}

	function getProductsFromBuffer(): array {
		return array_merge([], $this->products);
	}
}