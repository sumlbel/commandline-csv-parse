<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\AlterEntities;
use AppBundle\Service\CsvParser;
use Doctrine\Common\Persistence\ObjectManager;

class AlterEntitiesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test adding product if 'Product Code' already exists
     *
     * @covers AlterEntities::updateProductData()
     *
     * @return void
     */
    public function testAddingProductIfExist()
    {
        $entityManager = $this
            ->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $alterEntities = new AlterEntities($entityManager);
        $csvParser = new CsvParser();

        $oldData = array(
            'Product Code' => 'P9999',
            'Product Name' => 'PS3',
            'Product Description' => 'Best Gaming Ever',
            'Cost in GBP' => '70.0',
            'Stock' => '35',
            'Discontinued' => 'yes',
        );
        $newData = array(
            'Product Code' => 'P9999',
            'Product Name' => 'PS4',
            'Product Description' => 'Best Gaming Ever',
            'Cost in GBP' => '120.0',
            'Stock' => '40',
            'Discontinued' => 'yes',
        );

        $oldProduct = $csvParser->setNewProduct($oldData);
        $newProduct = $csvParser->setNewProduct($newData);

        $product = $alterEntities->updateProductData($oldProduct, $newProduct);

        $this->assertEquals(
            $newProduct->getStrProductCode(), $product->getStrProductCode()
        );
        $this->assertEquals(
            $newProduct->getStrProductName(), $product->getStrProductName()
        );
        $this->assertEquals(
            $newProduct->getStrProductDesc(), $product->getStrProductDesc()
        );
        $this->assertEquals($newProduct->getPrice(), $product->getPrice());
        $this->assertEquals($newProduct->getStock(), $product->getStock());
        $this->assertNotNull($product->getDtmDiscontinued());
        $this->assertNotNull($product->getDtmAdded());
        $this->assertNotNull($product->getStmTimeStamp());
    }
}

