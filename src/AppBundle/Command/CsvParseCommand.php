<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

class CsvParseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('demo:parseCSV')
            ->addOption(
                'test',
                null,
                InputOption::VALUE_NONE,
                'Perform everything, but not insert the data into the database'
            )
            ->addArgument(
                'file_path',
                InputArgument::REQUIRED,
                'path to correct *.csv file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $productsFromCsv = $this->getContainer()->get('app.products_from_csv');
        $productsFromCsv->run($input, $output);
    }
}
