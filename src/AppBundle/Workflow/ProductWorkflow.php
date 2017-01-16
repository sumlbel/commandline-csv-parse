<?php
/**
 * Created by PhpStorm.
 * User: s2.gerasimovich
 * Date: 1/16/17
 * Time: 12:21 PM
 */

namespace AppBundle\Workflow;


use AppBundle\Writer\ProductWriter;
use Ddeboer\DataImport\Reader;
use Ddeboer\DataImport\Result;
use Ddeboer\DataImport\Step\ConverterStep;
use Ddeboer\DataImport\Step\MappingStep;
use Ddeboer\DataImport\Workflow\StepAggregator;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\Console\Output\OutputInterface;

class ProductWorkflow extends StepAggregator
{
    protected $reader;
    protected $headers;
    protected $entityManager;

    /**
     * ProductWorkflow constructor.
     *
     * @param EntityManager $entityManager
     * @param Reader        $reader
     * @param array         $headers
     */
    public function __construct(EntityManager $entityManager, $reader, $headers)
    {
        parent::__construct($reader);
        $this->reader = $reader;
        $this->headers = $headers;
        $this->entityManager = $entityManager;
    }

    /**
     * Set mapping
     *
     * @param array $headers
     *
     * @return MappingStep
     */
    protected function setMapping(): MappingStep
    {
        $mapping = new MappingStep();
        $mapping->map('['.$this->headers['code'].']', '[productCode]');
        $mapping->map('['.$this->headers['name'].']', '[productName]');
        $mapping->map('['.$this->headers['description'].']', '[productDesc]');
        $mapping->map('['.$this->headers['stock'].']', '[stock]');
        $mapping->map('['.$this->headers['price'].']', '[price]');
        $mapping->map('['.$this->headers['discontinued'].']', '[discontinued]');

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
     * Run the writer workflow
     *
     * @param OutputInterface $logOutput
     * @param Reader          $reader
     * @param ProductWriter   $writer
     *
     * @throws Exception
     *
     * @return Result
     */
    public function runWorkflow(OutputInterface $logOutput, $writer)
    {
        $mapping = $this->setMapping();
        $converterStep = $this->setConverterStep();

        $em = $this->entityManager;
        $em->getConnection()->beginTransaction();
        try {
            $result = $this
                ->addStep($mapping)
                ->addStep($converterStep)
                ->addWriter($writer)
                ->process();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
        return $result;
    }

}
