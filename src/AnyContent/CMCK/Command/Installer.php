<?php

namespace AnyContent\CMCK\Command;

use Symfony\Component\Filesystem\Filesystem;

class Installer
{

    public static function postInstallUpdate()
    {

        $filesystem = new Filesystem();
        $baseDir    = realpath(__DIR__ . '/../../../../../../../');
        $packageDir = realpath(__DIR__ . '/../../../../');

        echo "...\n";

        echo "Creating Web Folder\n";

        $filesystem->mkdir($baseDir . '/web');
        $filesystem->mirror($packageDir . '/web', $baseDir . '/web');

        echo "Creating Config Folder with example config.\n";

        $filesystem->mkdir($baseDir . '/config');
        $filesystem->mirror($packageDir . '/config', $baseDir . '/config');

        echo "Creating TWIG cache folder, deleting current cache files.\n";
        $filesystem->remove($baseDir . '/twig-cache');
        $filesystem->mkdir($baseDir . '/twig-cache');

        echo "...\n";
        echo "Done.\n";
    }
}
