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
        foreach (array(2, 'STRING') as $value) {
            $fieldType = new FieldType(2);

            $this->assertEquals(2, $fieldType->value());
            $this->assertEquals('STRING', $fieldType->type());
        }
    }

    public function test_construct_double()
    {
        foreach (array(3, 'DOUBLE') as $value) {
            $fieldType = new FieldType(3);

            $this->assertEquals(3, $fieldType->value());
            $this->assertEquals('DOUBLE', $fieldType->type());
        }
    }

    public function test_construct_regex()
    {
        foreach (array(4, 'REGEX') as $value) {
            $fieldType = new FieldType(4);

            $this->assertEquals(4, $fieldType->value());
            $this->assertEquals('REGEX', $fieldType->type());
        }
    }

    public function test_construct_email()
    {
        foreach (array(5, 'EMAIL') as $value) {
            $fieldType = new FieldType(5);

            $this->assertEquals(5, $fieldType->value());
            $this->assertEquals('EMAIL', $fieldType->type());
        }
    }

    public function test_construct_bool()
    {
        foreach (array(6, 'BOOL') as $value) {
            $fieldType = new FieldType(6);

            $this->assertEquals(6, $fieldType->value());
            $this->assertEquals('BOOL', $fieldType->type());
        }
    }

    public function test_construct_datetime()
    {
        foreach (array(7, 'DATETIME') as $value) {
            $fieldType = new FieldType(7);

            $this->assertEquals(7, $fieldType->value());
            $this->assertEquals('DATETIME', $fieldType->type());
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
