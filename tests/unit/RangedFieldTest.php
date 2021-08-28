<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\RangedField;

class RangedFieldTest extends \PHPUnit\Framework\TestCase
{
    private function create_ranged_field_instance(string $name, $type, bool $required, bool $nullable, $min = null, $max = null, $default = null): RangedField
    {
        return new class($name, $type, $required, $nullable, $min, $max, $default) extends RangedField {
            public function __construct(string $name, $type, bool $required, bool $nullable, $min, $max, $default)
            {
                parent::__construct($name, $type, $required, $nullable, $min, $max, $default);
            }

            public function validate($value): bool
            {
                return false;
            }

            public function cast($value): bool
            {
                return false;
            }
        };
    }

    public function test_construct_no_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->create_ranged_field_instance('', FieldType::INTEGER, false, true);
    }

    public function test_construct_min_not_numeric()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->create_ranged_field_instance('', FieldType::INTEGER, false, true, 'a');
    }

    public function test_construct_max_not_numeric()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->create_ranged_field_instance('', FieldType::INTEGER, false, true, -1,'a');
    }

    public function test_construct_integer()
    {
        $field = $this->create_ranged_field_instance('id', FieldType::INTEGER, false, true);

        $this->assertEquals(new FieldType(FieldType::INTEGER), $field->type());
        $this->assertNull($field->min());
        $this->assertNull($field->max());
    }

    public function test_construct_integer_min()
    {
        $field = $this->create_ranged_field_instance('id', FieldType::INTEGER, false, true, -2);

        $this->assertEquals(-2, $field->min());
        $this->assertNull($field->max());
    }

    public function test_construct_integer_max()
    {
        $field = $this->create_ranged_field_instance('id', FieldType::INTEGER, false, true, null,-2);

        $this->assertNull($field->min());
        $this->assertEquals(-2, $field->max());
    }

    public function test_construct_integer_min_max()
    {
        $field = $this->create_ranged_field_instance('id', FieldType::INTEGER, false, true, -3,-2);

        $this->assertEquals(-3, $field->min());
        $this->assertEquals(-2, $field->max());
    }

    public function test_construct_min_greater_than_max()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->create_ranged_field_instance('id', FieldType::INTEGER, false, true, -1,-2);
    }
}
