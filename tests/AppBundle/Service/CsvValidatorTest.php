<?php
/**
 * Created by PhpStorm.
 * User: s2.gerasimovich
 * Date: 1/10/17
 * Time: 2:30 PM
 */

namespace Tests\AppBundle\Service;


use AppBundle\Service\CsvValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Class CsvValidatorTest
 * @package Tests\AppBundle\Service
 */
class CsvValidatorTest extends KernelTestCase
{

    /**
     * Validator for *.csv file
     *
     * @var CsvValidator
     */
    private $validator;

    /**
     * Array of *.csv proper headers
     *
     * @var array
     */
    private $headers;

    /**
     * Set up the kernel test
     *
     * @return void
     */
    public function setUp()
    {
        self::bootKernel();
        $this->headers = self::$kernel
            ->getContainer()->getParameter('product.headers');
        $this->validator = new CsvValidator($this->headers);
    }

    /**
     * Test validating correct *.csv file
     *
     * @covers CsvValidator::validate()
     *
     * @return void
     */
    public function testValidatingCorrectCsv()
    {
        $reader = $this->validator->validate('app/Resources/tests/correct.csv');

        $this->assertNotNull($reader);
        $this->assertEquals($this->validator->isValid(), true);
        $this->assertEquals($this->validator->getMessage(), '');
    }

    /**
     * Test validating invalid *.csv
     *
     * @covers CsvValidator::validate()
     *
     * @return void
     */
    public function testValidatingIncorrectCsv()
    {
        $reader = $this->validator->validate('app/Resources/tests/invalid.csv');

        $this->assertEquals($reader, null);
        $this->assertEquals($this->validator->isValid(), false);
        $this->assertEquals(
            $this->validator->getMessage(), '<error>Incorrect file. '.
                'It should contain headers such: '.PHP_EOL.
                implode(', ', $this->headers).'. '.PHP_EOL.
                'All fields should be separated by comma</error>'
        );
    }

    /**
     * Test validating file with invalid extension
     *
     * @covers CsvValidator::validate()
     *
     * @return void
     */
    public function testValidatingInvalidFile()
    {
        $reader = $this->validator->validate('app/Resources/tests/invalid.txt');

        $this->assertEquals($reader, null);
        $this->assertEquals($this->validator->isValid(), false);
        $this->assertEquals(
            $this->validator->getMessage(), '<error>Incorrect file. '.
            'It should have an *.csv extension</error>'
        );
    }
}
