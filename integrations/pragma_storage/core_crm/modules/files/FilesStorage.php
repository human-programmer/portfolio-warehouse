<?php


namespace PragmaStorage;


class FilesStorage
{

    static private $dir = __DIR__ . '/../../../../../../';

    static function deleteFiles(array $file_names): void
    {
        foreach ($file_names as $file_name)
            self::deleteFile($file_name);
    }

    static function deleteFile(string $file_name): void
    {
        unlink($file_name);
    }

    static function saveProductsFile(int $product_id, string $file_name, string $tmp_name): array
    {
        $dir_name = self::createDirName($product_id);
        self::saveFile($dir_name, $file_name, $tmp_name);
        $unique_file_name = self::rename($dir_name, $file_name);
        return [
            'file_name' => $unique_file_name,
            'file_path' => self::pathGenerator($product_id),
            'file_type' => self::DefineType($unique_file_name)
        ];
    }

    static private function createDirName(int $product_id): string
    {
        $dir = self::$dir . self::pathGenerator($product_id);
        self::createDir($dir);
        return $dir;
    }

    static private function pathGenerator(int $product_id): string
    {
        return 'account/' . Factory::getPragmaAccountId() . "/" . Factory::getNode()->getModule()->getCode() . "/products/$product_id/";
    }

    static private function getUniqueFileName(string $type): string
    {
        return microtime() . ".$type";
    }

    static private function parseTypeFile(string $file_name): string
    {
        $arr = explode('.', $file_name);
        return $arr[count($arr) - 1];
    }

    static private function saveFile(string $dir_name, string $file_name, string $tmp_name): void
    {
        move_uploaded_file($tmp_name, $dir_name . $file_name);
        self::compress($dir_name . $file_name);
    }

    static private function rename(string $dir_name, string $file_name): string
    {
        $type = self::parseTypeFile($file_name);
        $unique_file_name = self::getUniqueFileName($type);
        $unique_file_name = str_replace(" ", "", $unique_file_name);
        $full_name = $dir_name . $unique_file_name;
        rename($dir_name . $file_name, $full_name);
        return $unique_file_name;
    }

    static private function compress($path)
    {
        $image = new \Imagick($path);
        $image->adaptiveResizeImage(640, 384);
        file_put_contents($path, $image);
    }

    static private function createDir(string $dir_name): void
    {
        self::issetDir($dir_name) || self::create($dir_name);
    }

    static private function issetDir(string $dir_name)
    {
        return is_dir($dir_name);
    }

    static private function create(string $dir_name): void
    {
        $info = php_uname();
        $_dir_name = $dir_name;
        if (preg_match('/Windows/i', $info))
            $_dir_name = str_replace('/', "\\", $dir_name);
        mkdir($_dir_name, 0777, true);

    }


    static private function DefineType(string $file_name): string
    {
        $path_info = pathinfo($file_name);
        return $path_info['extension'];
    }
}