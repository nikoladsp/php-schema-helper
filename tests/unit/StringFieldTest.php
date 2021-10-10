<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\StringField;

class StringFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_type()
    {
        $field = new StringField('username');
        $this->assertEquals(new FieldType('STRING'), $field->type());
    }

    public function test_default()
    {
        $field = new StringField('username', false, true);
        $this->assertNull($field->default());

        $value = 'testuser';
        $field = new StringField('username', false, true, 1, 3, $value);
        $this->assertEquals($value, $field->default());
    }

    public function test_min_greater_than_max()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StringField('username', false, false,5, 4);
    }

    public function test_min_negative()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StringField('username', false, false,-1);
    }

    public function test_max_negative()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StringField('username', false, false, null,-1);
    }

    public function test_min_max_negative()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StringField('username', false, false, -2, -1);
    }

    public function test_nullable()
    {
        $field = new StringField('username', false, true);
        $this->assertTrue($field->validate(null));

        $field = new StringField('username', false,false, null, null, 'testme');
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
        $field = new StringField('name', true, false, null, null, 'test');

        $this->assertEquals('name', $field->name());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(false, $field->nullable());
    }

    public function test_construct_not_required_not_nullable()
    {
        $field = new StringField('name', false, false, null, null, 'teststr');

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
        $field = new StringField('name', false, false, 2, null, 'test');

        $this->assertFalse($field->validate(''));
        $this->assertFalse($field->validate('a'));
        $this->assertTrue($field->validate('ab'));
        $this->assertTrue($field->validate('abc'));
    }

    public function test_string_max_length()
    {
        $field = new StringField('name', false, false, null, 2, 'test');

        $this->assertTrue($field->validate(''));
        $this->assertTrue($field->validate('a'));
        $this->assertTrue($field->validate('ab'));
        $this->assertFalse($field->validate('abc'));
    }

    public function test_string_min_max_length()
    {
        $field = new StringField('name', false, false,1, 2, 'test');

        $this->assertFalse($field->validate(''));
        $this->assertTrue($field->validate('a'));
        $this->assertTrue($field->validate('ab'));
        $this->assertFalse($field->validate('abc'));
    }

    public function test_dump_null()
    {
        $field = new StringField('name', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(null);
    }

    public function test_dump_empty()
    {
        $field = new StringField('name', true, true);

        $this->assertEquals('', $field->dump('    '));
    }

    public function test_dump_invalid_bool_true()
    {
        $field = new StringField('name', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(true);
    }

    public function test_dump_invalid_bool_false()
    {
        $field = new StringField('name', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(false);
    }

    public function test_dump_int()
    {
        $field = new StringField('name', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(+1);
    }

    public function test_dump_float()
    {
        $field = new StringField('name', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(2.056);
    }

    public function test_dump_invalid_object()
    {
        $field = new StringField('name', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(new stdClass());
    }

    public function test_dump_valid()
    {
        $field = new StringField('name', true, true);

        $this->assertEquals('mystring', $field->dump(' mystring '));
        $this->assertEquals('-7', $field->dump(' -7 '));
        $this->assertEquals('+3.14159', $field->dump(' +3.14159 '));
    }
}
