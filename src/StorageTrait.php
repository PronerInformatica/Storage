<?php

namespace Proner\Storage;

trait StorageTrait
{
    public function directorySeparator($path)
    {
        $pathArray = explode('/', $path);
        $newPath = implode(DS, $pathArray);
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

    public static function directorySeparatorStatic($path)
    {
        $pathArray = explode('/', $path);
        $newPath = implode(DS, $pathArray);
        return $newPath;
    }
}
