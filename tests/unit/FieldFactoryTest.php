<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\StringField;
use \SchemaHelper\RegExField;
use \SchemaHelper\FieldFactory;
use \SchemaHelper\EmailField;
use \SchemaHelper\BoolField;
use \SchemaHelper\IntField;
use \SchemaHelper\DoubleField;
use \SchemaHelper\DateTimeField;

class FieldFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function test_create_no_params()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array());
    }

    public function test_create_default_name()
    {
        $field = FieldFactory::create(array(), 'username');
        $this->assertTrue($field instanceof StringField);
    }

    public function test_required()
    {
        $field = FieldFactory::create(array(), 'username');
        $this->assertFalse($field->required());

        $field = FieldFactory::create(array('required' => true), 'username');
        $this->assertTrue($field->required());

        $field = FieldFactory::create(array('required' => false), 'username');
        $this->assertFalse($field->required());
    }

    public function test_nullable()
    {
        $field = FieldFactory::create(array(), 'username');
        $this->assertTrue($field->nullable());

        $field = FieldFactory::create(array('nullable' => true), 'username');
        $this->assertTrue($field->nullable());

        $field = FieldFactory::create(array('nullable' => false), 'username');
        $this->assertFalse($field->nullable());
    }

    public function test_wrong_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array('name' => 'unsupported', 'type' => 'WillNeverExists'));
    }

    public function test_string()
    {
        $field = FieldFactory::create(array('name' => 'username'));
        $this->assertTrue($field instanceof StringField);
    }

    public function test_string_min_set()
    {
        $field = FieldFactory::create(array('min' => 2), 'username');

        $this->assertEquals(2, $field->min());
        $this->assertNull($field->max());
    }

    public function test_string_max_set()
    {
        $field = FieldFactory::create(array('max' => 3), 'username');

        $this->assertNull($field->min());
        $this->assertEquals(3, $field->max());
    }

    public function test_string_min_max_set()
    {
        $field = FieldFactory::create(array('min' => 2, 'max' => 3), 'username');

        $this->assertEquals(2, $field->min());
        $this->assertEquals(3, $field->max());
    }

    public function test_regex()
    {
        $field = FieldFactory::create(array('name' => 'username', 'type' => 'regex', 'pattern' => '*'));
        $this->assertTrue($field instanceof RegExField);
    }

    public function test_regex_min_set()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array('type' => 'regex', 'min' => 2), 'active');
    }

    public function test_regex_max_set()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array('type' => 'regex', 'max' => 2), 'active');
    }

    public function test_regex_min_max_set()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array('type' => 'regex', 'min' => 1, 'max' => 2), 'active');
    }

    public function test_email()
    {
        $field = FieldFactory::create(array('name' => 'email', 'type' => 'email'));
        $this->assertTrue($field instanceof EmailField);
    }

    public function test_email_min_set()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array('type' => 'email', 'min' => 2), 'active');
    }

    public function test_email_max_set()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array('type' => 'email', 'max' => 2), 'active');
    }

    public function test_email_min_max_set()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array('type' => 'email', 'min' => 1, 'max' => 2), 'active');
    }

    public function test_bool()
    {
        $field = FieldFactory::create(array('name' => 'active', 'type' => 'bool'));
        $this->assertTrue($field instanceof BoolField);
    }

    public function test_bool_min_set()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array('type' => 'bool', 'min' => 2), 'active');
    }

    public function test_bool_max_set()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array('type' => 'bool', 'max' => 2), 'active');
    }

    public function test_bool_min_max_set()
    {
        $this->expectException(\InvalidArgumentException::class);
        FieldFactory::create(array('type' => 'bool', 'min' => 1, 'max' => 2), 'active');
    }

    public function test_int()
    {
        $field = FieldFactory::create(array('name' => 'id', 'type' => 'integer'));
        $this->assertTrue($field instanceof IntField);
    }

    public function test_double()
    {
        $field = FieldFactory::create(array('name' => 'id', 'type' => 'double'));
        $this->assertTrue($field instanceof DoubleField);
    }

    public function test_datetime()
    {
        $field = FieldFactory::create(array('name' => 'timestamp', 'type' => 'datetime'));
        $this->assertTrue($field instanceof DateTimeField);
    }
}
