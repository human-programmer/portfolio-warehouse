<?php


namespace Templater\Pragma;


require_once __DIR__ . '/../business_rules/IDocLink.php';

class DocLink implements IDocLink {
	private int|null $templateId;
	private int $targetFileId;
	private int $entityId;
	private string $entityType;

	function __construct(array $params) {
		$this->entityId = $params['entity_id'];
		$this->entityType = $params['entity_type'];
		$this->templateId = $params['template_id'] ?? null;
		$this->targetFileId = $params['file_id'];
	}

	function getTemplateFileId(): int|null {
		return $this->templateId;
	}

	function getFileId(): int {
		return $this->targetFileId;
	}

	function getEntityId(): int {
		return $this->entityId;
	}

	function getEntityType(): string {
		return $this->entityType;
	}

	function toArray(): array {
		return [
			'entity_id' => $this->getEntityId(),
			'entity_type' => $this->getEntityType(),
			'template_id' => $this->getTemplateFileId(),
			'file_id' => $this->getFileId(),
		];
	}
}