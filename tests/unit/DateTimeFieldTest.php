<?php declare(strict_types=1);

require_once (__DIR__ . '/../../vendor/autoload.php');

use \SchemaHelper\FieldType;
use \SchemaHelper\DateTimeField;

class DateTimeFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_construct_no_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DateTimeField('');
    }

    public function test_type()
    {
        $field = new DateTimeField('timestamp');
        $this->assertEquals(new FieldType('DATETIME'), $field->type());
    }

    public function test_nullable()
    {
        $field = new DateTimeField('timestamp', false, true);
        $this->assertTrue($field->validate(null));

        $field = new DateTimeField('timestamp', false, false);
        $this->assertFalse($field->validate(null));
    }

    public function test_min_greater_than_max()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DateTimeField('timestamp', false, true, new \DateTime('2021-01-01T15:03:01.012345Z'), new \DateTime('2021-01-01T15:03:00.012345Z'));
    }

    public function test_construct_required_nullable()
    {
        $field = new DateTimeField('timestamp', true, true);
        $this->assertEquals('timestamp', $field->name());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(true, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_construct_not_required_nullable()
    {
        $field = new DateTimeField('timestamp', false, true);
        $this->assertEquals('timestamp', $field->name());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(true, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_construct_required_not_nullable()
    {
        $field = new DateTimeField('timestamp', true, false);
        $this->assertEquals('timestamp', $field->name());
        $this->assertEquals(true, $field->required());
        $this->assertEquals(false, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_construct_not_required_not_nullable()
    {
        $field = new DateTimeField('timestamp', false, false);
        $this->assertEquals('timestamp', $field->name());
        $this->assertEquals(false, $field->required());
        $this->assertEquals(false, $field->nullable());
        $this->assertEquals(null, $field->min());
        $this->assertEquals(null, $field->max());
    }

    public function test_wrong_string_value()
    {
        $field = new DateTimeField('timestamp');
        $this->assertFalse($field->validate('-'));
//        $this->assertFalse($field->validate('  '));
        $this->assertFalse($field->validate('abc'));
        $this->assertFalse($field->validate('0xAA'));
    }

    public function test_value_no_min_no_max()
    {
        $field = new DateTimeField('timestamp');
        $this->assertTrue($field->validate(new \DateTime('2021-01-01T15:03:01.012345Z')));
        $this->assertTrue($field->validate('2021-01-01T15:03:01.012345Z'));
    }

    public function test_value_min_no_max()
    {
        $field = new DateTimeField('timestamp',false,true, new \DateTime('2021-01-01T15:03:01.012345Z'), null);
        $this->assertFalse($field->validate(new \DateTime('2021-01-01T15:03:01.012344Z')));
        $this->assertTrue($field->validate(new \DateTime('2021-01-01T15:03:01.012345Z')));
        $this->assertTrue($field->validate(new \DateTime('2021-01-01T15:03:01.012346Z')));

        $this->assertFalse($field->validate('2021-01-01T15:03:01.012344Z'));
        $this->assertTrue($field->validate('2021-01-01T15:03:01.012345Z'));
        $this->assertTrue($field->validate('2021-01-01T15:03:01.012346Z'));
    }

    public function test_value_no_min_max()
    {
        $field = new DateTimeField('timestamp',false,true, null, new \DateTime('2021-01-01T15:03:01.012345Z'));
        $this->assertTrue($field->validate(new \DateTime('2021-01-01T15:03:01.012345Z')));
        $this->assertTrue($field->validate(new \DateTime('2021-01-01T15:03:01.012344Z')));
        $this->assertFalse($field->validate(new \DateTime('2021-01-01T15:03:01.012346Z')));

        $this->assertTrue($field->validate('2021-01-01T15:03:01.012345Z'));
        $this->assertTrue($field->validate('2021-01-01T15:03:01.012344Z'));
        $this->assertFalse($field->validate('2021-01-01T15:03:01.012346Z'));
    }

    public function test_value_min_max()
    {
        $field = new DateTimeField('timestamp', false,true, new \DateTime('2021-01-01T15:03:01.012341Z'), new \DateTime('2021-01-01T15:03:01.012345Z'));
        $this->assertFalse($field->validate(new \DateTime('2021-01-01T15:03:01.012340Z')));
        $this->assertTrue($field->validate(new \DateTime('2021-01-01T15:03:01.012341Z')));
        $this->assertTrue($field->validate(new \DateTime('2021-01-01T15:03:01.012345Z')));
        $this->assertFalse($field->validate(new \DateTime('2021-01-01T15:03:01.012346Z')));

        $this->assertFalse($field->validate('2021-01-01T15:03:01.012340Z'));
        $this->assertTrue($field->validate('2021-01-01T15:03:01.012341Z'));
        $this->assertTrue($field->validate('2021-01-01T15:03:01.012345Z'));
        $this->assertFalse($field->validate('2021-01-01T15:03:01.012346Z'));
    }
}