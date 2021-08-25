<?php declare(strict_types=1);

namespace SchemaHelper;

final class IntField extends Field
{
    private ?int $min;
    private ?int $max;

    public function __construct(string $name, bool $required=false, bool $nullable=true, ?int $min = null, ?int $max = null, $default=null)
    {
        if (is_int($min) && is_int($max) && $min > $max)
            throw new \InvalidArgumentException('min greater than max');

        parent::__construct($name, FieldType::INTEGER, $required, $nullable, $default);

        $this->min = $min;
        $this->max = $max;
    }

    public function min(): ?int
    {
        return $this->min;
    }

    public function max(): ?int
    {
        return $this->max;
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

        if (is_int($this->min) && is_int($this->max))
            return ($this->min <= $val) && ($val <= $this->max);
        else if (is_int($this->min))
            return $this->min <= $val;
        else if (is_int($this->max))
            return $val <= $this->max;
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
