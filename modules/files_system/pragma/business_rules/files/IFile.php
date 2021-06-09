<?php


namespace FilesSystem\Pragma;

require_once __DIR__ . '/IFileStruct.php';

interface IFile extends IFileStruct {
	function getAlias(): string;
	function getUniqueName(): string;
	function getExternalLink(): string;
	function getFullUniqueName(): string;
	function getSystemPath(): string;
	function getContent(): mixed;
	function getExternalModel(): array;
	function getToken(): string;
	function getAccountId(): int;
	function getModuleId(): int;
}