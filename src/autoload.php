<?php

spl_autoload_register(function (string $class): void {
    $srcPath = __DIR__;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcPath)
    );

    foreach ($iterator as $file) {
        if (
            $file->isFile() &&
            $file->getExtension() === 'php' &&
            $file->getBasename('.php') === $class
        ) {
            require_once $file->getPathname();
            return;
        }
    }
});