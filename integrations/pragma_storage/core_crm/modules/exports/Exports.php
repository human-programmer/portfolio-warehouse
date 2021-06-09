<?php


namespace PragmaStorage;



require_once __DIR__ . '/ExportsSchema.php';
require_once __DIR__ . '/export/Export.php';
require_once __DIR__ . '/../../business_rules/exports/iExports.php';
require_once __DIR__ . '/../priorities/StorePriorities.php';
require_once __DIR__ . '/ExportsLoader.php';

class Exports extends ExportsSchema implements iExports {
	use ExportsLoader;

	public function __construct(private IStoreApp $app) {
		parent::__construct($app->getPragmaAccountId());
	}

	public function getEntityExports(iEntity $entity): array {
		$models = parent::findEntityExports($entity->getPragmaEntityId());

		foreach ($models as $model)
			$exports[] = $this->findInBuffer($model['id']) ?? $this->createAndSaveInBuffer($model);

		return $exports ?? [];
	}

	public function getExport(int $export_id): iExport {
		return $this->findInBuffer($export_id) ?? $this->create($export_id);
	}

	function getExports(array $ids): array {
		$ids = array_unique($ids);
		foreach ($ids as $id) {
			$export = $this->findInBuffer($id);
			if ($export)
				$exports[] = $export;
			else
				$to_create[] = $id;
		}
		$fromDb = isset($to_create) ? $this->create_exports($to_create) : [];
		return array_merge($fromDb, $exports ?? []);
	}

	function saveExport(iExport $export): bool {
		return parent::updateProductExport($export->getExportId(), $export->getQuantity(), $export->getSellingPrice(), $export->getStatusId());
	}

	function createOrGetExport(iEntity $entity, iProduct $product): iExport {
		return $this->findExport($entity, $product) ?? $this->_createExport($entity, $product, 0);
	}

	function deleteEntityExport(iEntity $entity, iProduct $product): bool {
		$export = $this->findExport($entity, $product);
		return $export ? $export->delete() : true;
	}

	function deleteExport(iExport $export): bool {
        $this->validToDelete($export);
		$export->setStatus(EXPORT_STATUS_LINKED);
		$flag = parent::deleteProductExport($export->getExportId());
		$export->setDeleted();
		return $flag;
	}

	private function validToDelete(IExportModel $exportModel) : void {
        if($exportModel->getClientType() === STORE_SOURCE)
            throw new \Exception("Impossible to delete STORE_SOURCE type");
    }

	private function _createExport(iEntity $entity, iProduct $product, float $selling_price): iExport {
		$status_id = self::getStatuses()->getStatusByCode('linked')->getStatusId();
		$export_id = parent::createProductExport($entity->getPragmaEntityId(), $product->getProductId(), $status_id, 0, $selling_price);
		return $this->getExport($export_id);
	}

	function findExport(iEntity $entity, iProduct $product) {
		$export = $this->findInBufferByEntity($entity->getPragmaEntityId(), $product->getProductId());
		if ($export) return $export;
		$model = $this->findEntityExport($entity->getPragmaEntityId(), $product->getProductId());
		if ($model) return $this->createAndSaveInBuffer($model);
		return null;
	}

	private function create(int $export_id): iExport {
		$model = $this->getProductExportModel($export_id);
		return $this->createAndSaveInBuffer($model);
	}

	private function create_exports(array $export_ids): array {
		$models = $this->getProductExportModels($export_ids);
		foreach ($models as $model)
			$exports[] = $this->createAndSaveInBuffer($model);
		return $exports ?? [];
	}

	private function createAndSaveInBuffer(array $model): iExport {
		$export = $this->findInBuffer($model['id']) ?? $this->createInstance($model);
		$this->addInBuffer($export);
		return $export;
	}

	private function createInstance(array $model): iExport {
		return new Export($this, $model);
	}

//	private function getStorePriorities(array|int $exportsId): array {
//		$exportsId = is_array($exportsId) ? $exportsId : [$exportsId];
//		$fabric = $this->getStorePrioritiesFabric();
//		return $fabric->getPriorities($exportsId);
//	}

	private static function getStatuses(): iStatuses {
		return PragmaFactory::getStatuses();
	}

	function save(iExport $export): bool {
		return parent::updateProductExport(
			$export->getExportId(),
			$export->getQuantity(),
			$export->getSellingPrice(),
			$export->getStatusId()
		);
	}

	function createExports(iEntity $entity, array $models): array {
		foreach ($models as $model)
			$exports[] = $this->createExport($entity, $model['product'], $model['quantity'], $model['selling_price']);
		return $exports ?? [];
	}

	function createExport(iEntity $entity, iProduct $product, float $quantity, float $selling_price): iExport {
		$export = $this->_createExport($entity, $product, $selling_price);

		$export->setQuantity($quantity);

		$entity->updateStatus();

		return $export;
	}

	function updateExports(int $product_id): bool {
		$exports = $this->getExportsForUpdate($product_id);
		foreach ($exports as $export)
			$export->updateDetails();
		return true;
	}

	public function getExportsForUpdate(int $product_id): array {
		$models = parent::getProductsExports($product_id);

		foreach ($models as $model)
			if ($model['pragma_entity_id'])
				$exports[] = $this->findInBuffer($model['id']) ?? $this->createAndSaveInBuffer($model);

		return $exports ?? [];
	}

	function getStoreApp(): IStoreApp {
		return $this->app;
	}

	function getStorePrioritiesFabric(): IStorePriorities {
		return $this->getStoreApp()->getStorePriorities();
	}

	function getDeficitExports(int $product_id): array {
		$id = self::getDeficitExportsId([$product_id]);
		$exports = self::getExports($id);
		return self::filterExports($exports, STORE_SOURCE);
	}

	private static function filterExports(array $exports, int $client_type): array {
	    foreach($exports as $export)
	        if($export->getClientType() !== $client_type)
	            $result[] = $export;
        return $result ?? [];
    }

    function createExportFromStruct(IExportModel $export_struct): iExport {
        $export_id = $this->createLinkedExportsRow($export_struct);
        $export = $this->getExport($export_id);
        $export->setStatus($export_struct->getStatusId());
        return $export;
    }

    function createLinkedTravelsExportModel(int $product_id): IExportModel {
        return new ExportStruct([
            'pragma_entity_id' => null,
            'product_id' => $product_id,
            'selling_price' => 0,
            'status_id' => EXPORT_STATUS_LINKED,
            'export_id' => 0,
            'client_type' => STORE_SOURCE,
            'store_priorities' => null,
        ]);
    }
}