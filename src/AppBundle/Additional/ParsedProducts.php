<?php

namespace AppBundle\Additional;

use AppBundle\Entity\Product;

/**
 * Class ParsedProducts
 *
 * @package AppBundle\Additional
 */
class ParsedProducts
{
    /**
     * An array with correct product data, each key is product code,
     * each value is array with product data.
     *
     * @var array
     */
    protected $correct;

    /**
     * An array with incorrect product data,
     * which wouldn't be added to database,
     * each value is array with product data.
     *
     * @var array
     */
    protected $skipping;

    /**
     * Number of processed items.
     *
     * @var int
     */
    protected $countProcessed;

    /**
     * ParsedProducts constructor.
     */
    public function __construct()
    {
        $this->correct = [];
        $this->skipping = [];
        $this->countProcessed = 0;
    }

    /**
     * Return array with correct product data.
     *
     * @return array
     */
    public function getCorrect(): array
    {
        return $this->correct;
    }

    /**
     * Set the array with correct data.
     *
     * @param array $correct Array with correct data, each key is product code,
     * each value is array with product data
     *
     * @return void
     */
    public function setCorrect(array $correct)
    {
        $this->correct = $correct;
    }


    /**
     * Add one set of product data to correct array.
     *
     * @param Product $product Product object with correct data
     *
     * @return void
     */
    public function addCorrectProduct(Product $product)
    {
        $this->correct[$product->getProductCode()] = $product;
    }

    /**
     * Return array of product data, what we are skipping.
     *
     * @return array
     */
    public function getSkipping(): array
    {
        return $this->skipping;
    }

    /**
     * Set an skipping data array
     *
     * @param array $skipping Array of skipping data
     *
     * @return void
     */
    public function setSkipping(array $skipping)
    {
        $this->skipping = $skipping;
    }

    /**
     * Add one set of product data to skipping array.
     *
     * @param array $productData An array with all founded data for skipping product
     *
     * @return void
     */
    public function addSkippingProduct(array $productData)
    {
        $this->skipping[] = $productData;
    }

    /**
     * Return number of processed items
     *
     * @return int
     */
    public function getCountProcessed(): int
    {
        return $this->countProcessed;
    }

    /**
     * Set number of processed items
     *
     * @param int $countProcessed Value to set
     *
     * @return void
     */
    public function setCountProcessed(int $countProcessed)
    {
        $this->countProcessed = $countProcessed;
    }

    /**
     * Increase number of processed elements
     *
     * @param int $value Value to add
     *
     * @return void
     */
    public function increaseCount(int $value)
    {
        $this->countProcessed += $value;
    }
}
