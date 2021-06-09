<?php


namespace TemplateEngine\Pragma;


interface ITemplateDirs {
	function getTemplatesDirId(): int;
	function getCardDirId(int $entity_id, string $entity_type): int;
}