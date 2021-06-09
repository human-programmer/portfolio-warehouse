<?php

namespace PragmaStorage;

use const Generals\INTEGRATIONS_DOMAIN;

require_once __DIR__ . '/../../business_rules/files/iFiles.php';
require_once __DIR__ . '/FilesSchema.php';
require_once __DIR__ . '/FilesStorage.php';

class Files extends FilesSchema implements iFiles
{
    function __construct(int $pragma_account_id)
    {
        parent::__construct($pragma_account_id);
    }

    function saveProductsFile(string $name_file, int $product_id, $tmp_name): string
    {

        $domain = INTEGRATIONS_DOMAIN;
        $ArrFile = FilesStorage::saveProductsFile($product_id, $name_file, $tmp_name);

        $newFileName = $ArrFile['file_name'];
        $newFilePath = $ArrFile['file_path'];
        $newFileType = $ArrFile['file_type'];
        $this->createFiles($product_id,$newFileName , $newFileType, $newFilePath);
        if (!empty($URL)) {
            return "https://$domain/" . $URL;
        }
        return "https://$domain/$newFilePath$newFileName";
    }

    function deleteProductsFile(int $file_id): void
    {
        // TODO: Implement deleteProductsFile() method.
    }

    function deleteProductsFiles(int $product_id): void
    {
        $this->deleteFile($product_id);
    }

	function getProductsFiles(int $product_id): string {
		$domain = INTEGRATIONS_DOMAIN;
		$URL = $this->getLink($product_id);
		if (!empty($URL)) {
			return "https://$domain/" . $URL;
		}
		return "https://$domain/api/integrations/pragma_storage/amocrm/Stock/temp/no.png";
	}

}