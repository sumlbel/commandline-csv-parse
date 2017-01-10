<?php
/**
 * Created by PhpStorm.
 * User: s2.gerasimovich
 * Date: 1/10/17
 * Time: 2:30 PM
 */

namespace Tests\AppBundle\Service;


use AppBundle\Service\Validator;


class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test validating correct *.csv file
     *
     * @covers Validator::validate()
     *
     * @return void
     */
    public function testValidatingCorrectCsv()
    {
        $validator = new Validator();
        $reader = $validator->validate('app/Resources/tests/correct.csv');

        $this->assertNotNull($reader);
        $this->assertEquals($validator->isValid(), true);
        $this->assertEquals($validator->getMessage(), '');
    }

    /**
     * Test validating invalid *.csv
     *
     * @covers Validator::validate()
     *
     * @return void
     */
    public function testValidatingIncorrectCsv()
    {
        $validator = new Validator();
        $reader = $validator->validate('app/Resources/tests/invalid.csv');

        $this->assertEquals($reader, null);
        $this->assertEquals($validator->isValid(), false);
        $this->assertEquals(
            $validator->getMessage(), '<error>Incorrect file. '.
            'It should contain headers such: '.PHP_EOL.
            'Product Code, Product Name, Product Description, '.
            'Stock, Cost in GBP, Discontinued. '.PHP_EOL.
            'All fields should be separated by comma</error>'
        );
    }

    /**
     * Test validating file with invalid extension
     *
     * @covers Validator::validate()
     *
     * @return void
     */
    public function testValidatingInvalidFile()
    {
        $validator = new Validator();
        $reader = $validator->validate('app/Resources/tests/invalid.txt');

        $this->assertEquals($reader, null);
        $this->assertEquals($validator->isValid(), false);
        $this->assertEquals(
            $validator->getMessage(), '<error>Incorrect file. '.
            'It should have an *.csv extension</error>'
        );
    }
}
