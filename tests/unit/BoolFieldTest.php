<?php declare(strict_types=1);

require_once (__DIR__ . '/../../vendor/autoload.php');

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
        $field = new BoolField('id');
        $this->assertEquals(new FieldType("BOOL"), $field->type());
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
}
