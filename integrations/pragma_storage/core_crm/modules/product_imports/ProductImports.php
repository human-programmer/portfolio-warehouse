<?php


namespace PragmaStorage;


require_once __DIR__ . '/ProductImportSchema.php';
require_once __DIR__ . '/product_import/ProductImport.php';
require_once __DIR__ . '/../../business_rules/product_imports/iProductImports.php';
require_once __DIR__ . '/TProductImportBuffer.php';
require_once __DIR__ . '/ProductImportsLoader.php';


class ProductImports extends ProductImportSchema implements iProductImports {
	use ProductImportsLoader;

	public function __construct(protected IStoreApp $app) {
		parent::__construct($app->getPragmaAccountId());
	}

	public function getProductDeficit(int $product_id, int $store_id): iProductImport {
		$deficitProductImport = $this->findInBufferDeficit($product_id, $store_id);
		if($deficitProductImport) return $deficitProductImport;
		$this->loadStoreDeficit($product_id, $store_id);
		return $this->findInBufferDeficit($product_id, $store_id);
	}

	public function getProductImport(int $product_import_id): iProductImport {
		return $this->findInBuffer($product_import_id) ?? $this->getFromDb($product_import_id);
	}

	function getProductImports(int $product_id, int|array $store_id = null): array {
		$this->isBufferPreloadedProduct($product_id) || $this->loadFromDb($product_id);
		$productImports =  $this->findInBufferByProductId($product_id);
		return is_null($store_id) ? $productImports : self::filterProductImportsByStore($productImports, $store_id);
	}

	function getImportProductImports(int $import_id): array {
		$models = parent::get_import_product_imports($import_id);
		foreach ($models as $model)
			$imports[] = $this->createInstance($model);
		return $imports ?? [];
	}

	function getAllImportProductImports(array $import_ids): array {
		$models = parent::loadProductImportsFromDb($import_ids);
		foreach ($models as $model)
			$imports[] = $this->createInstance($model);
		return $imports ?? [];
	}

	private function createInstance(array $product_import_model): iProductImport {
		$product_import = $this->findInBuffer($product_import_model['product_import_id']) ?? new ProductImport($this->app, $this, $product_import_model);
		$this->addInBuffer($product_import);
		return $product_import;
	}

	function findFreeProductImport(int $product_id, int|null $store_id = null): iProductImport|null{
		$product_imports = $this->getProductImports($product_id);

		foreach ($product_imports as $product_import)
			if ($product_import->getFreeBalanceQuantity() > 0)
				if($store_id && $store_id === $product_import->findStoreId())
					return $product_import;
				else if(!$store_id)
					return $product_import;

		return null;
	}

	function createProductImport(int $import_id, int $product_id, float $quantity, float $purchase_price): iProductImport {
		$this->validProductImport($import_id, $product_id);
		$model = [
				'import_id' => $import_id,
				'product_id' => $product_id,
				'import_quantity' => 0,
				'free_balance_quantity' => 0,
				'balance_quantity' => 0,
				'purchase_price' => $purchase_price,
			];
		$id = parent::create_product_import($model);
		$product_import = $this->getProductImport($id);
		$product_import->setQuantity($quantity);
		return $product_import;
	}

	function deleteProductImport(iProductImport $product_import): void {
	    $this->validToDelete($product_import);
		$this->deleteFromDb($product_import);
		$this->deleteFromBuffer($product_import->getProductImportId());
	}

	private function validToDelete(iProductImport $productImport): void {
        if($productImport->getSource() === STORE_SOURCE)
            throw new \Exception("Impossible to delete STORE_SOURCE type");
    }

	private function deleteFromDb(iProductImport $productImport): void {
		if (!parent::delete_product_import($productImport->getProductImportId()))
			throw new \Exception('Failed to delete ProductImport: ' . $productImport->getProductImportId());
	}

	function save(IProductImportModel $productImportModel): void {
		parent::updateProductImport($productImportModel->getProductImportId(), $productImportModel->getPurchasePrice());
	}

	function saveProductImport(IProductImportModel $model): void {
		$this->updateProductImportModel($model);
	}

	function createModel(array $model): IProductImportModel {
		return new ProductImportModel($model);
	}

	function create(IProductImportModel $model): iProductImport {
		$this->validProductImport($model->getImportId(), $model->getProductId());
		$id = $this->createNewProductImportRow($model);
		return $this->getProductImport($id);
	}

	private function validProductImport(int $import_id, int $product_id): void {
		$import = $this->app->getImports()->getImport($import_id);
		$store_id = $import->getStoreId();
		$this->validProductImportForStore($store_id, $product_id);
	}

	private function validProductImportForStore(int $store_id, int $product_id): void {
        $flag = $this->app->getStores()->allowedForStore($store_id, $product_id);
        $flag || throw new \Exception("This product ('$product_id') is not valid for warehouse '$store_id'");
    }

	function createTravelsImport(ITravelModel $travelModel, int $product_id): iProductImport {
	    $this->validProductImport($travelModel->getEndImportId(), $product_id);
		$model = self::createTravelProductImportModel($travelModel, $product_id);
		$productImportId = $this->createNewProductImportRow($model);
		return $this->getProductImport($productImportId);
	}

	private static function createTravelProductImportModel(ITravelModel $travelModel, int $product_id): IProductImportModel {
		return new ProductImportModel([
			'import_id' => $travelModel->getEndImportId(),
			'product_id' => $product_id,
			'import_quantity' => 0,
			'purchase_price' => 0,
			'source' => STORE_SOURCE,
			'free_balance_quantity' => 0,
			'balance_quantity' => 0,
		]);
	}

    function getByIdList(array $product_import_id): array{
        $this->preloadTargetProductsImports($product_import_id);
        return $this->getFromBuffer($product_import_id);
    }

    function getApp(): IStoreApp {
        return $this->app;
    }
}