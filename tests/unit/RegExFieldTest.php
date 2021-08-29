<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\RegExField;

class RegExFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_type()
    {
        $field = new RegExField('id', '*');
        $this->assertEquals(new FieldType("REGEX"), $field->type());
    }

    public function test_validate_unsupported()
    {
        $field = new RegExField('id', '*');

        $this->assertFalse($field->validate(new stdClass()));
        $this->assertFalse($field->validate(new RegExField('id', '*')));
    }

    public function test_pattern()
    {
        $field = new RegExField('id', '*');
        $this->assertEquals('*', $field->pattern());
    }

    public function test_construct_no_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        new RegExField('', '*');
    }

    public function test_construct_no_pattern()
    {
        $this->expectException(\InvalidArgumentException::class);
        new RegExField('email', '');
    }

    public function test_nullable()
    {
        $field = new RegExField('id', '*', false,true);
        $this->assertTrue($field->validate(null));

        $field = new RegExField('id', '*', false,false);
        $this->assertFalse($field->validate(null));
    }

    public function test_no_match()
    {
        $field = new RegExField('id', '/Ain/', false,true);

        $this->assertFalse($field->validate('cmd'));
        $this->assertFalse($field->validate('Raindrops'));
    }

    public function test_match()
    {
        $field = new RegExField('id', '/Ain/i', false,true);

        $this->assertTrue($field->validate('Raindrops'));
        $this->assertTrue($field->validate('main()'));
    }

    public function test_cast_null()
    {
        $field = new RegExField('id', '/Ain/', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(null);
    }

    public function test_cast_empty()
    {
        $field = new RegExField('id', '/Ain/', true,true);

        $this->assertEquals('', $field->cast('    '));
    }

    public function test_cast_invalid_bool_true()
    {
        $field = new RegExField('id', '/Ain/', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(true);
    }

    public function test_cast_invalid_bool_false()
    {
        $field = new RegExField('id', '/Ain/', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(false);
    }

    public function test_cast_int()
    {
        $field = new RegExField('id', '/Ain/', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(+1);
    }

    public function test_cast_float()
    {
        $field = new RegExField('id', '/Ain/', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(2.056);
    }

    public function test_cast_invalid_object()
    {
        $field = new RegExField('id', '/Ain/', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(new stdClass());
    }

    public function test_cast_valid()
    {
        $field = new RegExField('id', '/Ain/', true,true);

        $this->assertEquals('myinvalidregex', $field->cast(' myinvalidregex '));
        $this->assertEquals('-7', $field->cast(' -7 '));
    }
}
