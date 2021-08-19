<?php declare(strict_types=1);

require_once (__DIR__ . '/../../vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\StringField;

class StringFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_construct_no_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StringField('');
    }

    public function test_type()
    {
        $field = new StringField('id');
        $this->assertEquals(new FieldType("STRING"), $field->type());
    }

    public function test_min_greater_than_max()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StringField('id', false, false,5, 4);
    }

    public function test_min_negative()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StringField('id', false, false,-1);
    }

    public function test_max_negative()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StringField('id', false, false, null,-1);
    }

    public function test_min_max_negative()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StringField('id', false, false, -2, -1);
    }

    public function test_nullable()
    {
        $field = new StringField('id', false, true);
        $this->assertTrue($field->validate(null));

        $field = new StringField('id', false,false);
        $this->assertFalse($field->validate(null));
    }

    public function test_construct_required_nullable()
    {
        $field = new StringField('name', true, true);
        $this->assertEquals('name', $field->name());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(true, $field->nullable());
    }

    public function test_construct_not_required_nullable()
    {
        $field = new StringField('name', false, true);
        $this->assertEquals('name', $field->name());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(true, $field->nullable());
    }

    public function test_construct_required_not_nullable()
    {
        $field = new StringField('name', true, false);
        $this->assertEquals('name', $field->name());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(false, $field->nullable());
    }

    public function test_construct_not_required_not_nullable()
    {
        $field = new StringField('name', false, false);
        $this->assertEquals('name', $field->name());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(false, $field->nullable());
    }

    public function test_numeric()
    {
        $field = new StringField('name');
        $this->assertFalse($field->validate(-1));
        $this->assertFalse($field->validate(-1.0));
    }

    public function test_string()
    {
        $field = new StringField('name');
        $this->assertTrue($field->validate(''));
        $this->assertTrue($field->validate('-1'));
        $this->assertTrue($field->validate('-1.0'));
    }

    public function test_string_min_length()
    {
        $field = new StringField('name', false, false, 2);
        $this->assertFalse($field->validate(''));
        $this->assertFalse($field->validate('a'));
        $this->assertTrue($field->validate('ab'));
        $this->assertTrue($field->validate('abc'));
    }

    public function test_string_max_length()
    {
        $field = new StringField('name', false, false, null, 2);
        $this->assertTrue($field->validate(''));
        $this->assertTrue($field->validate('a'));
        $this->assertTrue($field->validate('ab'));
        $this->assertFalse($field->validate('abc'));
    }

    public function test_string_min_max_length()
    {
        $field = new StringField('name', false, false,1, 2);
        $this->assertFalse($field->validate(''));
        $this->assertTrue($field->validate('a'));
        $this->assertTrue($field->validate('ab'));
        $this->assertFalse($field->validate('abc'));
    }
}
