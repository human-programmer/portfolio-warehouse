<?php


namespace PragmaStorage;


require_once __DIR__ . '/business_rules/iAmocrmStorage.php';
require_once __DIR__ . '/../../core_crm/modules/Storage.php';
require_once __DIR__ . '/modules/EntityPipelines.php';


class AmocrmStorage extends Storage implements iAmocrmStorage {
	private EntityPipelines $entity_pipelines;

	public function __construct() {
		parent::__construct(Factory::getPragmaAccountId());
		$this->entity_pipelines = new EntityPipelines(Factory::getPragmaAccountId());
	}

	function createExports(string $entity_type, int $entity_id, array $models): array {
		$pragma_entity_id = Factory::getAmocrmInterface()->getPragmaEntityId($entity_type, $entity_id);
		return $this->createPragmaExports($pragma_entity_id, $models);
	}

	function createExport(string $entity_type, int $entity_id, int $product_id, float $quantity, float $selling_price): iExport {
		$pragma_entity_id = Factory::getAmocrmInterface()->getPragmaEntityId($entity_type, $entity_id);
		return parent::createPragmaExport($pragma_entity_id, $product_id, $quantity, $selling_price);
	}

	function deleteExport(int|array $exports_id): bool {
		return parent::deleteExport($exports_id);
	}

	function getPipelinesOfAccount(): array {
		return $this->entity_pipelines->getPipelines();
	}
}