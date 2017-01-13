<?php
/**
 * Created by PhpStorm.
 * User: s2.gerasimovich
 * Date: 1/9/17
 * Time: 6:53 PM
 */

namespace Tests\AppBundle\Service;


use AppBundle\Service\EntityFilter;
use AppBundle\Service\CsvValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CsvParserTest extends KernelTestCase
{
    private $container;
    private $csvValidator;
    private $validator;
    private $headers;
    private $parser;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->headers = $this->container->getParameter('product.headers');
        $this->validator = $this->container->get('validator');
        $this->csvValidator = new CsvValidator($this->headers);
        $this->parser = new EntityFilter($this->headers, $this->validator);
    }
    /**
     * Test parsing correct csv file
     *
     * @covers EntityFilter::parse()
     *
     * @return void
     */
    public function testParsingCorrectCsv()
    {
        $reader = $this->csvValidator->validate('app/Resources/tests/correct.csv');

        $products = $this->parser->parse($reader);
        $product = $products->getCorrect()['P8888'];

        $this->assertEquals($product->getProductCode(), 'P8888');
        $this->assertEquals($product->getProductName(), 'Speakers');
        $this->assertEquals($product->getProductDesc(), '800w');
        $this->assertEquals($product->getPrice(), '99.99');
        $this->assertEquals($product->getStock(), '20');
        $this->assertNotNull($product->getDiscontinued());
        $this->assertEquals($products->getSkipping(), []);
        $this->assertEquals($products->getCountProcessed(), '1');
    }


    /**
     * Test creation of new product
     *
     * @covers EntityFilter::setNewProduct()
     *
     * @return void
     */
    public function testProductCreation()
    {

        $productData = [$this->headers['code'] => 'P9999',
            $this->headers['name'] => 'PS4',
            $this->headers['description'] => 'Best Gaming Ever',
            $this->headers['price'] => '120.0',
            $this->headers['stock'] => '40',
            $this->headers['discontinued'] => 'yes'];
        $product = $this->parser->setNewProduct($productData);

        $this->assertEquals(
            $productData['Product Code'], $product->getProductCode()
        );
        $this->assertEquals(
            $productData['Product Name'], $product->getProductName()
        );
        $this->assertEquals(
            $productData['Product Description'], $product->getProductDesc()
        );
        $this->assertEquals($productData['Cost in GBP'], $product->getPrice());
        $this->assertEquals($productData['Stock'], $product->getStock());
        $this->assertNotNull($product->getDiscontinued());
    }
}
