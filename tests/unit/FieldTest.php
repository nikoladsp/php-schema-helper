<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\Field;

class FieldTest extends \PHPUnit\Framework\TestCase
{
    private function create_field_instance(string $name, $type, bool $required, bool $nullable): Field
    {
        return new class($name, $type, $required, $nullable) extends Field {
            public function __construct(string $name, $type, bool $required, bool $nullable)
            {
                parent::__construct($name, $type, $required, $nullable);
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
        $this->create_field_instance('', FieldType::INTEGER, false, true);
    }

    public function test_construct_integer()
    {
        $field = $this->create_field_instance('id', FieldType::INTEGER, true, true);
        $this->assertEquals('id', $field->name());
        $this->assertEquals(new FieldType(FieldType::INTEGER), $field->type());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(true, $field->nullable());
    }

    public function test_construct_string()
    {
        $field = $this->create_field_instance('nick', FieldType::STRING, false, true);
        $this->assertEquals('nick', $field->name());
        $this->assertEquals(new FieldType(FieldType::STRING), $field->type());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(true, $field->nullable());
    }

    public function test_construct_email()
    {
        $field = $this->create_field_instance('email', 'EMAIL', true, false);
        $this->assertEquals('email', $field->name());
        $this->assertEquals(new FieldType(FieldType::EMAIL), $field->type());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(false, $field->nullable());
    }

    public function test_construct_double()
    {
        $field = $this->create_field_instance('amount', 10, false, false);
        $this->assertEquals('amount', $field->name());
        $this->assertEquals(new FieldType(FieldType::DOUBLE), $field->type());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(false, $field->nullable());
    }
}
