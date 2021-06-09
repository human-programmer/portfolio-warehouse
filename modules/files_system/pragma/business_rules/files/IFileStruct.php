<?php


namespace FilesSystem\Pragma;


interface IFileStruct {
	function getFileId(): int;
	function getExtension(): string;
	function getTitle(): string;
	function getName(): string;
	function getSize(): int;
	function getType(): int;
	function getParentId(): int|null;
	function isDir(): bool;
}