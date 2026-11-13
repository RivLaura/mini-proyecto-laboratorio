<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    static $classMap = null;

    // La integración conserva nombres de clases sin namespaces.
    // Cada clase o interfaz debe tener su propio archivo PHP.
    if (str_contains($class, '\\')) {
        return;
    }

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

            // No es necesario incluir el propio autoloader.
            if ($file->getPathname() === __FILE__) {
                continue;
            }

            $className = $file->getBasename('.php');

            if (isset($classMap[$className])) {
                throw new RuntimeException(
                    "Archivo PHP duplicado para la clase: {$className}"
                );
            }

            $classMap[$className] = $file->getPathname();
        }
    }

    if (isset($classMap[$class])) {
        require_once $classMap[$class];
    }
});
