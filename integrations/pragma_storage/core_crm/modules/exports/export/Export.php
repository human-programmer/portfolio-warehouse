<?php


namespace PragmaStorage;


use PragmaStorage\Export\ExportsTotalPrice;

require_once __DIR__ . '/../../../business_rules/exports/IExportModel.php';
require_once __DIR__ . '/../../../business_rules/exports/iExport.php';
require_once __DIR__ . '/../../../PragmaFactory.php';
require_once __DIR__ . '/ImportExportCalc.php';
require_once __DIR__ . '/ExportStruct.php';
require_once __DIR__ . '/ExportsTotalPrice.php';


class Export extends ExportStruct implements iExport {
	use ImportExportCalc;

	private Exports $exports;
	private bool $deleted = false;

	function __construct(Exports $exports, array $model) {
		parent::__construct($model);
		$this->exports = $exports;
	}
	protected function clearDetails(): bool {
		$details = self::getExportDetails()->getExportDetails($this);
		$this->setStageCurrentImports();
		foreach ($details as $detail)
			$detail->delete();
		$this->updateProductsImportsBalance();
		return true;
	}

	function getProduct(): iProduct {
		return self::getProducts()->getProduct($this->getProductId());
	}

	function getDetailsQuantity(): float {
		$details = $this->getDetails();
		$quantity = 0.0;
		foreach ($details as $detail)
			$quantity += $detail->getQuantity();
		return $quantity;
	}

	function setQuantity(float $quantity) {
		if(parent::getQuantity() === $quantity) return;
		parent::setQuantity($quantity);
		if (!$this->save())
			throw new \Exception('Failed to update export: ' . $this->getExportId());
		$this->updateDetails();
	}

	function setStatus(int $status_id): bool {
		if ($this->getStatusId() === $status_id)
			return true;

		parent::setStatus($status_id);
		if (!$this->save())
			throw new \Exception("Failed to change Export status: $status_id");

		$flag = $this->updateDetails();
		$this->setChangedExportValues();
		return $flag;
	}

	function updateDetails(iProductImport $productImport = null): bool {
		if($productImport && $productImport->getProductId() !== $this->getProductId())
			throw new \Exception("Invalid ProductImport");
		if (!$this->getStatus()->isDetailed())
			$this->clearDetails();
		else
			$this->updateDifDetails($productImport);
		return true;
	}

	function isExported(): bool {
		return $this->getStatus()->isExported();
	}

	function saveDeletedEntity () : void {
		$this->exports::saveDeletedEntityToExportsLink($this->getEntityId(), $this->getExportId());
	}

	function isDeleted(): bool {
		return $this->deleted;
	}

	function delete(): bool {
		return $this->exports->deleteExport($this);
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	function update(array $model): bool {
		$this->updateExportParams($model);
		$this->updateStatusParams($model);
		$this->safeUpdateQuantity($model);
		return $this->save();
	}

	private function updateExportParams(array $model): void {
		foreach ($model as $field_name => $value)
			switch ($field_name) {
				case 'quantity':
					parent::setQuantity($model['quantity']);
					break;
				case 'selling_price':
					parent::setSellingPrice($model['selling_price']);
					break;
			}
	}

	private function updateStatusParams(array $model): void {
		$status_id = $model['status_id'] ? (int)$model['status_id'] : null;
		if ($status_id && $this->getStatusId() !== $status_id)
			$this->setStatus($status_id);
	}

	private function safeUpdateQuantity(array $model): void {
		(int)$model['quantity'] !== $this->getQuantity() && $this->updateDetails();
	}

	private function save(): bool {
		return $this->exports->save($this);
	}

	function setPriorities(array $priorities): void {
		$model['store_priorities'] = $priorities;
		$priorities = self::fetchPriorities($model);
		$this->exports->getStorePrioritiesFabric()->savePriorities($this->getExportId(), $priorities);
		parent::setPriorities($priorities);
	}

	function getHighestPriority(): IStoreExportPriority {
		$availablePriorities = $this->getAvailablePriorities();
		return $availablePriorities[0];
	}

	private function getProducts(): iProducts {
		return PragmaFactory::getProducts();
	}

	private function getStatus(): iStatus {
		return PragmaFactory::getStatuses()->getStatus($this->getStatusId());
	}

	private static function getExportDetails(): iExportDetails {
		return PragmaFactory::getExportDetails();
	}

	private function getStoreApp(): IStoreApp {
		return $this->exports->getStoreApp();
	}

	private function getSelf(): self {
		return $this;
	}

	function getPrioritySort(int|null $store_id): int|null {
		if(!$store_id) return null;
		foreach ($this->getPriorities() as $priority)
			if($priority->getStoreId() === $store_id)
				return $priority->getSort();
		return null;
	}

	function getProductsImports(): array {
		$details = $this->getDetails();
		foreach ($details as $detail)
			$result[] = $detail->getProductImport();
		return $result ?? [];
	}

	function setDeleted(): void {
		$this->deleted = true;
	}

    function getTotalPurchasePrice(): float {
        if($this->getStatusId() === EXPORT_STATUS_LINKED) return 0;
        $calc = new ExportsTotalPrice($this->getDetails());
        return $calc->getTotalPurchasePrice();
    }
}