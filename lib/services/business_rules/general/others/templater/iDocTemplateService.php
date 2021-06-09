<?php


namespace Services\General;


use Services\Amocrm\iAmoEntityParams;

interface iDocTemplateService {
	static function getSelf(): iDocTemplateService;
	function amoCreateFromEntities(string $template_link, iAmoEntityParams $params): mixed;
}