<?php


namespace TemplateEngine\Amocrm;


use FilesSystem\Pragma\IFile;
use Services\Amocrm\iAmoEntityParams;

require_once __DIR__ . '/../../pragma/components/TemplateEngine.php';

class AmocrmTemplateEngine extends \TemplateEngine\Pragma\TemplateEngine {
	protected function createContent(IFile $file, iAmoEntityParams $params): mixed {
		return Factory::getDocxService()->amoCreateFromEntities($file->getExternalLink(), $params);
	}
}