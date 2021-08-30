<?php declare(strict_types=1);

use \SchemaHelper\DateTime;

class DateTimeTest extends \PHPUnit\Framework\TestCase
{
    public function test_default_format()
    {
        $timestamp = new DateTime();
        $this->assertEquals('c', $timestamp->getFormat());
    }

    public function test_format()
    {
        $format = 'Y-m-d H:i:s';
        $timestamp = new DateTime();
        $timestamp->setFormat($format);

        $this->assertEquals($format, $timestamp->getFormat());
    }

    public function test_default_json_serialize()
    {
        $timestamp = new DateTime('2021-08-30 08:24:15');
        $this->assertEquals('2021-08-30T08:24:15+00:00', $timestamp->jsonSerialize());
    }
}
