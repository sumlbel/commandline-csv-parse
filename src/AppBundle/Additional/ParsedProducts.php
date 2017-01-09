<?php

namespace AppBundle\Additional;


/**
 * Class ParsedProducts
 * @package AppBundle\Additional
 */
class ParsedProducts
{
    protected $correct;
    protected $skipping;
    protected $countProcessed;

    public function __construct()
    {
        $this->correct = array();
        $this->skipping = array();
        $this->countProcessed = 0;
    }

    /**
     * @return array
     */
    public function getCorrect(): array
    {
        return $this->correct;
    }

    /**
     * @param array $correct
     */
    public function setCorrect(array $correct)
    {
        $this->correct = $correct;
    }


    /**
     * @param string $productCode
     * @param array  $productData
     */
    public function addCorrect(string $productCode, array $productData)
    {
        $this->correct[$productCode] = $productData;
    }

    /**
     * @return array
     */
    public function getSkipping(): array
    {
        return $this->skipping;
    }

    /**
     * @param array $skipping
     */
    public function setSkipping(array $skipping)
    {
        $this->skipping = $skipping;
    }

    /**
     * @param string $productCode
     * @param array  $productData
     */
    public function addSkipping(string $productCode, array $productData)
    {
        $this->skipping[$productCode] = $productData;
    }

    /**
     * @return int
     */
    public function getCountProcessed(): int
    {
        return $this->countProcessed;
    }

    /**
     * @param int $countProcessed
     */
    public function setCountProcessed(int $countProcessed)
    {
        $this->countProcessed = $countProcessed;
    }

    /**
     * @param int $countProcessed
     */
    public function increaseCount(int $value)
    {
        $this->countProcessed += $value;
    }
}