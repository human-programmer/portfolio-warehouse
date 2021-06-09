<?php


namespace Templater;


use Files\iFiles;
use Services\General\iDocTemplateService;
use Services\General\iNode;

require_once __DIR__ . '/CONSTANTS.php';
require_once __DIR__ . '/pragma/modules/DocLink.php';
require_once __DIR__ . '/../../lib/db/CRMDB.php';
require_once __DIR__ . '/pragma/modules/DocLinks.php';
require_once __DIR__ . '/../../lib/services/Factory.php';
require_once __DIR__ . '/../../modules/files/Factory.php';

abstract class Factory {
	abstract protected static function initNode(): void;
	private static \LogWriter $logger;
	protected static iNode $node;

	static function init(string $logPrefix): void {
		$referer = static::getClientReferer();
		self::$logger = new \LogJSON($referer, WIDGET_NAME, $logPrefix);
		self::$logger->set_container('');
		\Services\Factory::init(WIDGET_NAME, $referer, self::$logger);
	}

	protected static function getClientReferer(): string {
		return parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) ?? 'undefined_referer';
	}

	static function getLogWriter(): \LogWriter {
		return self::$logger;
	}

	static function getNode(): iNode {
		return self::$node;
	}

	static function getFilesFactory(): iFiles {
		return \Files\Factory::getFiles();
	}

	static function getDocxService(): iDocTemplateService {
		return \Services\Factory::getTemplateService();
	}
}