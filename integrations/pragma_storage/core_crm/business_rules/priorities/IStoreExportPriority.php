<?php


namespace PragmaStorage;


interface IStoreExportPriority {
	function getExportId(): int;
	function getStoreId(): int;
	function getSort(): int;
	function toArray(): array;
}