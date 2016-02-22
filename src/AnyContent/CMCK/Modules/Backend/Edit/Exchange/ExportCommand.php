<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\Exchange;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;


use AnyContent\CMCK\Modules\Backend\Core\Repositories\RepositoryManager;

use Symfony\Component\Filesystem\Filesystem;

class ExportCommand extends \AnyContent\CMCK\Modules\Backend\Core\Application\Command
{

    protected function configure()
    {
        $this->setName('cmck:export')
            ->setDescription('Export records of a content type.')
            ->addArgument('repository', InputArgument::REQUIRED, 'Name/Id of the repository having the content type to be exported. Use the list command to show available repositories.')
            ->addArgument('contentType', InputArgument::REQUIRED, 'Name of the content type to be exported.')
            ->addArgument('workspace', InputArgument::OPTIONAL, 'Name of the workspace to be exported [default].')
            ->addArgument('language', InputArgument::OPTIONAL, 'Name of the language to be exported [default].')
            ->addOption('json', 'j')
            ->addOption('xlsx', 'x')
            ->addOption('print', 'p');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexApplication();

        $verbose = !$input->getOption('print');

        /** @var RepositoryManager $repositoryManager */
        $repositoryManager = $app['repos'];

        $contentTypeName = $input->getArgument('contentType');
        $repositoryName = $input->getArgument('repository');

        $workspace = 'default';
        if ($input->hasArgument('workspace')) {
            $workspace = $input->getArgument('workspace');
        }

        $language = 'default';
        if ($input->hasArgument('language')) {
            $language = $input->getArgument('language');
        }

        if ($verbose) {
            $output->writeln('');
            $output->writeln('Starting export for content type ' . $contentTypeName);
            $output->writeln('');
        }


        $exporter = new Exporter();

        if($verbose) {
            $exporter->setOutput($output);
        }

        $repository = $repositoryManager->getRepositoryById($repositoryName);


        if (!$repository) {
            $output->writeln(self::escapeError . 'Repository ' . $repositoryName . ' unknown. Use the list command to show available repositories.' . self::escapeReset);

            return;
        }

        if (!$repository->hasContentType($contentTypeName)) {
            $output->writeln(self::escapeError . 'Repository ' . $repositoryName . ' does not have a content type named ' . $contentTypeName . '. Use the list command to show available content types.' . self::escapeReset);
        }

        if (!$repository->getContentTypeDefinition($contentTypeName)->hasWorkspace($workspace)) {
            $output->writeln(self::escapeError . 'Content type ' . $contentTypeName . ' does not have a workspace named ' . $workspace . self::escapeReset);
        }

        if (!$repository->getContentTypeDefinition($contentTypeName)->hasLanguage($language)) {
            $output->writeln(self::escapeError . 'Content type ' . $contentTypeName . ' does not have a language named ' . $language . self::escapeReset);
        }

        if ($input->getOption('xlsx') == true) {
            $filename = $contentTypeName . '.' . $workspace . '.' . $language . '.xlsx';
            $data = $exporter->exportXLSX($repository, $contentTypeName, $workspace, $language);
            if (!$data) {
                $output->writeln(self::escapeError . 'Could not access repository ' . $repositoryName . '.' . self::escapeReset);
                return;
            }
        } else // default (JSON)
        {
            $filename = $contentTypeName . '.' . $workspace . '.' . $language . '.json';
            $data = $exporter->exportJSON($repository, $contentTypeName, $workspace, $language);
            if (!$data) {
                $output->writeln(self::escapeError . 'Could not access repository ' . $repositoryName . '.' . self::escapeReset);
                return;
            }
        }


        $filesystem = new Filesystem();

        if ($verbose) {
            $output->writeln('');
            $output->writeln('Dumping data to ' . $filename);
            $filesystem->dumpFile($filename, $data);
            $output->writeln('');
            $output->writeln('Done');
            $output->writeln('');
        }
        else{
            $output->write($data);
        }

    }
}