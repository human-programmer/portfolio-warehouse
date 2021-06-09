<?php


namespace TemplateEngine\Pragma;


require_once __DIR__ . '/../business_rules/IDocLinkToCreate.php';

class DocLink implements IDocLinkToCreate {
	private int|null $templateId;
	private int $targetFileId;
	private int $entityId;
	private string $entityType;

	function __construct(array $params) {
		$this->entityId = $params['entity_id'];
		$this->entityType = $params['entity_type'];
		$this->templateId = $params['template_id'] ?? null;
	}

	function getTemplateFileId(): int|null {
		return $this->templateId;
	}

	function getEntityId(): int {
		return $this->entityId;
	}

	function getEntityType(): string {
		return $this->entityType;
	}
}