<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Product
 *
 * @ORM\Table(name="tblProductData")
 * @ORM\Entity
 * @UniqueEntity("strProductCode")
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
    private $intProductDataId;

    /**
     * Product name
     *
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     */
    private $strProductName;

    /**
     * Product description
     *
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private $strProductDesc;

    /**
     * Product code
     *
     * @var string
     *
     * @ORM\Column(name="strProductCode",
     *     type="string", length=10, nullable=false, unique=true)
     */
    private $strProductCode;

    /**
     * Date and time of the addition of the product
     *
     * @var \DateTime
     *
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private $dtmAdded;

    /**
     * The date and time when the product was discontinued
     *
     * @var \DateTime
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private $dtmDiscontinued;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stmTimeStamp", type="datetime", nullable=false)
     */
    private $stmTimeStamp;

    /**
     * Date and time of the last changes of the product
     *
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer")
     */
    private $stock;

    /**
     * Product price
     *
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;



    /**
     * Get intProductDataId
     *
     * @return integer
     */
    public function getIntProductDataId()
    {
        return $this->intProductDataId;
    }

    /**
     * Set strProductName
     *
     * @param string $strProductName Product name
     *
     * @return Product
     */
    public function setStrProductName($strProductName)
    {
        $this->strProductName = $strProductName;

        return $this;
    }

    /**
     * Get strProductName
     *
     * @return string
     */
    public function getStrProductName()
    {
        return $this->strProductName;
    }

    /**
     * Set strProductDesc
     *
     * @param string $strProductDesc Product description
     *
     * @return Product
     */
    public function setStrProductDesc($strProductDesc)
    {
        $this->strProductDesc = $strProductDesc;

        return $this;
    }

    /**
     * Get strProductDesc
     *
     * @return string
     */
    public function getStrProductDesc()
    {
        return $this->strProductDesc;
    }

    /**
     * Set strProductCode
     *
     * @param string $strProductCode Product code
     *
     * @return Product
     */
    public function setStrProductCode($strProductCode)
    {
        $this->strProductCode = $strProductCode;

        return $this;
    }

    /**
     * Get strProductCode
     *
     * @return string
     */
    public function getStrProductCode()
    {
        return $this->strProductCode;
    }

    /**
     * Set dtmAdded
     *
     * @param \DateTime $dtmAdded \DateTime of the product addition
     *
     * @return Product
     */
    public function setDtmAdded($dtmAdded)
    {
        $this->dtmAdded = $dtmAdded;

        return $this;
    }

    /**
     * Get dtmAdded
     *
     * @return \DateTime
     */
    public function getDtmAdded()
    {
        return $this->dtmAdded;
    }

    /**
     * Set dtmDiscontinued
     *
     * @param \DateTime $dtmDiscontinued \DateTime of product became discontinued
     *
     * @return Product
     */
    public function setDtmDiscontinued($dtmDiscontinued)
    {
        $this->dtmDiscontinued = $dtmDiscontinued;

        return $this;
    }

    /**
     * Get dtmDiscontinued
     *
     * @return \DateTime
     */
    public function getDtmDiscontinued()
    {
        return $this->dtmDiscontinued;
    }

    /**
     * Set stmTimeStamp
     *
     * @param \DateTime $stmTimeStamp \DateTime of last changes
     *
     * @return Product
     */
    public function setStmTimeStamp($stmTimeStamp)
    {
        $this->stmTimeStamp = $stmTimeStamp;

        return $this;
    }

    /**
     * Get stmTimeStamp
     *
     * @return \DateTime
     */
    public function getStmTimeStamp()
    {
        return $this->stmTimeStamp;
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

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->setDtmAdded(new \DateTime());
    }
}
