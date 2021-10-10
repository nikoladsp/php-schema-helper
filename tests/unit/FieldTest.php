<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\Field;

class FieldTest extends \PHPUnit\Framework\TestCase
{
    private function create_field_instance($type, string $name='', bool $required=false, bool $nullable=true): Field
    {
        return new class($type, $name, $required, $nullable) extends Field {
            public function __construct($type, string $name='', bool $required=false, bool $nullable=true)
            {
                parent::__construct($type, $name, $required, $nullable);
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

    public function test_construct_no_name()
    {
        $field = $this->create_field_instance(FieldType::INTEGER);
        $this->assertFalse(empty($field->name()));
    }

    public function test_construct_integer()
    {
        $field = $this->create_field_instance(FieldType::INTEGER, 'id', true);
        $this->assertEquals('id', $field->name());
        $this->assertEquals(new FieldType(FieldType::INTEGER), $field->type());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(true, $field->nullable());
    }

    public function test_construct_string()
    {
        $field = $this->create_field_instance(FieldType::STRING, 'nick');
        $this->assertEquals('nick', $field->name());
        $this->assertEquals(new FieldType(FieldType::STRING), $field->type());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(true, $field->nullable());
    }

    public function test_construct_email()
    {
        $field = $this->create_field_instance('EMAIL', 'email', true, false);
        $this->assertEquals('email', $field->name());
        $this->assertEquals(new FieldType(FieldType::EMAIL), $field->type());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(false, $field->nullable());
    }

    public function test_construct_double()
    {
        $field = $this->create_field_instance('DOUBLE', 'amount', false, false);
        $this->assertEquals('amount', $field->name());
        $this->assertEquals(new FieldType(FieldType::DOUBLE), $field->type());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(false, $field->nullable());
    }
}
