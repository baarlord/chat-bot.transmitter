<?php

namespace lib;

class AutoLoader
{
    protected const FILE_EXTENSION = '.php';

    /**
     * @param string $className
     */
    public static function autoload(string $className)
    {
        $classNameDir = strtolower(str_replace('\\', '/', $className));
        $filename = dirname(__DIR__) . '/' . $classNameDir . AutoLoader::FILE_EXTENSION;
        if (is_readable($filename)) {
            require_once $filename;
        }
        return;
    }
}