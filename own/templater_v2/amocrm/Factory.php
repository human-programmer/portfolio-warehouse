<?php


namespace TemplateEngine\Amocrm;


use TemplateEngine\Pragma\ITemplateEngine;

require_once __DIR__ . '/../pragma/Factory.php';
require_once __DIR__ . '/components/AmoEntityParams.php';
require_once __DIR__ . '/components/AmocrmTemplateEngine.php';

class Factory extends \TemplateEngine\Pragma\Factory {
	static function amocrmInitFromRequest (string $script_name): void {
		$referer = self::findReferer();
		$code = self::findModuleCode();
		$logger = new \LogJSON($referer ?? 'default-referer', $code ?? 'default-referer', $script_name);
		$logger->set_container('');
		$logger->add('$_SERVER', $_SERVER);
		if(!$code || !$referer)
			throw new \Exception("Module code or account referer not found: '$code', '$referer'");
		\Services\Factory::init($code, $referer, $logger);
		$node = \Services\Factory::getNodesService()->findAmocrmReferer($code, $referer);
		if(!$node)
			throw new \Exception("Node not found: '$code', '$referer'");
		self::init($node, $logger);
	}

	private static function findReferer(): string|null {
		return parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) ?? null;
	}

	private static function findModuleCode(): string|null {
		return $_REQUEST['code'] ?? null;
	}

	static function getTemplateEngine(): ITemplateEngine {
		return new AmocrmTemplateEngine();
	}
}