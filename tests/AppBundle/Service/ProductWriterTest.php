<?php
/**
 * Created by PhpStorm.
 * User: s2.gerasimovich
 * Date: 1/9/17
 * Time: 6:53 PM
 */

namespace Tests\AppBundle\Service;


use AppBundle\Service\CsvValidator;
use AppBundle\Writer\ProductWriter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductWriterTest extends KernelTestCase
{
    private $container;
    private $csvValidator;
    private $validator;
    private $headers;
    private $writer;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->headers = $this->container->getParameter('product.headers');
        $this->validator = $this->container->get('validator');
        $this->csvValidator = new CsvValidator($this->headers);
        $em = $this->container->get('doctrine.orm.entity_manager');
        $this->writer = new ProductWriter($em, 'AppBundle:Product');
        $this->writer->setParameters($this->validator, true);
    }


    /**
     * Test creation of new product
     *
     * @covers EntityFilter::writeItem()
     *
     * @return void
     */
    public function testProductCreation()
    {

        $productData = ['productCode' => 'P9999',
            'productName' => 'PS4',
            'productDesc' => 'Best Gaming Ever',
            'price' => '120.0',
            'stock' => '40',
            'discontinued' => 'yes'];
        $this->writer->writeItem($productData);
        $product = $this->writer->getCorrect()['P9999'];

        $this->assertEquals(
            $productData['productCode'], $product->getProductCode()
        );
        $this->assertEquals(
            $productData['productName'], $product->getProductName()
        );
        $this->assertEquals(
            $productData['productDesc'], $product->getProductDesc()
        );
        $this->assertEquals($productData['price'], $product->getPrice());
        $this->assertEquals($productData['stock'], $product->getStock());
        $this->assertNotNull($product->getDiscontinued());
    }

    /**
     * Test failing to add according to violations
     *
     * @covers EntityFilter::writeItem()
     *
     * @return void
     */
    public function testIncorrectProduct()
    {
        $productData = ['productCode' => 'P9999',
            'productName' => 'PS4',
            'productDesc' => 'Best Gaming Ever',
            'price' => '0.0',
            'stock' => '0',
            'discontinued' => 'yes'];
        $this->writer->writeItem($productData);
        $product = $this->writer->getErrors()[0];

        $this->assertEquals(
            $productData['productCode'], $product['productCode']
        );
        $this->assertEquals(
            $productData['productName'], $product['productName']
        );
        $this->assertEquals(
            $productData['productDesc'], $product['productDesc']
        );
        $this->assertEquals($productData['price'], $product['price']);
        $this->assertEquals($productData['stock'], $product['stock']);
        $this->assertNotNull($product['discontinued']);
    }
}
