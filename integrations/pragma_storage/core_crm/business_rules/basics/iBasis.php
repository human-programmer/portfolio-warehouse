<?php


namespace PragmaStorage;


interface iBasis {
	function isDeleted(): bool;
	function delete(): bool;
	function recover();
	function toArray(): array;
	function update(array $model): bool;
}