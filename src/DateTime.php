<?php declare(strict_types=1);

namespace SchemaHelper;

final class DateTime extends \DateTime implements \JsonSerializable
{
    private string $format = 'c';

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    public function jsonSerialize()
    {
        return $this->format($this->format);
    }
}
