<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Layout;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

use AnyContent\CMCK\Modules\Backend\Core\Repositories\RepositoryManager;

use Symfony\Component\Filesystem\Filesystem;

class DumpRevisionCommand extends \AnyContent\CMCK\Modules\Backend\Core\Application\Command
{

    protected function configure()
    {
        $this->setName('cmck:revdump')
             ->setDescription('Dumps current git revision to revision.txt within config filer.');

    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        exec('git log -1', $line);
        $line = str_replace('commit ', '', $line[0]);

        $revision = substr($line, 0, 8);

        $filesystem = new Filesystem();
        $baseDir    = realpath(__DIR__ . '/../../../../../../../');

        $filesystem->dumpFile($baseDir . '/config/revision.txt', $revision);

        $output->writeln($revision);
    }
}

