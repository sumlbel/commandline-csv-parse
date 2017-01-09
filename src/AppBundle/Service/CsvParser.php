<?php

namespace AppBundle\Service;

use AppBundle\Additional\ParsedProducts;
use Ddeboer\DataImport\Reader\CsvReader;

class CsvParser
{
    /**
     * Splitting products into correct, skipping and count entries
     *
     * @param CsvReader $reader
     *
     * @return array
     */
    public function parse($file): ParsedProducts
    {
        $reader = new CsvReader($file, ',');
        $reader->setHeaderRowNumber(0);

        $products = $this->splitProducts($reader);

        return $products;
    }

    /**
     * @param $reader
     * @return ParsedProducts
     */
    public function splitProducts($reader): ParsedProducts
    {
        $products = new ParsedProducts();

        foreach ($reader as $line) {
            $productCost = floatval($line['Cost in GBP']);
            $productKey = $line['Product Code'];
            if ($productCost < 1000) {
                if ($productCost > 5 || intval($line['Stock']) > 10) {
                    $products->addCorrect($productKey, $line);
                    $products->increaseCount(1);
                }
            } else {
                $products->addSkipping($productKey, $line);
            }
        }
        $products->setSkipping(
            array_merge(
                $reader->getErrors(),
                $products->getSkipping()
            )
        );
        $products->increaseCount(count($products->getSkipping()));

        return $products;
    }
}