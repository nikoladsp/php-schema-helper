<?php declare(strict_types=1);

require_once (__DIR__ . '/../../vendor/autoload.php');

use \SchemaHelper\FieldType;

class FieldTypeTest extends \PHPUnit\Framework\TestCase
{
    private function type_values(): array
    {
        $constants = (new \ReflectionClass(FieldType::class))->getConstants();
        return array_unique(array_values($constants));
    }

    public function test_null_value()
    {
        $this->expectException(\InvalidArgumentException::class);
        new FieldType(null);
    }

    public function test_wrong_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        new FieldType(new stdClass());
    }

    public function test_types()
    {
        static $expected = array('INTEGER', 'STRING', 'DOUBLE', 'REGEX', 'EMAIL', 'BOOL', 'DATETIME', 'SCHEMA');
        $this->assertTrue(!array_diff($expected, FieldType::types()));
    }

    public function test_construct_integer()
    {
        foreach (array(1, 'INTEGER') as $value) {
            $fieldType = new FieldType($value);

            $this->assertEquals(1, $fieldType->value());
            $this->assertEquals('INTEGER', $fieldType->type());
        }
    }

    public function test_construct_string()
    {
        foreach (array(5, 'STRING') as $value) {
            $fieldType = new FieldType(5);

            $this->assertEquals(5, $fieldType->value());
            $this->assertEquals('STRING', $fieldType->type());
        }
    }

    public function test_construct_double()
    {
        foreach (array(10, 'DOUBLE') as $value) {
            $fieldType = new FieldType(10);

            $this->assertEquals(10, $fieldType->value());
            $this->assertEquals('DOUBLE', $fieldType->type());
        }
    }

    public function test_construct_regex()
    {
        foreach (array(15, 'REGEX') as $value) {
            $fieldType = new FieldType(15);

            $this->assertEquals(15, $fieldType->value());
            $this->assertEquals('REGEX', $fieldType->type());
        }
    }

    public function test_construct_email()
    {
        foreach (array(20, 'EMAIL') as $value) {
            $fieldType = new FieldType(20);

            $this->assertEquals(20, $fieldType->value());
            $this->assertEquals('EMAIL', $fieldType->type());
        }
    }

    public function test_construct_bool()
    {
        foreach (array(25, 'BOOL') as $value) {
            $fieldType = new FieldType(25);

            $this->assertEquals(25, $fieldType->value());
            $this->assertEquals('BOOL', $fieldType->type());
        }
    }

    public function test_construct_datetime()
    {
        foreach (array(30, 'DATETIME') as $value) {
            $fieldType = new FieldType(30);

            $this->assertEquals(30, $fieldType->value());
            $this->assertEquals('DATETIME', $fieldType->type());
        }
    }

    public function test_construct_schema()
    {
        foreach (array(35, 'SCHEMA') as $value) {
            $fieldType = new FieldType(35);

            $this->assertEquals(35, $fieldType->value());
            $this->assertEquals('SCHEMA', $fieldType->type());
        }
    }

    public function test_below_min()
    {
        $values = $this->type_values();
        $min = min($values) - 1;

        $this->expectException(\InvalidArgumentException::class);
        new FieldType($min);
    }

    public function test_above_max()
    {
        $values = $this->type_values();
        $max = max($values) + 1;

        $this->expectException(\InvalidArgumentException::class);
        new FieldType($max);
    }
}
