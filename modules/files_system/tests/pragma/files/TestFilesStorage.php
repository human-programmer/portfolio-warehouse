<?php


namespace FilesSystem\Pragma\Tests;


use Services\General\iNode;

class TestFilesStorage extends \FilesSystem\Pragma\Files {
	function setFileSystem(TestFileSystem $system): void {
		$this->system = $system;
	}
}