<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CsvParseCommand
 *
 * @package AppBundle\Command
 */
class CsvParseCommand extends ContainerAwareCommand
{
    /**
     * Configuration of script options and arguments
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('demo:parseCSV')
            ->setHelp(
                'Script, which validate and parse *.csv file '.
                'with products information, put correct ones in database'.PHP_EOL.
                'Correct *.csv file should contain headers such: '.PHP_EOL.
                'Product Code, Product Name, Product Description, '.
                'Stock, Cost in GBP, Discontinued. '.PHP_EOL.
                'All fields should be separated by comma'
            )
            ->addOption(
                'test',
                null,
                InputOption::VALUE_NONE,
                'Perform everything, but not insert products into the database'
            )
            ->addArgument(
                'file_path',
                InputArgument::REQUIRED,
                'path to correct *.csv file'
            );
    }

    /**
     * Run script, which validating and parsing csv file
     * and making changes to database(not in test mode)
     *
     * @param InputInterface  $input  InputInterface object
     * @param OutputInterface $output OutputInterface object
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $validator = $this->getContainer()->get('app.validator');
        $parser = $this->getContainer()->get('app.csv_parser');
        $alter = $this->getContainer()->get('app.alter_entities');
        $logger = $this->getContainer()->get('app.logger');

        $reader = $validator->validate($input->getArgument('file_path'));
        if (!$validator->isValid()) {
            $output->writeln($validator->getMessage());
        } else {
            $products = $parser->parse($reader);

            if ($input->getOption('test')) {
                $output->writeln(
                    'Running in test mode. No changes will be made in database.'
                );
            } else {
                $alter->flushChanges($products->getCorrect());
            }

            $logger->logWork($output, $products);
        }
    }
}
