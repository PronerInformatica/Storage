<?php

namespace Proner\Storage;

trait StorageTrait
{
    public function directorySeparator($path)
    {
        $pathArray = explode('/', $path);
        $newPath = implode(PS_DS, $pathArray);
        return $newPath;
    }

    public function containsFile($path)
    {
        $info = pathinfo($path);
        if (isset($info['extension'])) {
            return true;
        }
        return false;
    }

    public function getExtensionByName($file)
    {
        $file = basename($file);
        $rev = strrev($file);
        $exp = explode('.', $rev);
        $ext = strrev($exp[0]);
        return $ext;
    }

    public static function directorySeparatorStatic($path)
    {
        $pathArray = explode('/', $path);
        $newPath = implode(PS_DS, $pathArray);
        return $newPath;
    }
}
