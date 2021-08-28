<?php declare(strict_types=1);

namespace SchemaHelper;

final class StringField extends RangedField
{
    public function __construct(string $name, bool $required = false, bool $nullable = true, ?int $min = null, ?int $max = null, $default=null)
    {
        if (is_int($min) && is_int($max))
        {
            if ($min < 0 || $max < 0)
                throw new \InvalidArgumentException('min and max must be non-negative numbers');
        } else if (is_int($min) && $min < 1) {
            throw new \InvalidArgumentException('min must be non-negative numbers');
        } else if (is_int($max) && $max < 1) {
            throw new \InvalidArgumentException('max must be non-negative numbers');
        }

        parent::__construct($name, FieldType::STRING, $required, $nullable, $min, $max, $default);
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();
        else if (!is_string($value))
            return false;

        $len = strlen($value);
        $min = $this->min();
        $max = $this->max();

        if (is_int($min) && is_int($max))
            return ($min <= $len) && ($len <= $max);
        else if (is_int($min))
            return $min <= $len;
        else if (is_int($max))
            return $len <= $max;
        else
            return true;
    }

    public function cast($value): string
    {
        if (is_null($value))
            throw new \InvalidArgumentException('Invalid value');

        if (is_string($value))
            return trim($value);

        throw new \InvalidArgumentException('Invalid value');
    }
}
