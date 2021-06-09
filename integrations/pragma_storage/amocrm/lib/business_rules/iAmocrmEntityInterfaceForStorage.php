<?php


namespace PragmaStorage;


interface iAmocrmEntityInterfaceForStorage {
	function getAndSavePragmaEntityId(string $entity_type, int $entity_id): int;
}