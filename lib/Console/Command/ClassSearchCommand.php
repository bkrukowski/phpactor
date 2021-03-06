<?php

namespace Phpactor\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Phpactor\Application\ClassSearch;
use Phpactor\Console\Dumper\DumperRegistry;
use Phpactor\Container\SourceCodeFilesystemExtension;

class ClassSearchCommand extends Command
{
    /**
     * @var ClassSearch
     */
    private $search;

    /**
     * @var DumperRegistry
     */
    private $dumperRegistry;

    public function __construct(
        ClassSearch $search,
        DumperRegistry $dumperRegistry
    ) {
        parent::__construct();
        $this->search = $search;
        $this->dumperRegistry = $dumperRegistry;
    }

    public function configure()
    {
        $this->setName('class:search');
        $this->setDescription('Search for class by (short) name and return informations on candidates');
        $this->addArgument('name', InputArgument::REQUIRED, 'Source path or FQN');
        Handler\FormatHandler::configure($this);
        Handler\FilesystemHandler::configure($this, SourceCodeFilesystemExtension::FILESYSTEM_COMPOSER);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $results = $this->search->classSearch(
            $input->getOption('filesystem'),
            $input->getArgument('name')
        );

        $dumper = $this->dumperRegistry->get($input->getOption('format'));
        $dumper->dump($output, $results);
    }
}
