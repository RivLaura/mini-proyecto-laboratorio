<?php

spl_autoload_register(function (string $class): void {
    static $classMap = null;

    if ($classMap === null) {
        $classMap = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                __DIR__,
                FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $className = $file->getBasename('.php');

            if (isset($classMap[$className])) {
                throw new RuntimeException(
                    "Nombre de archivo PHP duplicado: {$className}"
                );
            }

            $classMap[$className] = $file->getPathname();
        }
    }

    if (isset($classMap[$class])) {
        require_once $classMap[$class];
    }
});