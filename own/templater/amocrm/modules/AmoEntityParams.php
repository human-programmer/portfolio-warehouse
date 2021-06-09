<?php


namespace Templater\Amocrm;

require_once __DIR__ . '/../../../../lib/services/business_rules/amocrm/others/templater/iAmoEntityParams.php';


class AmoEntityParams implements \Services\Amocrm\iAmoEntityParams {
	private array $entities;
	private array $managers;
	private array $customFields;

	function __construct(array $params) {
		$this->entities = is_array($params['entities']) ? $params['entities'] : [];
		$this->managers = is_array($params['managers']) ? $params['managers'] : [];
		$this->customFields = is_array($params['customFields']) ? $params['customFields'] : [];
	}

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