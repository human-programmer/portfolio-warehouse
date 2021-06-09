<?php


namespace FilesSystem\Pragma;


require_once __DIR__ . '/RelationContainsSchema.php';

trait FindingPath {
	static function updateParents(IFileStruct $file): void {
		$parents_list = self::getExpectedParentsList($file->getFileId());
		RelationContainsSchema::saveParentRelations($file->getFileId(), $parents_list);
	}

	static function getExpectedParentsList(int|null $file_id, array &$parents = []): array {
		if(!$file_id) return $parents;
		$parent_id = FilesSchema::getParentId($file_id);
		if($parent_id) {
			isset($parents[$parent_id]) && throw new \Exception("Recreational file path");
			$parents[$parent_id] = $parent_id;
		}
		return self::getExpectedParentsList($parent_id, $parents);
	}

	static function findFileDirFromIndexedFiles(int $file_id, array &$dirs = []): string {
		if(!$file_id) return implode('/', array_reverse($dirs));
		$parent_id = FilesSchema::getParentId($file_id);
		if($parent_id) {
			isset($dirs[$parent_id]) && throw new \Exception("Recreational file path");
			$dirs[$parent_id] = $parent_id;
		}
		return self::findFileDirFromIndexedFiles($parent_id, $dirs);
	}

	static function findFileDirFromRelations(int $file_id): string {
		$links = RelationContainsSchema::getFileParents($file_id);
		$convertedLinks = self::convertLinks($links);
		return self::assemblePath($convertedLinks, $file_id);
	}

	static function convertLinks(array $links): array {
		foreach ($links as $link)
			$res[$link['child_id']] = $link['parent_id'];
		return $res ?? [];
	}

	static function assemblePath(array $convertedLinks, int $targetFileId, array &$dirs = []): string {
		if(!$convertedLinks[$targetFileId]) return implode('/', array_reverse($dirs));
		$parent_id = $convertedLinks[$targetFileId];
		isset($dirs[$parent_id]) && throw new \Exception("Recreational file path");
		$dirs[$parent_id] = $parent_id;
		return self::assemblePath($convertedLinks, $parent_id, $dirs);
	}
}