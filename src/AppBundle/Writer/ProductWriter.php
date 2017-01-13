<?php

namespace AppBundle\Writer;

use AppBundle\Entity\Product;
use Ddeboer\DataImport\Writer\DoctrineWriter;

/**
 * Class PersistEntities
 *
 * @package AppBundle\Service
 */
class ProductWriter extends DoctrineWriter
{
    /**
     * An array of products, that can't be inserted in Doctrine
     *
     * @var array
     */
    protected $errors;
    /**
     * Entity validator
     *
     * @var
     */
    protected $validator;
    /**
     * Is running in test mode
     *
     * @var bool
     */
    protected $test;
    /**
     * Array of correct products
     *
     * @var array
     */
    protected $correct;

    /**
     * Prepare writer
     *
     * @return void
     */
    public function prepare()
    {
        $this->errors = [];
        $this->correct = [];
    }

    /**
     * Set parameters of writer
     *
     * @param           $validator
     * @param bool      $test 
     *
     * @return void
     */
    public function setParameters($validator, $test)
    {
        $this->validator = $validator;
        $this->test = $test;
        $this->prepare();
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
     * Get array of correct products
     *
     * @return array
     */
    public function getCorrect()
    {
        return $this->correct;
    }

    /**
     * Add error to array
     *
     * @param array $line
     *
     * @return void
     */
    public function addError($line)
    {
        $this->errors[] = $line;
    }

    /**
     * Get array of errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Flush changes to database
     *
     * @param array $item 
     *
     * @return void
     */
    public function writeItem(array $item)
    {
        $item = $this->convertIncoding($item);
        $entity = $this->findOrCreateItem($item);

        $this->loadAssociationObjectsToEntity($item, $entity);
        $this->updateEntity($item, $entity);
        $errors = $this->validator->validate($entity);
        if (!$errors->has(0)) {
            $this->addCorrectProduct($entity);
        } else {
            $this->addError($item);
        }
    }

    /**
     * Flush changes to Doctrine, if not in test mode
     *
     * @return void
     */
    public function flush()
    {
        if (!$this->test) {
            $em = $this->entityManager;
            foreach ($this->correct as $product) {
                $em->persist($product);
            }
            $em->flush();
        }
    }

    /**
     * Change charset to UTF-8
     *
     * @param array $productData Array with product data
     *
     * @return array
     */
    public function convertIncoding($productData)
    {
        foreach ($productData as $key => $value) {
            if (is_string($value)) {
                $productData[$key] = mb_convert_encoding($value, 'UTF-8');
            }
        }
        return $productData;
    }
}
