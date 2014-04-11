<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\Exchange;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \AnyContent\CMCK\Modules\Backend\Core\Application\Command
{

    protected function configure()
    {
        $this->setName('cmck:export')
             ->setDescription('Export records of a content type')
             ->addArgument('content type', InputArgument::REQUIRED, 'Name of the content type to be exported')
             ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'xls (default) or json');

    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting export for content type '.$input->getArgument('content type').'.');
    }
}