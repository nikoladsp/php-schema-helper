<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

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

    public function test_cast_null()
    {
        $field = new DateTimeField('timestamp', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(null);
    }

    public function test_cast_empty()
    {
        $field = new DateTimeField('timestamp', true, true);

        $timestamp = $field->cast('    ');
        $this->assertInstanceOf(\DateTime::class, $timestamp);
    }

    public function test_cast_invalid_string()
    {
        $field = new DateTimeField('timestamp', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast('invalid timestamp');
    }

    public function test_cast_invalid_bool_true()
    {
        $field = new DateTimeField('timestamp', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(true);
    }

    public function test_cast_invalid_bool_false()
    {
        $field = new DateTimeField('timestamp', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(false);
    }

    public function test_cast_invalid_object()
    {
        $field = new DateTimeField('timestamp', true, true);

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(new stdClass());
    }

    public function test_cast_valid()
    {
        $field = new DateTimeField('timestamp', true, true);

        // Wednesday, August 25, 2021 6:35:12 AM
        $timestamp = $field->cast(1629873312);
        $this->assertInstanceOf(\DateTime::class, $timestamp);
        $this->assertEquals(2021, $timestamp->format('Y'));
        $this->assertEquals(8, $timestamp->format('m'));
        $this->assertEquals(25, $timestamp->format('d'));
        $this->assertEquals(6, $timestamp->format('H'));
        $this->assertEquals(35, $timestamp->format('i'));
        $this->assertEquals(12, $timestamp->format('s'));

        // Tuesday, June 4, 2019 2:46:27 AM
        $timestamp = $field->cast('  1559616387  ');
        $this->assertInstanceOf(\DateTime::class, $timestamp);
        $this->assertEquals(2019, $timestamp->format('Y'));
        $this->assertEquals(6, $timestamp->format('m'));
        $this->assertEquals(4, $timestamp->format('d'));
        $this->assertEquals(2, $timestamp->format('H'));
        $this->assertEquals(46, $timestamp->format('i'));
        $this->assertEquals(27, $timestamp->format('s'));

        // Wednesday, August 21, 2020 5:25:06 AM
        $timestamp = $field->cast('2020-08-21 05:25:06');
        $this->assertInstanceOf(\DateTime::class, $timestamp);
        $this->assertEquals(2020, $timestamp->format('Y'));
        $this->assertEquals(8, $timestamp->format('m'));
        $this->assertEquals(21, $timestamp->format('d'));
        $this->assertEquals(5, $timestamp->format('H'));
        $this->assertEquals(25, $timestamp->format('i'));
        $this->assertEquals(6, $timestamp->format('s'));
    }
}