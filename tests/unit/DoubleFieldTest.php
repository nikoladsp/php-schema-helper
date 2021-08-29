<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\DoubleField;

class DoubleFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_construct_no_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DoubleField('');
    }

    public function test_type()
    {
        $field = new DoubleField('cost');
        $this->assertEquals(new FieldType("DOUBLE"), $field->type());
    }

    public function test_nullable()
    {
        $field = new DoubleField('cost', false, true);
        $this->assertTrue($field->validate(null));

        $field = new DoubleField('cost', false, false);
        $this->assertFalse($field->validate(null));
    }

    public function test_min_greater_than_max()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DoubleField('cost', false, true, 5, 4);
    }

    public function test_int()
    {
        $field = new DoubleField('cost');

        $this->assertTrue($field->validate(-1));
        $this->assertTrue($field->validate('-1'));
    }

    public function test_validate_unsupported()
    {
        $field = new DoubleField('cost');

        $this->assertFalse($field->validate(new stdClass()));
        $this->assertFalse($field->validate(new DoubleField('cost')));
    }

    public function test_construct_required_nullable()
    {
        $field = new DoubleField('cost', true, true);

        $this->assertEquals('cost', $field->name());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(true, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_construct_not_required_nullable()
    {
        $field = new DoubleField('cost', false, true);

        $this->assertEquals('cost', $field->name());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(true, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_construct_required_not_nullable()
    {
        $field = new DoubleField('cost', true, false);

        $this->assertEquals('cost', $field->name());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(false, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_construct_not_required_not_nullable()
    {
        $field = new DoubleField('cost', false, false);

        $this->assertEquals('cost', $field->name());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(false, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_wrong_string_value()
    {
        $field = new DoubleField('cost');

        $this->assertFalse($field->validate(''));
        $this->assertFalse($field->validate('  '));
        $this->assertFalse($field->validate('0.0a'));
        $this->assertFalse($field->validate('0xAA'));
    }

    public function test_value_no_min_no_max()
    {
        $field = new DoubleField('cost');

        $this->assertTrue($field->validate(-1.0));
        $this->assertTrue($field->validate(0.0));
        $this->assertTrue($field->validate(+1.0));

        $this->assertTrue($field->validate('-1.0'));
        $this->assertTrue($field->validate('0.0'));
        $this->assertTrue($field->validate('+1.0'));
    }

    public function test_value_min_no_max()
    {
        $field = new DoubleField('cost',false,true,-1.0, null);

        $this->assertFalse($field->validate(-2.0));
        $this->assertTrue($field->validate(-1.0));
        $this->assertTrue($field->validate(0.0));
        $this->assertTrue($field->validate(+1.0));

        $this->assertFalse($field->validate('-2.0'));
        $this->assertTrue($field->validate('-1.0'));
        $this->assertTrue($field->validate('0.0'));
        $this->assertTrue($field->validate('+1.0'));
    }

    public function test_value_no_min_max()
    {
        $field = new DoubleField('cost',false,true, null, -1.0);

        $this->assertTrue($field->validate(-2.0));
        $this->assertTrue($field->validate(-1.0));
        $this->assertFalse($field->validate(0.0));
        $this->assertFalse($field->validate(+1.0));

        $this->assertTrue($field->validate('-2.0'));
        $this->assertTrue($field->validate('-1.0'));
        $this->assertFalse($field->validate('0.0'));
        $this->assertFalse($field->validate('+1.0'));
    }

    public function test_value_min_max()
    {
        $field = new DoubleField('cost', false,true, -2.0, -1.0);

        $this->assertFalse($field->validate(-3.0));
        $this->assertTrue($field->validate(-2.0));
        $this->assertTrue($field->validate(-1.0));
        $this->assertFalse($field->validate(0.0));
        $this->assertFalse($field->validate(+1.0));

        $this->assertFalse($field->validate('-3.0'));
        $this->assertTrue($field->validate('-2.0'));
        $this->assertTrue($field->validate('-1.0'));
        $this->assertFalse($field->validate('0.0'));
        $this->assertFalse($field->validate('+1.0'));
    }

    public function test_cast_null()
    {
        $field = new DoubleField('cost', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(null);
    }

    public function test_cast_empty()
    {
        $field = new DoubleField('cost', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast('    ');
    }

    public function test_cast_invalid_string()
    {
        $field = new DoubleField('cost', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast('invalid value');
    }

    public function test_cast_invalid_bool_true()
    {
        $field = new DoubleField('cost', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(true);
    }

    public function test_cast_invalid_bool_false()
    {
        $field = new DoubleField('cost', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(false);
    }

    public function test_cast_invalid_object()
    {
        $field = new DoubleField('cost', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(new stdClass());
    }

    public function test_cast_valid()
    {
        $field = new DoubleField('cost', true,true);

        $this->assertEquals(-1.02, $field->cast(-1.02));
        $this->assertEquals(-3, $field->cast(-3));
        $this->assertEquals(0.0, $field->cast(0));
        $this->assertEquals(-2.07, $field->cast('  -2.07  '));
        $this->assertEquals(6, $field->cast('  +6  '));
        $this->assertEquals(0, $field->cast('  0.0  '));
    }
}