<?php


namespace FilesSystem\Pragma;


interface IAccountVariables {
	function getNodeDir(): string;
	static function rootDirByFile(IFile $file): string;
	static function getDefaultExternalPath(): string;
}