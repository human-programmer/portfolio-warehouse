<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../CONSTANTS.php';
require_once __DIR__ . '/Storage.php';
require_once __DIR__ . '/../../../pragmacrm/iIntegrationActionsHandler.php';


use PragmaCRM\iIntegrationActionsHandler;

class StorageActionsHandler extends \PragmaStorage\Storage implements iIntegrationActionsHandler {
	public function __construct(int $pragma_account_id) {
		parent::__construct($pragma_account_id);
	}

	public function updateEntitiesEventHandler(array $entities): array {
		foreach ($entities as $entity)
			PragmaFactory::getEntities()->getEntity($entity->getPragmaEntityId())->updateStatus();

		return [];
	}

	public function deleteEntitiesEventHandler(array $entities): array {
		foreach ($entities as $entity)
			PragmaFactory::getEntities()->getEntity($entity->getPragmaEntityId())->delete();

		return [];
	}

	public function getWidgetName(): string {
		return WIDGET_NAME;
	}

	public function isMyName(string $widget_name): bool {
		return $widget_name === $this->getWidgetName();
	}

	public function afterDeleteEntitiesEventHandler(array $entities): array {
		return [];
	}
}