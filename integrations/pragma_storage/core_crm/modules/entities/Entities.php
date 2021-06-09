<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../PragmaFactory.php';
require_once __DIR__ . '/Entity.php';
require_once __DIR__ . '/../../business_rules/entities/iEntities.php';


class Entities implements iEntities {
	private int $pragma_account_id;

	private array $entities = [];

	public function __construct(int $pragma_account_id) {
		$this->pragma_account_id = $pragma_account_id;
	}

	public function getPragmaAccountId(): int {
		return $this->pragma_account_id;
	}

	function getEntity(int $pragma_entity_id): iEntity {
		return $this->findInBuffer($pragma_entity_id) ?? $this->_create($pragma_entity_id);
	}

	private function findInBuffer(int $pragma_entity_id) {
		foreach ($this->entities as $entity)
			if ($entity->getPragmaEntityId() === $pragma_entity_id)
				return $entity;

		return null;
	}

	private function _create(int $pragma_entity_id): iEntity {
		$pragma_crm_entity = PragmaFactory::getCrmStorage()->getEntityForStore($pragma_entity_id);
		$entity = new Entity($pragma_crm_entity);
		$this->entities[] = $entity;

		return $entity;
	}
}