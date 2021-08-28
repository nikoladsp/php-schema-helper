<?php declare(strict_types=1);

namespace SchemaHelper;

final class DoubleField extends RangedField
{
    public function __construct(string $name, bool $required=false, bool $nullable=true, ?float $min = null, ?float $max = null, $default=null)
    {
        parent::__construct($name, FieldType::DOUBLE, $required, $nullable, $min, $max, $default);
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();

        try {
            $val = is_float($value) ? $value : $this->numeric($value);
            if (!is_float($val) && !is_int($val))
                return false;
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        $min = $this->min();
        $max = $this->max();

        if (is_float($min) && is_float($max))
            return ($min <= $val) && ($val <= $max);
        else if (is_float($min))
            return $min <= $val;
        else if (is_float($max))
            return $val <= $max;
        else
            return true;
    }

    public function cast($value): float
    {
        if (is_null($value))
            throw new \InvalidArgumentException('Invalid value');

        if (is_numeric($value))
            return (float)$value;
        else if (is_string($value)) {
            $value = trim($value);
            if (is_numeric($value))
                return (float)$value;
        }

        throw new \InvalidArgumentException('Invalid value');
    }
}
