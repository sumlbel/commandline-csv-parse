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
            $productKey = $line['Product Code'];
            if ($this->isAcceptable($line)) {
                $product = $this->setNewProduct($line);
                $products->addCorrect($productKey, $product);
                $products->increaseCount(1);
            } else {
                $products->addSkipping($line);
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
        $product->setStrproductcode($productData['Product Code']);
        $product = $this->setProductData($product, $productData);
        return $product;
    }

    /**
     * Set data of existing product
     *
     * @param Product $product     Product object
     * @param array   $productData Array with product data
     *
     * @return Product
     */
    protected function setProductData(Product $product, $productData)
    {
        $product->setStrproductname($productData['Product Name']);
        $product->setStrproductdesc($productData['Product Description']);
        $product->setStock(intval($productData['Stock']));
        $product->setPrice(floatval($productData['Cost in GBP']));
        $dateTime = new \DateTime();
        $product->setDtmadded($dateTime);
        if ($productData['Discontinued'] === 'yes') {
            $product->setDtmdiscontinued($dateTime);
        }
        return $product;
    }

    /**
     * @param array $line
     */
    public function isAcceptable($line): bool
    {
        $productCost = floatval($line['Cost in GBP']);
        $isStringsValid = (strlen($line['Product Code']) < 10) &&
            (strlen($line['Product Name']) < 50) &&
            (strlen($line['Product Description']) < 255);
        if ($productCost < 1000 && $isStringsValid) {
            if ($productCost > 5 || intval($line['Stock']) > 10) {
                return true;
            }
        } else {
            return false;
        }
    }
}
