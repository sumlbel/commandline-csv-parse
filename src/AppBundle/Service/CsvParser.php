<?php

namespace AppBundle\Service;

use AppBundle\Additional\ParsedProducts;
use AppBundle\Entity\Product;
use Ddeboer\DataImport\Reader\CsvReader;

/**
 * Class CsvParser
 *
 * @package AppBundle\Service
 */
class CsvParser
{
    private $headers;

    /**
     * CsvParser constructor.
     *
     * @param array $headers array of headers
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Divide data lines from reader into correct products,
     * skipping because of errors or according to business logic
     * and counts processed items
     *
     * @param CsvReader $reader CsvReader object
     *
     * @return ParsedProducts
     */
    public function parse($reader): ParsedProducts
    {
        $products = new ParsedProducts();

        foreach ($reader as $line) {
            if ($this->isAcceptable($line)) {
                $product = $this->setNewProduct($line);
                $products->addCorrectProduct($product);
                $products->increaseCount(1);
            } else {
                $products->addSkippingProduct($line);
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

    /**
     * Set new product from product data array
     *
     * @param array $productData Array with product data
     *
     * @return Product
     */
    public function setNewProduct($productData)
    {
        foreach ($productData as $str) {
            $str = mb_convert_encoding($str, 'UTF-8');
        }
        $product = new Product();
        $product->setProductCode($productData[$this->headers['code']]);
        $product->setProductName($productData[$this->headers['name']]);
        $product->setProductDesc($productData[$this->headers['description']]);
        $product->setStock(intval($productData[$this->headers['stock']]));
        $product->setPrice(floatval($productData[$this->headers['price']]));
        $dateTime = new \DateTime();
        if ($productData[$this->headers['discontinued']] === 'yes') {
            $product->setDiscontinued($dateTime);
        }
        return $product;
    }

    /**
     * @param array $line
     */
    public function isAcceptable($line): bool
    {
        $productCost = floatval($line[$this->headers['price']]);
        if ($productCost < 1000) {
            if ($productCost > 5 || intval($this->headers['stock']) > 10) {
                return true;
            }
        }
        return false;
    }
}
