<?php

namespace AppBundle\Service;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ProductsFromCsv
{
    protected $csvParser;
    protected $alterEntities;

    /**
     * CsvParser constructor.
     *
     * @param CsvParser     $csvParser
     * @param AlterEntities $alterEntities
     */
    public function __construct(CsvParser $csvParser ,AlterEntities $alterEntities)
    {
        $this->csvParser = $csvParser;
        $this->alterEntities = $alterEntities;
    }

    /**
     * Runs script, which parsing csv file
     * and making changes to database(not in test mode)
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $file = new \SplFileObject($input->getArgument('file_path'));

        $products = $this->csvParser->splitProducts($file);

        if (!$input->getOption('test')) {
            $this->alterEntities->flushChanges($products['correct']);
        } else {
            $output->writeln(
                'Running in test mode. No changes will be made in database.'
            );
        }

        $this->logWork($output, $products);
    }


    /**
     * Logging work to Output Interface
     *
     * @param OutputInterface $output
     * @param array           $products
     *
     * @return void
     */
    protected function logWork(OutputInterface $output, $products)
    {
        $output->writeln(
            '<info>'.
            $products['countProcessed'].
            ' product(s) have been processed</info>'
        );
        $output->writeln(
            '<info>'.
            count($products['correct']).
            ' product(s) have been correctly added</info>'
        );
        $output->writeln(
            '<comment>'.
            count($products['skipping']).
            ' product(s) have been skippingProducts: </comment>'
        );
        foreach ($products['skipping'] as $productData) {
            $output->writeln('<comment>'.implode(' ', $productData).'</comment>');
        }
    }
}