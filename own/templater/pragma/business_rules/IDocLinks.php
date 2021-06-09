<?php


namespace Templater\Pragma;


interface IDocLinks {
	function getLinksOfEntity(string $entity_type, int $entity_id): array;
	static function saveDocLink(IDocLink $link): void;
}