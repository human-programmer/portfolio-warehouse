<?php


namespace PragmaStorage;

require_once __DIR__ . '/../../business_rules/categories_to_stores/ICategoriesToStores.php';
require_once __DIR__ . '/CategoryStoreLink.php';
require_once __DIR__ . '/CategoriesToStoresSchema.php';
require_once __DIR__ . '/ProductsToCategory.php';


class CategoriesToStores extends CategoriesToStoresSchema implements ICategoriesToStores {
	private array $links;
	private ProductsToCategory $productsToCategory;

	function __construct(private IStoreApp $app) {
		parent::__construct($this->app->getPragmaAccountId());
		$this->productsToCategory = new ProductsToCategory($this->app->getPragmaAccountId());
	}

	function saveCategoryLinks(int $category_id, array $stores_id, int $status = null): void {
		$status = $status ?? UNARCHIVED_STATUS;
		foreach ($stores_id as $store_id)
			$links[] = new CategoryStoreLinkStruct(['store_id' => $store_id, 'category_id' => $category_id, 'link_status' => $status]);
		$this->saveLinks($links ?? []);
	}

	function saveLinks(array $links): void {
		self::saveLinkRows($links);
		$this->addLinksInBuffer($links);
	}

	function getAllLinks(): array {
		if(isset($this->links)) return $this->bufferLinks();
		$this->loadLinks();
		return $this->bufferLinks();
	}

	private function bufferLinks(): array {
		return array_values($this->links);
	}

	private function loadLinks(): void {
		$links = $this->getLinksRows();
		foreach ($links as $link)
			$result[] = new CategoryStoreLink($this->app, $link);
		$this->addLinksInBuffer($result ?? []);
		$this->links = $this->links ?? [];
	}

	private function addLinksInBuffer(array $links): void {
		foreach ($links as $link)
			$this->addLinkInBuffer($link);
	}

	private function addLinkInBuffer(ICategoryStoreLinkStruct $link): void {
		$str_id = $link->getStoreId() . '.' . $link->getCategoryId();
		$this->links[$str_id] = $link;
	}

	function getProductsIdInStore(int $store_id): array {
		$categories = $this->getCategoriesIdInStore($store_id);
		foreach ($categories as $category_id)
			$result = array_merge($result ?? [], $this->getProductsIdOfCategory($category_id));
		return $result ?? [];
	}

	function getProductsIdOfCategory(int $category_id): array {
		return $this->productsToCategory->getProductsIdOfCategory($category_id);
	}

	function getCategoriesIdInStore(int $store_id): array {
		$links = $this->getAllLinks();
		foreach ($links as $link)
			if($link->getStoreId() === $store_id)
				$id[] = $link->getCategoryId();
		return $id ?? [];
	}

	function getStoresIdForProduct(int $product_id): array {
		$category_id = $this->getCategoryIdOfProduct($product_id);
		return $this->getStoresForCategory($category_id);
	}

	function getCategoryIdOfProduct(int $product_id): int {
		return $this->productsToCategory->getCategoryId($product_id);
	}

	function getStoresForCategory(int $category_id): array {
		$id = $this->findStoresForCategory($category_id);
		if(isset($id)) return $id;
		$this->saveDefaultLinks($category_id);
		return $this->findStoresForCategory($category_id) ?? throw new \Exception("Links Error");
	}

	private function findStoresForCategory(int $category_id): array|null {
		$links = $this->getAllLinks();
		foreach ($links as $link)
			if($link->getCategoryId() === $category_id)
				$id[] = $link->getStoreId();
		return $id ?? null;
	}

	private function saveDefaultLinks(int $category_id): void {
		$stores_id = $this->getAllStoresId();
		$this->saveCategoryLinks($category_id, $stores_id);
	}

	private function getAllStoresId(): array {
		$stores = $this->app->getStores();
		foreach ($stores as $store)
			$result[] = $store->getStoreId();
		return $result ?? throw new \Exception("Store is missing");
	}

	function getProductCreationHandler(): IProductCreationHandler {
		return $this->productsToCategory;
	}
}