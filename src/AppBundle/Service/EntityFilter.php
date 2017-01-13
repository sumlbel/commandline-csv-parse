<?php

namespace AppBundle\Service;

use AppBundle\Additional\ParsedProducts;
use AppBundle\Entity\Product;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Doctrine\ORM\EntityManager;

/**
 * Class EntityFilter
 *
 * @package AppBundle\Service
 */
class EntityFilter
{

    private $headers;
    private $validator;

    /**
     * EntityFilter constructor.
     *
     * @param array $headers array of headers
     */
    public function __construct(array $headers, $validator)
    {
        $this->headers = $headers;
        $this->validator = $validator;
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
    public function filter($result, $csvErrors): ParsedProducts
    {
        $products = new ParsedProducts();

        foreach ($result as $line) {
            $product = $this->setNewProduct($line);
            $errors = $this->validator->validate($product);
            if (!$errors->has(0)) {
                $products->addCorrectProduct($product);
                $products->increaseCount(1);
            } else {
                $products->addSkippingLine($line);
            }
        }
        $products->setSkipping(
            array_merge(
                $csvErrors,
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
        $product->setProductCode($productData['productCode']);
        $product->setProductName($productData['productName']);
        $product->setProductDesc($productData['productDesc']);
        $product->setStock($productData['stock']);
        $product->setPrice($productData['price']);
        $product->setDiscontinued($productData['discontinued']);
        return $product;
    }
}
