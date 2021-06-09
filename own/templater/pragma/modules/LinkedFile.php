<?php


namespace Templater\Pragma;


use Files\iFile;

require_once __DIR__ . '/../business_rules/ALinkedFile.php';

class LinkedFile extends ALinkedFile {

	function __construct(private iFile $file, private IDocLink $link) {}

	function getFileId(): int {
		return $this->link->getFileId();
	}

	function getTemplateFileId(): int|null {
		return $this->link->getTemplateFileId();
	}

	function getEntityId(): int|null {
		return $this->link->getEntityId();
	}

	function getEntityType(): string|null {
		return $this->link->getEntityType();
	}

	function toArray(): array {
		return array_merge($this->file->getModel(), $this->link->toArray());
	}

	function getId(): int {
		return $this->file->getId();
	}

	function getUniqueName(): string {
		return $this->file->getUniqueName();
	}

	function getExternalLink(): string {
		return $this->file->getExternalLink();
	}

	function getFullUniqueName(): string {
		return $this->file->getFullUniqueName();
	}

	function getContent(): mixed {
		return $this->file->getContent();
	}

	function getPath(): string {
		return $this->file->getPath();
	}

	function getExtension(): string {
		return $this->file->getExtension();
	}

	function getTitle(): string {
		return $this->file->getTitle();
	}

	function getName(): string {
		return $this->file->getName();
	}

	function getSize(): int {
		return $this->file->getSize();
	}

	function getModel(): array {
		return $this->toArray();
	}

	function getExternalModel(): array {
		return array_merge($this->file->getExternalModel(), $this->link->toArray());
	}

	function getGroup(): string {
		return $this->file->getGroup();
	}
}