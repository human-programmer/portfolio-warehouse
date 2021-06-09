<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../business_rules/entities/iEntity.php';
require_once __DIR__ . '/../../PragmaFactory.php';


class Entity implements iEntity {
	private iEntityForStore $entity;

	public function __construct(iEntityForStore $entity) {
		$this->entity = $entity;
	}

	function getAllExports(): array {
		return self::getExports()->getEntityExports($this);
	}

	function getExportStatus() {
		$entity_status_id = $this->entity->findStatusId();
		$entity_pipeline_id = $this->entity->findPipelineId();

		if (!$entity_status_id || !$entity_pipeline_id)
			return null;

		return self::findExportStatus($entity_pipeline_id, $entity_status_id);
	}

	static private function findExportStatus (int $entity_pipeline_id, int $entity_status_id){
		try {
			$status = self::getStatusToStatus()->getExportStatus($entity_pipeline_id, $entity_status_id);
		} catch (\Exception $e) {
		} finally {
			return $status ?? null;
		}
	}

	function setExport(iProduct $product, float $selling_price, float $quantity): bool {
		if ($quantity <= 0)
			return self::getExports()->deleteEntityExport($this, $product);
		$export = self::getExports()->createOrGetExport($this, $product);
		$model = ['quantity' => $quantity, 'selling_price' => $selling_price];
		$export->update($model);
		return true;
	}

	function setChangedExportValues(): void {
		$this->entity->setValueIsChanged('storage_total_selling_price');
		$this->entity->setValueIsChanged('storage_total_profit_price');
		$this->entity->setValueIsChanged('storage_total_purchase_price');
	}

	function getPragmaEntityId(): int {
		return $this->entity->getPragmaEntityId();
	}

	function findResponsibleUserId() {
		return $this->entity->findResponsibleUserId();
	}

	function delete(){
		if ($this->isDeleteExportsStatus())
			$this->deleteExports();
//		else
//			$this->saveEntitiesForExports();
	}

	private function saveEntitiesForExports () : void {
		$exports = $this->getOwnedExports();
		foreach ($exports as $export)
			$export->saveDeletedEntity();
	}

	private function deleteExports () : void {
		$exports = $this->getOwnedExports();
		foreach ($exports as $export)
			$export->delete();
	}

	function updateStatus() {
		self::log('updateStatus - START');

		if (!$this->entity->isChangedStatusOrPipeline())
			return;

		$status = $this->getExportStatus();

		if (!$status)
			return;
		$exports = $this->getOwnedExports();
		foreach ($exports as $export)
			$export->setStatus($status->getStatusId());
	}

	function getOwnedExports(): array {
		return self::getExports()->getEntityExports($this);
	}

	private function isDeleteExportsStatus(): bool {
		$status = $this->getExportStatus();
		return $status ? !$status->isExported() : true;
	}

	private static function getStatusToStatus(): iStatusToStatus {
		return PragmaFactory::getStatusToStatus();
	}

	private static function getExports(): iExports {
		return PragmaFactory::getExports();
	}

	private static function getStatuses(): iStatuses {
		return PragmaFactory::getStatuses();
	}

	static private function log(string $message, $params = null) {
		PragmaFactory::getLogWriter()->add('Entity - ' . $message, $params);
	}
}