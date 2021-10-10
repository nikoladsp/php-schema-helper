<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\EmailField;

class EmailFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_type()
    {
        $field = new EmailField('email');
        $this->assertEquals(new FieldType("EMAIL"), $field->type());
    }

    public function test_default()
    {
        $field = new EmailField('email');
        $this->assertNull($field->default());

        $value = 'test.me@gmail.com';
        $field = new EmailField('email', false, true, $value);
        $this->assertEquals($value, $field->default());
    }

    public function test_nullable()
    {
        $field = new EmailField('email', false, true);
        $this->assertTrue($field->validate(null));

        $field = new EmailField('email', false, false, 'test.me@gmail.com');
        $this->assertFalse($field->validate(null));
    }

    public function test_validate_unsupported()
    {
        $field = new EmailField('email');

        $this->assertFalse($field->validate(new stdClass()));
        $this->assertFalse($field->validate(new EmailField('email')));
    }

    public function test_valid_email()
    {
        $field = new EmailField('email', false,false, 'test.me@gmail.com');
        $this->assertTrue($field->validate('email@example.com'));
        $this->assertTrue($field->validate('firstname.lastname@example.com'));
        $this->assertTrue($field->validate('email@subdomain.example.com'));
        $this->assertTrue($field->validate('firstname+lastname@example.com'));
//        $this->assertTrue($field->validate('email@123.123.123.123'));
        $this->assertTrue($field->validate('email@[123.123.123.123]'));
        $this->assertTrue($field->validate('"email"@example.com'));
        $this->assertTrue($field->validate('1234567890@example.com'));
        $this->assertTrue($field->validate('email@example-one.com'));
        $this->assertTrue($field->validate('_______@example.com'));
        $this->assertTrue($field->validate('email@example.name'));
        $this->assertTrue($field->validate('email@example.museum'));
        $this->assertTrue($field->validate('email@example.co.jp'));
        $this->assertTrue($field->validate('firstname-lastname@example.com'));
        $this->assertTrue($field->validate('much."more\ unusual"@example.com'));
        $this->assertTrue($field->validate('very.unusual."@".unusual.com@example.com'));
//        $this->assertTrue($field->validate('very."(),:;<>[]".VERY."very@\\ "very".unusual@strange.example.com'));
    }

    public function test_invalid_email()
    {
        $field = new EmailField('email', false,false, 'test.me@gmail.com');
        $this->assertFalse($field->validate('mysite.ourearth.com'));
        $this->assertFalse($field->validate('mysite@.com.my'));
        $this->assertFalse($field->validate('@you.me.net'));
//        $this->assertFalse($field->validate('mysite123@gmail.b'));
        $this->assertFalse($field->validate('mysite@.org.org'));
        $this->assertFalse($field->validate('.mysite@mysite.org'));
        $this->assertFalse($field->validate('mysite()*@gmail.com'));
        $this->assertFalse($field->validate('mysite..1234@yahoo.com'));
    }

    public function test_dump_null()
    {
        $field = new EmailField('email', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(null);
    }

    public function test_dump_empty()
    {
        $field = new EmailField('email', true,true);

        $this->assertEquals('', $field->dump('    '));
    }

    public function test_dump_invalid_bool_true()
    {
        $field = new EmailField('email', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(true);
    }

    public function test_dump_invalid_bool_false()
    {
        $field = new EmailField('email', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(false);
    }

    public function test_dump_int()
    {
        $field = new EmailField('email', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(+1);
    }

    public function test_dump_float()
    {
        $field = new EmailField('email', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(2.056);
    }

    public function test_dump_invalid_object()
    {
        $field = new EmailField('email', true,true);

        $this->expectException(\InvalidArgumentException::class);
        $field->dump(new stdClass());
    }

    public function test_dump_valid()
    {
        $field = new EmailField('email', true,true);

        $this->assertEquals('myinvalidemail', $field->dump(' myinvalidemail '));
        $this->assertEquals('-7', $field->dump(' -7 '));
        $this->assertEquals('name.surname@gmail.com', $field->dump(' name.surname@gmail.com '));
    }
}
