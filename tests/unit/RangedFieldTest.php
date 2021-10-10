<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\RangedField;

class RangedFieldTest extends \PHPUnit\Framework\TestCase
{
    private function create_ranged_field_instance($type, string $name='', bool $required=false, bool $nullable=true, $min = null, $max = null, $default = null): RangedField
    {
        return new class($type, $name, $required, $nullable, $min, $max, $default) extends RangedField {
            public function __construct($type, string $name='', bool $required=false, bool $nullable=true, $min, $max, $default)
            {
                parent::__construct($type, $name, $required, $nullable, $min, $max, $default);
            }

            public function validate($value): bool
            {
                return false;
            }

            public function dump($value): bool
            {
                return false;
            }
        };
    }

    public function test_default()
    {
        $field = $this->create_ranged_field_instance(FieldType::INTEGER, 'id', false, true, null,-2);
        $this->assertNull($field->default());

        $value = 7;
        $field = $this->create_ranged_field_instance(FieldType::INTEGER, 'id', false, true, null,-2, $value);
        $this->assertEquals($value, $field->default());
    }

//    public function test_construct_min_not_numeric()
//    {
//        $this->expectException(\InvalidArgumentException::class);
//        $this->create_ranged_field_instance(FieldType::INTEGER, '', false, true, 'a');
//    }
//
//    public function test_construct_max_not_numeric()
//    {
//        $this->expectException(\InvalidArgumentException::class);
//        $this->create_ranged_field_instance(FieldType::INTEGER, '', false, true, -1,'a');
//    }

    public function test_construct_integer()
    {
        $field = $this->create_ranged_field_instance(FieldType::INTEGER, 'id', false, true);

        $this->assertEquals(new FieldType(FieldType::INTEGER), $field->type());
        $this->assertNull($field->min());
        $this->assertNull($field->max());
    }

    public function test_construct_integer_min()
    {
        $field = $this->create_ranged_field_instance(FieldType::INTEGER, 'id', false, true, -2);

        $this->assertEquals(-2, $field->min());
        $this->assertNull($field->max());
    }

    public function test_construct_integer_max()
    {
        $field = $this->create_ranged_field_instance(FieldType::INTEGER, 'id', false, true, null,-2);

        $this->assertNull($field->min());
        $this->assertEquals(-2, $field->max());
    }

    public function test_construct_integer_min_max()
    {
        $field = $this->create_ranged_field_instance(FieldType::INTEGER, 'id', false, true, -3,-2);

        $this->assertEquals(-3, $field->min());
        $this->assertEquals(-2, $field->max());
    }

    public function test_construct_min_greater_than_max()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->create_ranged_field_instance(FieldType::INTEGER, 'id', false, true, -1,-2);
    }
}
