<?php


namespace Templater\Amocrm;


use Files\FileToCreate;
use Files\iFile;
use Files\iFileToCreate;
use Services\Amocrm\iAmoEntityParams;
use Templater\Pragma\ALinkedFile;
use Templater\Pragma\IDocLinkToCreate;
use Templater\Pragma\LinkedFile;

require_once __DIR__ . '/../business_rules/IAmocrmLinks.php';

class AmoDocLinks extends \Templater\Pragma\DocLinks implements IAmocrmLinks {
	function __construct(int $pragmaAccountId) {
		parent::__construct($pragmaAccountId);
	}

	function createAndLink(IDocLinkToCreate $link, iAmoEntityParams $params): ALinkedFile {
		$templateFile = static::getFile($link->getTemplateFileId());
		$fileContent = static::createDoc($templateFile->getExternalLink(), $params);
		$fileToCreate = self::getFileToCreateStruct($templateFile);
		$file = Factory::getFilesFactory()->createFromContent($fileToCreate, $fileContent);
		$link = self::saveFileLink($file->getId(), $link);
		return new LinkedFile($file, $link);
	}

	protected static function getFileToCreateStruct(iFile $file): iFileToCreate {
		$model = $file->getModel();
		unset($model['id']);
		$model['group'] = 'linked';
		return new FileToCreate($model);
	}

	protected static function getFile(int $id): iFile {
		$file = static::findFile($id);
		$file || throw new  \Exception('File not found, id "' . $id . '"');
		return $file;
	}

	protected static function findFile(int $fileId): iFile|null {
		return Factory::getFilesFactory()->getFiles([$fileId])[0] ?? null;
	}

	private static function createDoc(string $templateFileLink, iAmoEntityParams $params): mixed {
		return Factory::getDocxService()->amoCreateFromEntities($templateFileLink, $params);
	}
}