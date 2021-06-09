<?php


namespace Services;


use Services\Amocrm\iAmoEntityParams;
use Services\General\iDocTemplateService;

require_once __DIR__ . '/../../../business_rules/general/others/templater/iDocTemplateService.php';
require_once __DIR__ . '/../../../business_rules/amocrm/others/templater/iAmoEntityParams.php';

class DocTemplateService extends Service implements iDocTemplateService {
	private static string $route = '/amocrm/docx/from.entities';
	private static self $inst;

	static function getSelf(): DocTemplateService {
		if(isset(self::$inst))
			return self::$inst;
		self::$inst = new self();
		return self::$inst;
	}


	function amoCreateFromEntities(string $template_link, iAmoEntityParams $params): mixed {
		$query = self::createQuery($template_link, $params);
		return self::modulesRequest(self::$route, $query);
	}

	private static function createQuery(mixed $template, iAmoEntityParams $params): array {
		$model = self::paramsToArray($params);
		$data = self::createData($template, $model);
		return self::createQueryFromData($data);
	}

	private static function paramsToArray(iAmoEntityParams $params): array {
		return [
			'managers' => $params->getManagers(),
			'entities' => $params->getEntities(),
			'customFields' => $params->getCustomFields(),
		];
	}

	private static function createData(mixed $template, array $paramsModel): array {
		return ['template' => $template, 'params' => $paramsModel];
	}
}