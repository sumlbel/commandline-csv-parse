<?php

namespace AppBundle\Command;

use AppBundle\AppBundle;
use AppBundle\Service\PersistEntities;
use Ddeboer\DataImport\Filter\ValidatorFilter;
use Ddeboer\DataImport\Step\ConverterStep;
use Ddeboer\DataImport\Step\FilterStep;
use Ddeboer\DataImport\Step\MappingStep;
use Ddeboer\DataImport\ValueConverter\ArrayValueConverterMap;
use Ddeboer\DataImport\Workflow\StepAggregator;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Workflow\Workflow;

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
        $filter = $this->getContainer()->get('app.entity_filter');
        $logger = $this->getContainer()->get('app.logger');
        $headers = $this->getContainer()->getParameter('product.headers');

        $reader = $csvValidator->validate($input->getArgument('file_path'));
        $validator = $this->getContainer()->get('validator');
        $mapping = new MappingStep();
        $mapping->map('['.$headers['code'].']', '[productCode]');
        $mapping->map('['.$headers['name'].']', '[productName]');
        $mapping->map('['.$headers['description'].']', '[productDesc]');
        $mapping->map('['.$headers['stock'].']', '[stock]');
        $mapping->map('['.$headers['price'].']', '[price]');
        $mapping->map('['.$headers['discontinued'].']', '[discontinued]');

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $workflow = new StepAggregator($reader);
        $errors = $reader->getErrors();
        $converterStep = new ConverterStep();
        $converterStep->add(
            function ($input) {
                $dateTime = new \DateTime();
                $input['stock'] = intval($input['stock']);
                $input['price'] = intval($input['price']);
                $input['discontinued'] =
                    ($input['discontinued'] === 'yes')?$dateTime:null;
                return $input;
            }
        );
        $result = $workflow
            ->addStep($mapping)
            ->addStep($converterStep)
            ->process();
        
        if ($csvValidator->isValid()) {
            $em->getConnection()->beginTransaction();
            try {
                $products = $filter->filter($result, $errors);

                if ($input->getOption('test')) {
                    $output->writeln(
                        'Running in test mode. No changes will be made in database.'
                    );
                } else {
                    $writer = new PersistEntities($em, 'AppBundle:Product');
                    $writer->writeItem($products->getCorrect());
                    $writer->flush();
                }
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }

            $logger->logWork($output, $products);
        } else {
            $output->writeln($csvValidator->getMessage());
        }
    }

    static function productConverter($input)
    {
        $dateTime = new \DateTime();
        $input['stock'] = intval($input['stock']);
        $input['price'] = intval($input['price']);
        $input['discontinued'] = ($input['discontinued'] === 'yes')?$dateTime:null;
        return $input;
    }
}
