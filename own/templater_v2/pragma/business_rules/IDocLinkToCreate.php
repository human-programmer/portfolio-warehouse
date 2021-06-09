<?php

namespace TemplateEngine\Pragma;

interface IDocLinkToCreate {
	function getTemplateFileId(): int|null;
	function getEntityId(): int|null;
	function getEntityType(): string|null;
}