<?php

namespace AppBundle\Service;

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
    public function splitProducts($file): array
    {
        $reader = new CsvReader($file, ',');
        $reader->setHeaderRowNumber(0);

        $products = array(
            'correct' => array(),
            'skipping' => array(),
            'countProcessed' => 0
        );

        foreach ($reader as $line) {
            $productCost = floatval($line['Cost in GBP']);
            $productKey = $line['Product Code'];
            if ($productCost < 1000) {
                if ($productCost > 5 || intval($line['Stock']) > 10) {
                    $products['correct'][$productKey] = $line;
                    $products['countProcessed']++;
                }
            } else {
                $products['skipping'][$productKey] = $line;
            }
        }
        $products['skipping'] = array_merge(
            $reader->getErrors(),
            $products['skipping']
        );
        $products['countProcessed'] += count($products['skipping']);

        return $products;
    }
}