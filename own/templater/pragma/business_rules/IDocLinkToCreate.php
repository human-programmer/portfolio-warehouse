<?php

namespace Templater\Pragma;

interface IDocLinkToCreate {
	function getTemplateFileId(): int|null;
	function getEntityId(): int|null;
	function getEntityType(): string|null;
	function toArray(): array;
}