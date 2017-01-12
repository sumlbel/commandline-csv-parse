<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as ProductAssert;

/**
 * Product
 *
 * @ORM\Table(name="tblProductData")
 * @ORM\Entity
 * @UniqueEntity("productCode")
 * @ProductAssert\PriceStockLess(minPrice = 5, minStock = 10)
 */
class Product
{
    /**
     * Automatically generated id
     *
     * @var integer
     *
     * @ORM\Column (name="intProductDataId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productDataId;

    /**
     * Product name
     *
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     *
     * @Assert\Length(max = 50)
     */
    private $productName;

    /**
     * Product description
     *
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     *
     * @Assert\Length(max = 255)
     */
    private $productDesc;

    /**
     * Product code
     *
     * @var string
     *
     * @ORM\Column(name="strProductCode",
     *     type="string", length=10, nullable=false, unique=true)
     *
     * @Assert\Length(max = 10)
     */
    private $productCode;

    /**
     * Date and time of the addition of the product
     *
     * @var \DateTime
     *
     * @ORM\Column(name="dtmAdded", type="datetime",
     *     nullable=true, options={"default": 0})
     */
    private $added;

    /**
     * The date and time when the product was discontinued
     *
     * @var \DateTime
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private $discontinued;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stmTimeStamp", type="datetime", nullable=false)
     */
    private $timeStamp;

    /**
     * Date and time of the last changes of the product
     *
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer",
     *     options={"unsigned"=true, "default"=0})
     */
    private $stock;

    /**
     * Product price
     *
     * @var float
     *
     * @ORM\Column(name="price", type="decimal", precision=6, scale=2)
     *
     * @Assert\LessThanOrEqual(1000)
     */
    private $price;

    /**
     * Product constructor
     */
    public function __construct()
    {
        $this->added = new \DateTime;
        $this->timeStamp = new \DateTime;
    }

    /**
     * Get intProductDataId
     *
     * @return integer
     */
    public function getProductDataId()
    {
        return $this->productDataId;
    }

    /**
     * Set strProductName
     *
     * @param string $productName Product name
     *
     * @return Product
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get strProductName
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set strProductDesc
     *
     * @param string $productDesc Product description
     *
     * @return Product
     */
    public function setProductDesc($productDesc)
    {
        $this->productDesc = $productDesc;

        return $this;
    }

    /**
     * Get strProductDesc
     *
     * @return string
     */
    public function getProductDesc()
    {
        return $this->productDesc;
    }

    /**
     * Set strProductCode
     *
     * @param string $productCode Product code
     *
     * @return Product
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * Get strProductCode
     *
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * Set dtmAdded
     *
     * @param \DateTime $added \DateTime of the product addition
     *
     * @return Product
     */
    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
    }

    /**
     * Get dtmAdded
     *
     * @return \DateTime
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set dtmDiscontinued
     *
     * @param \DateTime $discontinued \DateTime of product became discontinued
     *
     * @return Product
     */
    public function setDiscontinued($discontinued)
    {
        $this->discontinued = $discontinued;

        return $this;
    }

    /**
     * Get dtmDiscontinued
     *
     * @return \DateTime
     */
    public function getDiscontinued()
    {
        return $this->discontinued;
    }

    /**
     * Set stmTimeStamp
     *
     * @param \DateTime $timeStamp \DateTime of last changes
     *
     * @return Product
     */
    public function setTimeStamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }

    /**
     * Get stmTimeStamp
     *
     * @return \DateTime
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    /**
     * Get stock
     *
     * @return int
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set stock
     *
     * @param int $stock Number of products in stock
     *
     * @return $this
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
        return $this;
    }

    /**
     * Get price in GBP
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set price in GBP
     *
     * @param float $price Price value
     *
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }


}
