<?php

namespace AppBundle\Command;

use AppBundle\Writer\ProductWriter;
use Ddeboer\DataImport\Reader;
use Ddeboer\DataImport\Step\ConverterStep;
use Ddeboer\DataImport\Step\MappingStep;
use Ddeboer\DataImport\Workflow\StepAggregator;
use Doctrine\ORM\EntityManager;
use Exception;
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

        $reader = $csvValidator->validate($input->getArgument('file_path'));
        
        if ($csvValidator->isValid()) {
            $isTest = $input->getOption('test');
            if ($isTest) {
                $output->writeln(
                    'Running in test mode. No changes will be made in database.'
                );
            }

            $this->runWorkflow($output, $reader, $isTest);
        } else {
            $output->writeln($csvValidator->getMessage());
        }
    }

    /**
     * Set mapping
     *
     * @param array $headers
     *
     * @return MappingStep
     */
    protected function setMapping($headers): MappingStep
    {
        $mapping = new MappingStep();
        $mapping->map('['.$headers['code'].']', '[productCode]');
        $mapping->map('['.$headers['name'].']', '[productName]');
        $mapping->map('['.$headers['description'].']', '[productDesc]');
        $mapping->map('['.$headers['stock'].']', '[stock]');
        $mapping->map('['.$headers['price'].']', '[price]');
        $mapping->map('['.$headers['discontinued'].']', '[discontinued]');

        return $mapping;
    }

    /**
     * Set converter
     *
     * @return ConverterStep
     */
    protected function setConverterStep(): ConverterStep
    {
        $converterStep = new ConverterStep();
        $converterStep->add(
            function ($input) {
                $dateTime = new \DateTime();
                $input['stock'] = intval($input['stock']);
                $input['price'] = floatval($input['price']);
                $input['discontinued'] =
                    ($input['discontinued'] === 'yes') ? $dateTime : null;

                return $input;
            }
        );

        return $converterStep;
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

    /**
     * Run the writer workflow
     *
     * @param OutputInterface $output
     * @param Reader          $reader
     * @param bool            $isTest Is running in test mode
     *
     * @throws Exception
     *
     * @return void
     */
    protected function runWorkflow(OutputInterface $output, $reader, $isTest)
    {
        $headers = $this->getContainer()->getParameter('product.headers');

        $workflow = new StepAggregator($reader);
        $mapping = $this->setMapping($headers);
        $converterStep = $this->setConverterStep();

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $em->getConnection()->beginTransaction();
        try {
            $writer = $this->setWriter($em, $isTest);
            $result = $workflow
                ->addStep($mapping)
                ->addStep($converterStep)
                ->addWriter($writer)
                ->process();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }

        $logger = $this->getContainer()->get('app.logger');
        $logger->logWork(
            $output,
            $result->getSuccessCount(),
            $reader->getErrors(),
            $writer->getErrors()
        );
    }
}
