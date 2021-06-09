<?php


namespace PragmaStorage;


require_once __DIR__ . '/../business_rules/iAmocrmInterface.php';


class AmoEntityInterface implements iAmocrmInterface {
	public function __construct() {
	}

	function getPragmaEntityId(string $entity_type, int $entity_id): int {
		return Factory::getAmocrmEntityInterface()->getAndSavePragmaEntityId($entity_type, $entity_id);
	}
}