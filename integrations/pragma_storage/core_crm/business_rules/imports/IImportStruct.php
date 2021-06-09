<?php


namespace PragmaStorage;


interface IImportStruct {
	function getProvider(): string;
	function getStoreId() : int;
	function getImportId(): int;
	function getTimeCreate(): int;
	function getImportTime(): int;
	function getSource(): int;
	function isDeficit(): bool;
	function getNumber(): int;
	function isDeleted(): bool;
	function toArray(): array;
}