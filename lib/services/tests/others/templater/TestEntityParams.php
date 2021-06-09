<?php


namespace Services\Tests;


class TestEntityParams implements \Services\Amocrm\iAmoEntityParams {
	function __construct(private array $entities, private array $managers, private array $customFields) {}

	function getEntities(): array {
		return $this->entities;
	}

	function getManagers(): array {
		return $this->managers;
	}

	function getCustomFields(): array {
		return $this->customFields;
	}
}