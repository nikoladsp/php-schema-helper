<?php declare(strict_types=1);

namespace SchemaHelper;

final class IntField extends RangedField
{
    public function __construct(string $name, bool $required=false, bool $nullable=true, ?int $min = null, ?int $max = null, $default=null)
    {
        parent::__construct($name, FieldType::INTEGER, $required, $nullable, $min, $max, $default);
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();

        try {
            $val = is_int($value) ? $value : $this->numeric($value);
            if (!is_int($val))
                return false;
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        $min = $this->min();
        $max = $this->max();

        if (is_int($min) && is_int($max))
            return ($min <= $val) && ($val <= $max);
        else if (is_int($min))
            return $min <= $val;
        else if (is_int($max))
            return $val <= $max;
        else
            return true;
    }

    public function cast($value): int
    {
        if (is_null($value))
            throw new \InvalidArgumentException('Invalid value');

        if (is_int($value))
            return $value;
        else if (is_string($value)) {
            $value = trim($value);
            $value = is_int($value) ? $value : $this->numeric($value);

            if (is_int($value))
                return $value;
        }

        throw new \InvalidArgumentException('Invalid value');
    }
}
