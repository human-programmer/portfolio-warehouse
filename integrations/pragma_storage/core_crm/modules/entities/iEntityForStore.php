<?php


namespace PragmaStorage;


interface iEntityForStore {
	function isChangedStatusOrPipeline(): bool;
	function findStatusId();
	function findPipelineId();
	function setValue(string $pragma_field_name, string $value): bool;
	function getPragmaEntityId(): int;
	function findResponsibleUserId();
	function setValueIsChanged(string $pragma_field_name): void;
}