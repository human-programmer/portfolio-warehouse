<?php


namespace PragmaStorage;

use Imagick;

require_once __DIR__ . '/../../../CONSTANTS.php';

class FilesSchema extends PragmaStoreDB
{
    private int $pragma_account_id;

    protected function __construct(int $pragma_account_id)
    {
        parent::__construct();
        $this->pragma_account_id = $pragma_account_id;
    }


    protected function deleteFiles(int $product_id)
    {
        $files = $this->getStorageFilesSchema();
        $products_links = $this->getStorageFilesToProductSchema();

        $sql = "DELETE
					$files
				FROM $files
					INNER OUTER JOIN $products_links ON $products_links.file_id = $files.id
				WHERE $files.`account_id` = $this->pragma_account_id AND $products_links.product_id = $product_id";
        return self::query($sql);
    }

//

    protected function getProductsFilesNames(int $product_id): array
    {
        return [];
    }


    function createFiles(int $product_id, string $newFileName, string $type, string $customPath): void
    {
        $Path = $customPath . $newFileName;
        $file_id = $this->FilesSQL($Path, $type);
        $this->FileToProductSQL($product_id, $file_id);
    }


    function FilesSQL(string $customPath, string $type): int
    {
        $products_schema = $this->getStorageFilesSchema();
        $pragma_account_id = $this->getPragmaAccountId();
        $_type = $type;
        $_customPath = $customPath;


        $sql = "INSERT INTO $products_schema (`account_id`, `type`, `file_name`)
                VALUES ($pragma_account_id, :type, :file_name)";
        $flag = self::execute($sql, ['type' => $_type, 'file_name' => $_customPath]);
        if (!$flag)
            throw new \Exception('Failed to create files: ' . $_customPath);


        return self::last_id();

    }


    function getLink($product_id): string
    {

        $products_Files_schema = $this->getStorageFilesSchema();
        $products_FilesToProduct_schema = $this->getStorageFilesToProductSchema();

        $pragma_account_id = $this->getPragmaAccountId();

        $sql = "SELECT
        $products_Files_schema.`file_name`
        FROM $products_Files_schema
        LEFT OUTER JOIN $products_FilesToProduct_schema
        ON $products_FilesToProduct_schema.file_id = $products_Files_schema.id
        WHERE $products_FilesToProduct_schema.product_id = $product_id
        ";
        $arr = self::query($sql);
        $l = count($arr) - 1;
        $Link = $arr[$l]['file_name'];
        return "$Link";

    }


    function FileToProductSQL(int $product_id, int $file_id)
    {
        $products_schema = $this->getStorageFilesToProductSchema();
        $sql = "INSERT INTO $products_schema (`product_id`, `file_id`)
                VALUES (:product_id, :file_id)";

        $flag = self::execute($sql, ['product_id' => $product_id, 'file_id' => $file_id]);
        if (!$flag) {
            throw new \Exception('Failed to create FileToProduct: ' . $file_id);
        }

    }

    public function getPragmaAccountId(): int
    {
        return $this->pragma_account_id;
    }

    private function pathCreate(string $file_name): string
    {
        $p = explode("/", __DIR__);
        return "https://$p[6]/$file_name";
    }


}