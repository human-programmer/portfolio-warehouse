<?php


namespace PragmaStorage;

require_once __DIR__ . '/ICreationTravelModel.php';

interface ITravelModel extends ICreationTravelModel {
	function getTravelId(): int;
	function getEndImportId(): int|null;
	function toArray(): array;
}