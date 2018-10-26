<?php

namespace Proner\Storage;

trait StorageTrait
{
    public function directorySeparator($path)
    {
        $pathArray = explode('/', $path);
        $newPath = implode(DIRECTORY_SEPARATOR, $pathArray);
        return $newPath;
    }

    public static function directorySeparatorStatic($path)
    {
        $pathArray = explode('/', $path);
        $newPath = implode(DIRECTORY_SEPARATOR, $pathArray);
        return $newPath;
    }
}
