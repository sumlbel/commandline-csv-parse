<?php

namespace AppBundle\Command;


use AppBundle\Service\CsvParseLogger;
use AppBundle\Workflow\ProductWorkflow;
use AppBundle\Writer\ProductWriter;
use Doctrine\ORM\EntityManager;
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
        $csvValidator = $this->getContainer()->get('app.csv_validator');
        $headers = $this->getContainer()->getParameter('product.headers');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $reader = $csvValidator->validate($input->getArgument('file_path'));
        
        if ($csvValidator->isValid()) {
            $isTest = $input->getOption('test');
            if ($isTest) {
                $output->writeln(
                    'Running in test mode. No changes will be made in database.'
                );
            }

            $workflow = new ProductWorkflow($em, $reader, $headers);
            $writer = $this->setWriter($em, $isTest);
            $result = $workflow->runWorkflow($output, $writer);

            $logger = new CsvParseLogger($output);
            $logger->logWork(
                $result->getSuccessCount(),
                $reader->getErrors(),
                $writer->getErrors()
            );
        } else {
            $output->writeln($csvValidator->getMessage());
        }
    }

    /**
     * Set writer
     *
     * @param EntityManager $em
     * @param bool          $isTest
     *
     * @return ProductWriter
     */
    protected function setWriter($em, $isTest): ProductWriter
    {
        $writer = new ProductWriter($em, 'AppBundle:Product');
        $validator = $this->getContainer()->get('validator');
        $writer->setParameters($validator, $isTest);

        return $writer;
    }
}
