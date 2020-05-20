<?php
declare(strict_types = 1);
namespace Proner\Storage;

class Tools
{
    public static function containsFile(string $path)
    {
        $info = pathinfo($path);
        if (isset($info['extension'])) {
            return true;
        }
        return false;
    }

    public static function getExtensionByName(string $file)
    {
        $file = basename($file);
        $rev = strrev($file);
        $exp = explode('.', $rev);
        $ext = strrev($exp[0]);
        return $ext;
    }

    public static function directorySeparator(string $path = null)
    {
        if ($path === null) {
            return null;
        }
        $pathArray = explode('/', $path);
        $newPath = implode(PS_DS, $pathArray);
        return $newPath;
    }
}
