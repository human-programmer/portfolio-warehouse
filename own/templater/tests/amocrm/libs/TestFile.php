<?php


namespace Templater\Amocm\Tests;


use Configs\Configs;

class TestFile extends \Files\File {

	protected static function getStartDomain(): string {
		return 'http://smart-dev.core_crm';
	}
}