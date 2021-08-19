<?php declare(strict_types=1);

require_once (__DIR__ . '/../../vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\IntField;

class IntFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_construct_no_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        new IntField('');
    }

    public function test_type()
    {
        $field = new IntField('id');
        $this->assertEquals(new FieldType("INTEGER"), $field->type());
    }

    public function test_min_greater_than_max()
    {
        $this->expectException(\InvalidArgumentException::class);
        new IntField('id', false,false,5,4);
    }

    public function test_double()
    {
        $field = new IntField('id');
        $this->assertFalse($field->validate(-1.0));

        $this->assertFalse($field->validate('-1.0'));
    }

    public function test_nullable()
    {
        $field = new IntField('id', false, true);
        $this->assertTrue($field->validate(null));

        $field = new IntField('id', false,false);
        $this->assertFalse($field->validate(null));
    }

    public function test_construct_required_nullable()
    {
        $field = new IntField('id', true, true);
        $this->assertEquals('id', $field->name());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(true, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_construct_not_required_nullable()
    {
        $field = new IntField('id', false, true);
        $this->assertEquals('id', $field->name());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(true, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_construct_required_not_nullable()
    {
        $field = new IntField('id', true, false);
        $this->assertEquals('id', $field->name());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(false, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_construct_not_required_not_nullable()
    {
        $field = new IntField('id', false, false);
        $this->assertEquals('id', $field->name());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(false, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_wrong_string_value()
    {
        $field = new IntField('id');
        $this->assertFalse($field->validate(''));
        $this->assertFalse($field->validate('  '));
        $this->assertFalse($field->validate('0a'));
        $this->assertFalse($field->validate('0xAA'));
    }

    public function test_value_no_min_no_max()
    {
        $field = new IntField('id');
        $this->assertTrue($field->validate(-1));
        $this->assertTrue($field->validate(0));
        $this->assertTrue($field->validate(+1));

        $this->assertTrue($field->validate('-1'));
        $this->assertTrue($field->validate('0'));
        $this->assertTrue($field->validate('+1'));
    }

    public function test_value_min_no_max()
    {
        $field = new IntField('id', false, false,-1);
        $this->assertFalse($field->validate(-2));
        $this->assertTrue($field->validate(-1));
        $this->assertTrue($field->validate(0));
        $this->assertTrue($field->validate(+1));

        $this->assertFalse($field->validate('-2'));
        $this->assertTrue($field->validate('-1'));
        $this->assertTrue($field->validate('0'));
        $this->assertTrue($field->validate('+1'));
    }

    public function test_value_no_min_max()
    {
        $field = new IntField('id', false, false, null,-1);
        $this->assertTrue($field->validate(-2));
        $this->assertTrue($field->validate(-1));
        $this->assertFalse($field->validate(0));
        $this->assertFalse($field->validate(+1));

        $this->assertTrue($field->validate('-2'));
        $this->assertTrue($field->validate('-1'));
        $this->assertFalse($field->validate('0'));
        $this->assertFalse($field->validate('+1'));
    }

    public function test_value_min_max()
    {
        $field = new IntField('id', false, false,-2, -1);
        $this->assertFalse($field->validate(-3));
        $this->assertTrue($field->validate(-2));
        $this->assertTrue($field->validate(-1));
        $this->assertFalse($field->validate(0));
        $this->assertFalse($field->validate(+1));

        $this->assertFalse($field->validate('-3'));
        $this->assertTrue($field->validate('-2'));
        $this->assertTrue($field->validate('-1'));
        $this->assertFalse($field->validate('0'));
        $this->assertFalse($field->validate('+1'));
    }
}