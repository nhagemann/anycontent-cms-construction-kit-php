<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\Exchange;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AnyContent\CMCK\Modules\Backend\Edit\Exchange\Exporter;
use AnyContent\CMCK\Modules\Backend\Core\Repositories\RepositoryManager;

use Symfony\Component\Filesystem\Filesystem;

class ImportCommand extends \AnyContent\CMCK\Modules\Backend\Core\Application\Command
{

    protected function configure()
    {
        $this->setName('cmck:import')
             ->setDescription('Import records of a content type.')
             ->addArgument('repository', InputArgument::REQUIRED, 'Url of the repository having the content type to be imported, e.g. "https://www.anycontent.org". Use the list command to show available repositories.')
             ->addArgument('content type', InputArgument::REQUIRED, 'Name of the content type to be imported.')
             ->addArgument('filename', InputArgument::REQUIRED, 'Name of the file to be imported.')
             ->addOption('json', 'j')
             ->addOption('xlsx', 'x');

    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');


        $app = $this->getSilexApplication();
        /** @var RepositoryManager $repositoryManager */
        $repositoryManager = $app['repos'];

        $contentTypeName = $input->getArgument('content type');
        $repositoryUrl   = $input->getArgument('repository');
        $filename        = $input->getArgument('filename');

        $workspace = 'default';
        $language  = 'default';

        $output->writeln('Starting import for content type ' . $contentTypeName);

        $output->writeln('');

        $exporter = new Exporter();

        $repositories = $repositoryManager->listRepositories();

        $repository = false;
        foreach ($repositories as $url => $repositoryInfo)
        {
            if ($url == $repositoryUrl)
            {
                $repository = $repositoryManager->getRepositoryByRepositoryAccessHash($repositoryInfo['accessHash']);

                if (!$repository)
                {
                    $output->writeln(self::escapeError . 'Could not connect to ' . $repositoryUrl . '.' . self::escapeReset);

                    return;
                }
                break;
            }
        }

        if (!$repository)
        {
            $output->writeln(self::escapeError . 'Repository ' . $repositoryUrl . ' unknown. Use the list command to show available repositories.' . self::escapeReset);

            return;
        }

        if (!$repository->hasContentType($contentTypeName))
        {
            $output->writeln(self::escapeError . 'Repository ' . $repositoryUrl . ' does not have a content type named ' . $contentTypeName . '. Use the list command to show available content types.' . self::escapeReset);

            return;
        }


        if (strpos($filename,'/')!==0)
        {
            $filename = getcwd() . '/'.$filename;
        }
        $filename = realpath($filename);

        if (!file_exists($filename))
        {
            $output->writeln(self::escapeError . 'Could not find/access file.'. self::escapeReset);

            return;
        }

        $output->writeln('Reading '. $filename);
        $output->writeln('');




        if ($input->getOption('xlsx') == true)
        {
            $exporter = new Exporter();
            $exporter->setOutput($output);
            $exporter->importXLSX($repository, $contentTypeName, $filename, $workspace, $language);
        }
        else // default (JSON)
        {
            $data =file_get_contents($filename);
            $exporter = new Exporter();
            $exporter->setOutput($output);
            $exporter->importJSON($repository, $contentTypeName, $data, $workspace, $language);
        }

    }

}
