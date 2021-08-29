<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\BoolField;

class BoolFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_construct_no_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        new BoolField('');
    }

    public function test_type()
    {
        $field = new BoolField('authenticated');
        $this->assertEquals(new FieldType("BOOL"), $field->type());
    }

    public function test_default()
    {
        $field = new BoolField('authenticated', true, false, true);
        $this->assertEquals(true, $field->default());

        $field = new BoolField('authenticated', true, false, false);
        $this->assertEquals(false, $field->default());
    }

    public function test_nullable()
    {
        $field = new BoolField('authenticated', false, true);
        $this->assertTrue($field->validate(null));

        $field = new BoolField('authenticated', false, false);
        $this->assertFalse($field->validate(null));
    }

    public function test_int()
    {
        $field = new BoolField('authenticated');

        $this->assertTrue($field->validate(-1));
        $this->assertTrue($field->validate(0));
        $this->assertTrue($field->validate(+1));
    }

    public function test_double()
    {
        $field = new BoolField('authenticated');

        $this->assertTrue($field->validate(-1.0));
        $this->assertTrue($field->validate(0.0));
        $this->assertTrue($field->validate(+1.0));
    }

    public function test_bool()
    {
        $field = new BoolField('authenticated');

        $this->assertTrue($field->validate(true));
        $this->assertTrue($field->validate(false));
    }

    public function test_validate_unsupported()
    {
        $field = new BoolField('authenticated');

        $this->assertFalse($field->validate(new stdClass()));
        $this->assertFalse($field->validate(new BoolField('authenticated')));
    }

    public function test_valid_string()
    {
        $field = new BoolField('authenticated');

        $this->assertTrue($field->validate('y'));
        $this->assertTrue($field->validate('Y'));
        $this->assertTrue($field->validate('yes'));
        $this->assertTrue($field->validate('YES'));
        $this->assertTrue($field->validate('true'));
        $this->assertTrue($field->validate('TRUE'));
        $this->assertTrue($field->validate('on'));
        $this->assertTrue($field->validate('ON'));
        $this->assertTrue($field->validate('1'));

        $this->assertTrue($field->validate('n'));
        $this->assertTrue($field->validate('N'));
        $this->assertTrue($field->validate('no'));
        $this->assertTrue($field->validate('NO'));
        $this->assertTrue($field->validate('false'));
        $this->assertTrue($field->validate('FALSE'));
        $this->assertTrue($field->validate('off'));
        $this->assertTrue($field->validate('OFF'));
        $this->assertTrue($field->validate('0'));
    }

    public function test_invalid_string()
    {
        $field = new BoolField('authenticated');

        $this->assertFalse($field->validate('yup'));
        $this->assertFalse($field->validate('yeah'));
        $this->assertFalse($field->validate('nope'));
        $this->assertFalse($field->validate('nah'));
        $this->assertFalse($field->validate('sure'));
        $this->assertFalse($field->validate('ok'));
        $this->assertFalse($field->validate('k'));
        $this->assertFalse($field->validate(''));
    }

    public function test_cast_null()
    {
        $field = new BoolField('authenticated', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(null);
    }

    public function test_cast_empty()
    {
        $field = new BoolField('authenticated', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast('    ');
    }

    public function test_cast_invalid()
    {
        $field = new BoolField('authenticated', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast('yep');
    }

    public function test_cast_true()
    {
        $field = new BoolField('authenticated', true, true);

        $this->assertEquals(true, $field->cast(-1));
        $this->assertEquals(true, $field->cast(1.02));
        $this->assertEquals(true, $field->cast(true));
        $this->assertEquals(true, $field->cast('y'));
        $this->assertEquals(true, $field->cast('Y'));
        $this->assertEquals(true, $field->cast('YeS'));
        $this->assertEquals(true, $field->cast('tRUe'));
        $this->assertEquals(true, $field->cast('oN'));
        $this->assertEquals(true, $field->cast('1'));
    }

    public function test_cast_false()
    {
        $field = new BoolField('authenticated', true, true);

        $this->assertEquals(false, $field->cast(0));
        $this->assertEquals(false, $field->cast(0.00));
        $this->assertEquals(false, $field->cast(false));
        $this->assertEquals(false, $field->cast('n'));
        $this->assertEquals(false, $field->cast('N'));
        $this->assertEquals(false, $field->cast('nO'));
        $this->assertEquals(false, $field->cast('fAlSe'));
        $this->assertEquals(false, $field->cast('OfF'));
        $this->assertEquals(false, $field->cast('0'));
    }
}
