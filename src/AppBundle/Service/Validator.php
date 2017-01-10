<?php

namespace AppBundle\Service;

use Ddeboer\DataImport\Reader\CsvReader;
use SplFileInfo;

/**
 * Class Validator
 *
 * @package AppBundle\Service
 */
class Validator
{
    /**
     * Is valid or not
     *
     * @var bool
     */
    protected $isValid;

    /**
     * Error message
     *
     * @var string
     */
    protected $message;

    /**
     * Validator constructor.
     */
    public function __construct()
    {
        $this->isValid = true;
        $this->message = '';
    }

    /**
     * Split products into correct, skipping and count entries
     *
     * @param string $filePath Path to file
     *
     * @return CsvReader
     */
    public function validate($filePath)
    {
        if ($this->extensionValidation($filePath)) {
            $reader = $this->initializeReader($filePath);
            if ($this->headersValidation($reader)) {
                return $reader;
            }
        }
        return null;
    }

    /**
     * Get 'is valid' status
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Check is file have *.csv extension
     *
     * @param string $filePath Path to file
     *
     * @return bool
     */
    public function extensionValidation($filePath)
    {
        $fileInfo = new SplFileInfo($filePath);
        if ($fileInfo->getExtension() !== 'csv') {
            $this->isValid = false;
            $this->message = '<error>Incorrect file. '.
                'It should have an *.csv extension</error>';
        }
        return $this->isValid();
    }

    /**
     * Initialize the CsvReader on correct file path
     *
     * @param string $filePath Path to file
     *
     * @return CsvReader
     */
    public function initializeReader($filePath): CsvReader
    {
        $file = new \SplFileObject($filePath);
        $reader = new CsvReader($file);
        $reader->setHeaderRowNumber(0);

        return $reader;
    }

    /**
     * Set isValid to true if headers in csv correct, else false
     *
     * @param CsvReader $reader CsvReader object with headers
     *
     * @return bool
     */
    public function headersValidation($reader)
    {
        $headers = $reader->getColumnHeaders();
        $this->isValid = in_array('Product Code', $headers) &&
            in_array('Product Name', $headers) &&
            in_array('Product Description', $headers) &&
            in_array('Stock', $headers) &&
            in_array('Cost in GBP', $headers) &&
            in_array('Discontinued', $headers);
        if (!$this->isValid) {
            $this->message = '<error>Incorrect file. '.
                'It should contain headers such: '.PHP_EOL.
                'Product Code, Product Name, Product Description, '.
                'Stock, Cost in GBP, Discontinued. '.PHP_EOL.
                'All fields should be separated by comma</error>';
        }
        return $this->isValid();
    }
}