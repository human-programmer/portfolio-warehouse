<?php


namespace PragmaStorage;


interface iAmocrmInterface {
	function getPragmaEntityId(string $entity_type, int $entity_id): int;
}