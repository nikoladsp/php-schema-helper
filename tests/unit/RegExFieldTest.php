<?php declare(strict_types=1);

require_once (__DIR__ . '/../../vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\RegExField;

class RegExFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_type()
    {
        $field = new RegExField('id', '*');
        $this->assertEquals(new FieldType("REGEX"), $field->type());
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
}
