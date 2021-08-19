<?php declare(strict_types=1);

namespace SchemaHelper;

final class StringField extends Field
{
    private ?int $min;
    private ?int $max;

    public function __construct(string $name, bool $required = false, bool $nullable = true, ?int $min = null, ?int $max = null)
    {
        if (is_int($min) && is_int($max))
        {
            if ($min < 0 || $max < 0)
                throw new \InvalidArgumentException('min and max must be non-negative numbers');

            if ($min > $max)
                throw new \InvalidArgumentException('min greater than max');
        } else if (is_int($min) && $min < 1) {
            throw new \InvalidArgumentException('min must be non-negative numbers');
        } else if (is_int($max) && $max < 1) {
            throw new \InvalidArgumentException('max must be non-negative numbers');
        }

        parent::__construct($name, FieldType::STRING, $required, $nullable);

        $this->min = $min;
        $this->max = $max;
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();
        else if (!is_string($value))
            return false;

        $len = strlen($value);

        if (is_int($this->min) && is_int($this->max))
            return ($this->min <= $len) && ($len <= $this->max);
        else if (is_int($this->min))
            return $this->min <= $len;
        else if (is_int($this->max))
            return $len <= $this->max;
        else
            return true;
    }
}
