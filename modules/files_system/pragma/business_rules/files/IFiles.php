<?php


namespace FilesSystem\Pragma;


interface IFiles {
	function createDir(string $title, int $parent_id = null): IFile;
	function getDirContent(int $dir_id): array;
	function getDirContentModels(int $dir_id): array;
	function createFromRequest(IFileStruct $file, string $file_location): IFile;
	function createFromContent(IFileStruct $file, mixed $content): IFile;
	function delete(int|array $id): void;
	static function sendFile(int $file_id, string $token): void;
	function getFile(int $id): IFile;
}