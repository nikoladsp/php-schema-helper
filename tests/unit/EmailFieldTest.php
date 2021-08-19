<?php declare(strict_types=1);

require_once (__DIR__ . '/../../vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\EmailField;

class EmailFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_type()
    {
        $field = new EmailField('email');
        $this->assertEquals(new FieldType("EMAIL"), $field->type());
    }

    public function test_construct_no_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EmailField('');
    }

    public function test_nullable()
    {
        $field = new EmailField('email', false, true);
        $this->assertTrue($field->validate(null));

        $field = new EmailField('email', false, false);
        $this->assertFalse($field->validate(null));
    }

    public function test_valid_email()
    {
        $field = new EmailField('email', false,false);
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
        $field = new EmailField('email', false,false);
        $this->assertFalse($field->validate('mysite.ourearth.com'));
        $this->assertFalse($field->validate('mysite@.com.my'));
        $this->assertFalse($field->validate('@you.me.net'));
//        $this->assertFalse($field->validate('mysite123@gmail.b'));
        $this->assertFalse($field->validate('mysite@.org.org'));
        $this->assertFalse($field->validate('.mysite@mysite.org'));
        $this->assertFalse($field->validate('mysite()*@gmail.com'));
        $this->assertFalse($field->validate('mysite..1234@yahoo.com'));
    }
}
