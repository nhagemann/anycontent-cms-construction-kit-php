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

        if (!$filesystem->exists($baseDir . '/web'))
        {
            echo "Creating Web Folder\n";
            $filesystem->mkdir($baseDir . '/web');
            $filesystem->mirror($packageDir . '/web', $baseDir . '/web');
        }

        echo "Creating Config Folder with example config.\n";

        $filesystem->mkdir($baseDir . '/config');

        $filesystem->copy($packageDir . '/config/config.example.yml', $baseDir . '/config/config.example.yml');
        $filesystem->copy($packageDir . '/config/modules.example.php', $baseDir . '/config/modules.example.php');

        $filesystem->copy($packageDir . '/config/revision.txt', $baseDir . '/config/revision.txt');

        echo "Creating cache folder, deleting eventually current cache files.\n";

        $filesystem->remove($baseDir . '/twig-cache');
        $filesystem->mkdir($baseDir . '/twig-cache');
        $filesystem->remove($baseDir . '/doctrine-cache');
        $filesystem->mkdir($baseDir . '/doctrine-cache');

        echo "...\n";
        echo "Done.\n";
    }

}
